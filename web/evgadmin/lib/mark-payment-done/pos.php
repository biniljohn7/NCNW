<?php
$txnOwnerStripeInfo = $this->getMember($txn->member, 'stripeCusId');
$plans = $pixdb->fetchAssoc('membership_plans', [], '*', 'id');
$products = $pixdb->fetchAssoc('products', [], '*', 'id');

$receipts = [];
$memberIds = [];
foreach ($txnItems as $item) {
    $pald = json_decode($item->details ?? '');
    if (is_object($pald) && isset($pald->memberId)) {
        $memberIds[] = $pald->memberId;
    }
}

$memberIds = array_merge($memberIds, [$txn->member]);
$memberIds = array_unique($memberIds);
$memberData = [];
if ($memberIds) {
    $memberData = $this->getMembers($memberIds, 'id, firstName, lastName, email, memberId');
}

foreach ($txnItems as $item) {
    if ($item->type == 'membership') {
        $pald = json_decode($item->details ?? '');
        if (!is_object($pald)) {
            $pald = (object)[];
        }

        $mmbShpExp = null;
        $instllmntPhase = null;
        $payStatus = 'pending';

        // if installment
        if ($pald->installment) {
            $instllmntPhase = 1;
            $mmbShpExp = $this->calcInstlmntExpiry(date('Y-m-d'), $pald->installment, $instllmntPhase);
        } else {
            $payStatus = 'completed';
        }

        if (!$mmbShpExp && $pald->validity == '1 year') {
            $mmbShpExp = $this->calcOneYrExpiry();
        }
        if ($pald->memberId) {
            $giftedBy = null;
            $isInstallment = $pald->installment && $pald->installment != '' ? $pald->installment : null;
            $giftReceiver = '';
            if ($giftedBy) {
                $receiver = $memberData[$pald->memberId] ?? null;
                if ($receiver) {
                    $giftReceiver = "$receiver->firstName $receiver->lastName";
                }
            }
            $paidAmount = $pald->installment ? $pald->ttlCharge / $pald->installment : $pald->ttlCharge;
            $receiptItms = [
                [
                    'item' => ($pald->planName ?? 'Unknown') .
                        ($isInstallment ? "- Installment 1 of $isInstallment" : '') .
                        ($giftReceiver ? " (gift to $giftReceiver)" : ''),
                    'amount' => ($mmbShpExp && !$pald->installment ? '(valid till ' . date('F j, Y', strtotime($mmbShpExp)) . ' )' : '') . '<b>' .
                        dollar($paidAmount) . '</b>'
                ]
            ];

            $pinStatus = null;
            if ($pald->ttlCharge >= 1000) {
                if (!$pald->installment && $payStatus === 'completed') {
                    $pinStatus = 'pending';
                } elseif ($pald->installment) {
                    $paidSoFar = ($pald->ttlCharge / $pald->installment) * $instllmntPhase;
                    if ($paidSoFar >= 1000) {
                        $pinStatus = 'pending';
                    }
                }
            }

            $dbData = [
                'member' => $pald->memberId,
                'giftedBy' => $giftedBy,
                'planId' => $pald->planId ?? null,
                'planName' => $pald->planName ?? 'Unknown',
                'created' => $time ?: $datetime,
                'expiry' => $mmbShpExp,
                'amount' => $pald->ttlCharge,
                'amtCalc' => json_encode([
                    'natDue' => $pald->natDue ?? 0,
                    'natLtFee' => $pald->natLtFee ?? 0,
                    'natCapFee' => $pald->natCapFee ?? 0,
                    'natReinFee' => $pald->natReinFee ?? 0,
                    'locDue' => $pald->locDue ?? 0,
                    'ttlLocChaptChrg' => $pald->ttlLocChaptChrg ?? 0,
                    'ttlNatChaptChrg' => $pald->ttlNatChaptChrg ?? 0,
                    'ttlCharge' => $pald->ttlCharge ?? 0,
                    'chapDon' => $pald->chapDon ?? 0,
                    'natDon' => $pald->natDon ?? 0,
                    'validity' => $pald->validity ?? 'Unknown',
                ]),
                'enabled' => $giftedBy ? 'N' : 'Y',
                'payStatus' => $payStatus,
                'installment' => $pald->installment && $pald->installment != '' ? $pald->installment : null,
                'instllmntPhase' => $instllmntPhase,
                'section' => intval($pald->sectionId) ?? null,
                'affiliate' => intval($pald->affiliateId) ?? null,
                'pinStatus' => $pinStatus
            ];

            $ins = $pixdb->insert(
                'memberships',
                $dbData
            );

            if ($ins) {
                if (
                    $txnOwnerStripeInfo &&
                    $dbData['installment'] &&
                    $dbData['installment'] > 0 &&
                    $dbData['installment'] > $dbData['instllmntPhase']
                ) {
                    $installments = $dbData['installment'];
                    $phase = $dbData['instllmntPhase'];
                    $subscrpCancelDate = $this->calcInstlmntExpiry(
                        date('Y-m-d'),
                        $installments,
                        $phase
                    );
                    $billingInterval = $installments > 0 ? (12 / $installments) : 0;
                    $amtPerInstllmnt = $dbData['amount'] / $installments;

                    if ($subscrpCancelDate && $billingInterval) {
                        $subscription = loadModule('stripe')->createInstllmntSubscription(
                            $txnOwnerStripeInfo->stripeCusId,
                            $dbData['planName'] . " - $installments payments",
                            $amtPerInstllmnt,
                            $mmbShpExp,
                            $billingInterval,
                            $subscrpCancelDate,
                            $txn->member,
                            $ins
                        );
                    }
                }
                if ($pald->planId && isset($plans[$pald->planId])) {
                    $selPlan = $plans[$pald->planId];
                    $addons = json_decode($selPlan->addons ?? '');
                    if ($addons && is_array($addons)) {
                        foreach ($addons as $idx => $ad) {
                            if (isset($products[$ad])) {
                                $expiry = $products[$ad]->validity == 'fiscal-year' ? $this->calcOneYrExpiry() : null;
                                $pixdb->insert(
                                    'paid_benefits',
                                    [
                                        'members' => $pald->memberId,
                                        'payCategoryId' => $products[$ad]->id,
                                        'title' => $products[$ad]->name,
                                        'expiry' => $expiry,
                                        'benefitFrom' => $date
                                    ]
                                );
                            }
                        }
                    }
                }
            }
            if (!isset($receipts[$pald->memberId])) {
                $receipts[$pald->memberId] = [];
            }
            $receipts[$pald->memberId][] = $receiptItms;
            if ($giftedBy) {
                if (!isset($receipts[$giftedBy])) {
                    $receipts[$giftedBy] = [];
                }
                $receipts[$giftedBy][] = $receiptItms;
            }
        }
    } elseif ($item->type == 'product') {
        $pInfo = json_decode($item->details ?? '');
        if (is_object($pInfo) && $pInfo->id && isset($products[$pInfo->id])) {
            $pdt = $products[$pInfo->id];
            $expiry = $pdt->validity == 'fiscal-year' ? $this->calcOneYrExpiry() : null;
            $pixdb->insert('paid_benefits', [
                'members' => $item->benefitTo,
                'title' => $pdt->name,
                'payCategoryId' => $pdt->id,
                'expiry' => $expiry,
                'benefitFrom' => $date
            ]);
            if (!isset($receipts[$txn->member])) {
                $receipts[$txn->member] = [];
            }
            $receipts[$txn->member][] = [
                [
                    'item' => $pdt->name,
                    'amount' => '<b>' . dollar($pdt->amount) . '</b>'
                ]
            ];
        }
    }
}
$txnMarked = true;
$txnOwner = isset($memberData[$txn->member]) ? $memberData[$txn->member] : null;

if ($txnOwner) {
    // notify admin
    $this->postNotification(
        'admin',
        $txn->member,
        'new-membership',
        'New Membership',
        "$txnOwner->firstName $txnOwner->lastName paid for membership(s)"
    );

    foreach ($receipts as $member => $re) {
        $memberInfo = isset($memberData[$member]) ? $memberData[$member] : null;
        if ($memberInfo) {
            $isOwnTxn = $member == $txn->member;
            $mainCnt = '';
            $txnDtls = '';

            if ($isOwnTxn) {
                $mainCnt .= 'We have received your
                <div style="padding: 8px 0px;">
                    <span
                        style="
                        color: #5b2166;
                        font-size: 2em;
                        line-height: normal;
                        padding: 8px 0px;
                        "
                        >' . (dollar($txn->amount) ?? 0) . '</span
                    >
                </div>';
                $txnDtls .= '<div
                    style="
                        display: inline-block;
                        min-width: 210px;
                        margin-bottom: 20px;
                        width: 49%;
                    "
                    >
                    <div style="font-size: 0.9em; color: rgb(119, 119, 119)">
                        Transaction ID
                    </div>
                    <div style="font-weight: bold">
                        ' . $txn->txnid . '
                    </div>
                </div>
                <div
                    style="
                        display: inline-block;
                        min-width: 210px;
                        margin-bottom: 20px;
                        width: 49%;
                    "
                    >
                    <div style="font-size: 0.9em; color: rgb(119, 119, 119)">
                        Transaction Date
                    </div>
                    <div style="font-weight: bold">
                        ' . (date('d M Y', strtotime($txn->date))) . '
                    </div>
                </div>';
            } else {
                $mainCnt .= "<b>$txnOwner->firstName $txnOwner->lastName</b> purchased memberships as gifts for $memberInfo->firstName $memberInfo->lastName.";
                $txnDtls .= '<div
                    style="
                        display: inline-block;
                        min-width: 210px;
                        margin-bottom: 20px;
                        width: 49%;
                    "
                    >
                    <div style="font-size: 0.9em; color: rgb(119, 119, 119)">
                        Purchased on
                    </div>
                    <div style="font-weight: bold">
                        ' . (date('d M Y', strtotime($txn->date))) . '
                    </div>
                </div>';
            }

            $tr = '';
            foreach ($re as $item) {
                foreach ($item as $itm) {
                    $tr .= '<tr>
                        <td style="padding: 5px 0px">' . $itm['item'] . '</td>
                        <td style="padding: 5px 0px; text-align: right">
                        ' . $itm['amount'] . '
                        </td>
                    </tr>';
                }
            }

            $tnxListDtls = '<table style="margin: auto; font-size: 0.8em; text-transform: capitalize">
                <tbody>
                    <tr style="font-size: 1.2em; color: rgb(187, 186, 186)">
                        <th style="width: 50%; text-align: left; padding: 5px 0px">
                        Items
                        </th>
                        <th style="width: 50%; text-align: right; padding: 5px 0px">Amount</th>
                    </tr>
                    ' . $tr . '
                </tbody>
            </table>';

            $pix->e_mail(
                $memberInfo->email,
                'Payment Reciept',
                'payment-reciept',
                [
                    'MN_CONTENT' => '<div
                            style="
                                font-size: 1.5em;
                                padding-top: 0.9em;
                                line-height: normal;
                            "
                            >
                            Hi, ' . "$memberInfo->firstName $memberInfo->lastName" . '
                        </div>' . $mainCnt,
                    'TXN_DTLS' => $txnDtls,
                    'TXN_LIST_DTLS' => $tnxListDtls,
                ]
            );
        }
    }
}
