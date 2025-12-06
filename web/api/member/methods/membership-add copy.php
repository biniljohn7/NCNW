<?php

$inp = getJsonBody();
if (
    isset($inp->members) &&
    is_array($inp->members)
) {
    $memberships = $inp->members;
    $plnIds = [];
    $plans = [];
    $mbrIds = [];
    $validReq = true;

    foreach ($memberships as $mbr) {
        if (isset($mbr->membershipPlan, $mbr->memberIds) && is_array($mbr->memberIds)) {
            $memberIds = array_unique(array_filter(array_map('esc', $mbr->memberIds)));
            if (is_array($memberIds)) {
                $mbrIds = array_merge($mbrIds, $memberIds);
                $plnIds[] = esc($mbr->membershipPlan);
            } else {
                $validReq = false;
                break;
            }
        }
    }

    $plnIds = array_unique(array_filter($plnIds));

    if ($validReq && $plnIds) {
        $plans = $pixdb->fetchAssoc(
            'membership_plans',
            [
                'active' => 'Y',
                '#QRY' => 'id in (' . implode(', ', $plnIds) . ')'
            ],
            '*',
            'id'
        );
    }

    //  check plans are valid or not

    foreach ($memberships as $mbr) {
        if (!isset($plans[$mbr->membershipPlan])) {
            $validReq = false;
            break;
        }
    }


    if ($validReq) {
        $payTotal = 0;
        $payload = [];
        $stripeItms = [];
        $own = false;

        foreach ($memberships as $idx => $mbr) {
            $memberIds = array_unique(array_filter(array_map('esc', $mbr->memberIds)));
            $membershipPlan = esc($mbr->membershipPlan);
            $installment = esc($mbr->installment ?? null);
            $sectionId = esc($mbr->sectionId ?? null);
            $affiliateId = esc($mbr->affiliateId ?? null);

            $thsMbrPln = $plans[$membershipPlan];
            $planTtlCharge = $thsMbrPln->ttlCharge;
            $installment = $installment > 1 ? $installment : null;
            if ($installment) {
                $isRightInstllmnt = false;

                if ($thsMbrPln->installments) {
                    $installments = explode(',', $thsMbrPln->installments);
                    if (in_array($installment, $installments)) {
                        $isRightInstllmnt = true;
                        $planTtlCharge = $planTtlCharge / $installment;
                    }
                }
            }

            if (!$installment || ($installment && $isRightInstllmnt)) {
                $quantity = count($memberIds);
                $subTotal = $planTtlCharge * $quantity;
                $payTotal += $subTotal;
                $isGift = array_diff($mbrIds, [$lgUser->id]) ? true : false;
                $publicTtl = $thsMbrPln->title . ($installment ? " - installment 1 of $installment" : '') . ($isGift ? " (Gift)" : '');
                $stripeItms[] = [
                    'name' => $publicTtl,
                    'amount' => $evg->dollarInCents($planTtlCharge),
                    'quantity' => $quantity,
                    'item_id' => "$thsMbrPln->code$idx"
                ];

                foreach ($memberIds as $mbmId) {
                    $payload[] = (object)[
                        'code' => $thsMbrPln->code,
                        'title' => $publicTtl,
                        'details' => (object)[
                            'memberId' => $mbmId,
                            'planId' => $thsMbrPln->id,
                            'planName' => $thsMbrPln->title,
                            'natDue' => $thsMbrPln->nationalDue,
                            'natLtFee' => $thsMbrPln->natLateFee,
                            'natCapFee' => $thsMbrPln->natCapFee,
                            'natReinFee' => $thsMbrPln->natReinFee,
                            'locDue' => $thsMbrPln->localDue,
                            'ttlLocChaptChrg' => $thsMbrPln->ttlLocChaptChrg,
                            'ttlNatChaptChrg' => $thsMbrPln->ttlNatChaptChrg,
                            'ttlCharge' => $thsMbrPln->ttlCharge,
                            'chapDon' => null,
                            'natDon' => null,
                            'validity' => $thsMbrPln->duration,
                            'installment' => $installment,
                            'sectionId' => $sectionId ?? null,
                            'affiliateId' => $affiliateId ?? null
                        ],
                        'sectionId' => $sectionId ?? null,
                        'affiliateId' => $affiliateId ?? null,
                        'amount' => $planTtlCharge,
                        'beneficiary' => $mbmId
                    ];
                }
            }
            if (in_array($lgUser->id, $memberIds)) {
                $own = true;
            }
        }

        $msg = '';
        if ($own) {
            $msg = 'Own membership';
            $mbrIds = array_diff($mbrIds, [$lgUser->id]);
        }
        if (!empty($mbrIds)) {
            $msg .= ($msg == '' ? '' : ' and ') . 'Gift purchase';
        }

        $txnData = [
            'type' => 'membership-payment',
            'member' => $lgUser->id,
            'amount' => $payTotal,
            'title' => 'Membership Payment - ' . $msg,
            'items' => $payload
        ];

        $nTxnId = $evg->createTxn($txnData);

        if ($nTxnId) {
            // process payment from here
            $stripeSession = loadModule('stripe')->createCheckoutSession($stripeItms, $nTxnId, $lgUser);

            if (is_object($stripeSession) && $stripeSession->url) {
                $r->success = 1;
                $r->status = 'ok';
                $r->data->paymentUrl = $stripeSession->url;
            } else {
                $r->message = 'Oops! some error has been occured. Please, try again.';
            }
        }
    } else {
        $r->message = 'Invalid request';
    }
}
