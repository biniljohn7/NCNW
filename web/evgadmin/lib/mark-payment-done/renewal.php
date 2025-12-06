<?php
$mmbrshpIds = [];
$paidBenefitIds = [];
$memberships = [];
$paidBenefits = [];
$receipts = [];
$mmbrIds = [$txn->member];
$mmberInfo = [];

foreach ($txnItems as $item) {
    $pald = json_decode($item->details);

    if (is_object($pald) && $pald->target && $pald->targetId) {
        if ($pald->target == 'membership') {
            $mmbrshpIds[] = $pald->targetId;
        } elseif ($pald->target == 'paid_benefits') {
            $paidBenefitIds[] = $pald->targetId;
        }
    }
}

if ($mmbrshpIds) {
    $memberships = $pixdb->fetchAssoc(
        'memberships',
        ['id' => $mmbrshpIds],
        'id,
            member,
            giftedBy,
            planId,
            planName,
            created,
            amount,
            expiry,
            payStatus,
            installment,
            instllmntPhase',
        'id'
    );
    $mmbrIds = array_merge($mmbrIds, collectObjData($memberships, 'member'));
}

if ($paidBenefitIds) {
    $paidBenefits = $pixdb->fetchAssoc(
        'paid_benefits',
        ['id' => $paidBenefitIds],
        '*',
        'id'
    )->data;
    $mmbrIds = array_merge($mmbrIds, collectObjData($memberships, 'members'));
}

$mmbrIds = array_unique($mmbrIds);
if ($mmbrIds) {
    $mmberInfo = $this->getMemberInfo($mmbrIds, ['firstName', 'lastName', 'email', 'memberId'], [], true);
}

$planIds = collectObjData($memberships, 'planId');
$pdtIds = collectObjData($paidBenefits, 'payCategoryId');
$mshpPlans = [];
$products = [];

if ($planIds) {
    $mshpPlans = $pixdb->fetchAssoc(
        'membership_plans',
        ['id' => $planIds],
        'id,
            active,
            duration',
        'id'
    );
}
if ($pdtIds) {
    $products = $pixdb->fetchAssoc(
        'products',
        ['id' => $pdtIds],
        'id,
            enabled,
            amount,
            validity',
        'id'
    );
}

foreach ($memberships as $mshp) {
    $member = isset($mmberInfo[$mshp->member]) ? $mmberInfo[$mshp->member] : null;
    if ($member) {
        if (isset($mshpPlans[$mshp->planId]) && $mshpPlans[$mshp->planId]->active == 'Y') {
            $update = [];
            $giftedBy = $txn->member == $mshp->giftedBy;

            //installment or renew
            if ($mshp->instllmntPhase < $mshp->installment) {
                $mmbShpExp = $mshp->expiry;
                $payStatus = $mshp->payStatus;
                $instllmntPhase = min($mshp->installment, $mshp->instllmntPhase + 1);

                if ($mshp->installment == $instllmntPhase) {
                    $validity = $mshpPlans[$mshp->planId]->duration;
                    if ($validity == '1 year') {
                        $mmbShpExp = $this->calcOneYrExpiry();
                    } else {
                        $mmbShpExp = null;
                    }
                    $payStatus = 'completed';
                } else {
                    $mmbShpExp = $this->calcInstlmntExpiry($mshp->created, $mshp->installment, $instllmntPhase);
                    $payStatus = 'pending';
                }

                $update = [
                    'expiry' => $mmbShpExp,
                    'payStatus' => $payStatus,
                    'instllmntPhase' => $instllmntPhase
                ];
                $amount = $mshp->amount / $mshp->installment;
                $receiptItms = [
                    'item' => ($mshp->planName ?? 'Unknown') . ($giftedBy ? '(gift)' : '') . ", Installment $mshp->instllmntPhase of $mshp->installment",
                    'amount' => ($mmbShpExp ? '(valid till ' . date('F j, Y', strtotime($mmbShpExp)) . ' )' : '') . '<b>' . dollar($amount) . '</b>'
                ];
            } else {
                $mmbShpExp = null;
                $validity = $mshpPlans[$mshp->planId]->duration;

                if ($validity == '1 year') {
                    $mmbShpExp = $this->calcOneYrExpiry();
                }
                $update['expiry'] = $mmbShpExp;
                $receiptItms = [
                    'item' => ($mshp->planName ?? 'Unknown') . ($giftedBy ? '(gift)' : ''),
                    'amount' => ($mmbShpExp ? '(valid till ' . date('F j, Y', strtotime($mmbShpExp)) . ' )' : '') . '<b>' . dollar($mshp->amount) . '</b>'
                ];
            }

            if ($update) {
                $pixdb->update(
                    'memberships',
                    ['id' => $mshp->id],
                    $update
                );
            }
        } else {
            $this->postNotification(
                'admin',
                $mshp->member,
                'new-membership',
                "$mshp->planName Renewal Failed",
                'Payment received successfully, But the renwal plan not exist now'
            );
        }
        if (!isset($receipts[$member->id])) {
            $receipts[$member->id] = [];
        }
        $receipts[$member->id][] = $receiptItms;
    }
}

foreach ($paidBenefitIds as $padBnfts) {
    $member = isset($mmberInfo[$padBnfts->members]) ? $mmberInfo[$padBnfts->members] : null;
    if ($member) {
        if (isset($products[$padBnfts->payCategoryId]) && $products[$padBnfts->payCategoryId]->enabled == 'Y') {
            $pdt = $products[$padBnfts->payCategoryId];
            $expiry = null;
            $validity = $pdt->validity;

            if ($validity == 'fiscal-year') {
                $expiry = $this->calcOneYrExpiry();
            }
            $receiptItms = [
                'item' => ($padBnfts->title ?? $pdt->name),
                'amount' => ($mmbShpExp ? '(valid till ' . date('F j, Y', strtotime($expiry)) . ' )' : '') . '<b>' . dollar($pdt->amount) . '</b>'
            ];

            $pixdb->update(
                'paid_benefits',
                ['id' => $padBnfts->id],
                [
                    'expiry' => $expiry,
                    'enabled' => 'Y'
                ]
            );
        } else {
            $this->postNotification(
                'admin',
                $padBnfts->member,
                'new-membership',
                "$payCategoryId->title Renewal Failed",
                'Payment received successfully, But the renwal item not exist now'
            );
        }
        if (!isset($receipts[$member->id])) {
            $receipts[$member->id] = [];
        }
        $receipts[$member->id][] = $receiptItms;
    }
}

$txnMarked = true;
$txnOwner = isset($mmberInfo[$txn->member]) ? $mmberInfo[$txn->member] : null;
if ($txnOwner) {
    // notify admin
    $this->postNotification(
        'admin',
        $txn->member,
        'new-membership',
        'Renewal Payment',
        "$txnOwner->firstName $txnOwner->lastName paid for renewals"
    );

    foreach ($receipts as $member => $re) {
        $memberInfo = isset($mmberInfo[$member]) ? $mmberInfo[$member] : null;
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
                $mainCnt .= "Received: Renewal updates made to $memberInfo->firstName $memberInfo->lastName by <b>$txnOwner->firstName $txnOwner->lastName</b>.";
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
                $tr .= '<tr>
                    <td style="padding: 5px 0px">' . $item['item'] . '</td>
                    <td style="padding: 5px 0px; text-align: right">
                    ' . $item['amount'] . '
                    </td>
                </tr>';
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
