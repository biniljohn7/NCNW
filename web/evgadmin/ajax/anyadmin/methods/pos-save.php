<?php
$_ = $_POST;

$mmbrship = [];
$products = [];
$payTotal = 0;
$payload = [];
$stripeItms = [];

if (isset($_['paymentCatgry'], $_['paymentMode']) && is_array($_['paymentCatgry'])) {
    $paymentMode = esc($_['paymentMode']);

    if (preg_match('/^(mark-paid|make-payment)$/', $paymentMode)) {
        $lgMemberData = $pixdb->getRow(
            [
                ['admins', 'a', 'memberid'],
                ['members', 'm', 'id']
            ],
            ['a.id' => $lgUser->id],
            'm.*'
        );
        if ($lgMemberData) {
            foreach ($_['paymentCatgry'] as $key => $values) {
                $values = (array) $values;

                if (strpos($key, 'mb_') === 0) {
                    $mmbrship[] = (object)[
                        'id'     => str_replace('mb_', '', $key),
                        'member' => $values
                    ];
                } elseif (strpos($key, 'pd_') === 0) {
                    $products[] = (object)[
                        'id'    => str_replace('pd_', '', $key),
                        'member' => $values
                    ];
                }
            }

            $mmbrshpIds = collectObjData($mmbrship, 'id');
            $prodIds = collectObjData($products, 'id');
            $membershipData = [];
            $productData = [];
            if ($mmbrshpIds) {
                $membershipData = $pixdb->fetchAssoc(
                    'membership_plans',
                    ['id' => $mmbrshpIds],
                    '*',
                    'id'
                );
            }
            if ($prodIds) {
                $productData = $pixdb->fetchAssoc(
                    'products',
                    ['id' => $prodIds],
                    '*',
                    'id'
                );
            }

            $txnData = [
                'type' => 'pos',
                'member' => $lgMemberData->id,
                'posDoneBy' => $lgUser->id
            ];
            if ($paymentMode == 'mark-paid') {
                $txnData['paymentMode'] = 'manual';
            }

            foreach ($mmbrship as $idx => $item) {
                if (isset($membershipData[$item->id])) {
                    $thsMbrPln = $membershipData[$item->id];
                    $memberIds = array_unique(array_filter(array_map('esc', $item->member)));
                    $quantity = count($memberIds);
                    $subTotal = $thsMbrPln->ttlCharge * $quantity;
                    $payTotal += $subTotal;
                    $stripeItms[] = [
                        'name' => $thsMbrPln->title,
                        'amount' => $evg->dollarInCents($thsMbrPln->ttlCharge),
                        'quantity' => $quantity,
                        'item_id' => "$thsMbrPln->code$idx"
                    ];
                    foreach ($memberIds as $mbmId) {
                        $payload[] = (object)[
                            'code' => $thsMbrPln->code,
                            'title' => $thsMbrPln->title,
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
                                'installment' => null,
                                'sectionId' => null,
                                'affiliateId' => null
                            ],
                            'sectionId' => null,
                            'affiliateId' => null,
                            'amount' => $thsMbrPln->ttlCharge,
                            'beneficiary' => $mbmId,
                            'type' => 'membership'
                        ];
                    }
                }
            }


            foreach ($products as $idx => $item) {
                if (isset($productData[$item->id])) {
                    $prod = $productData[$item->id];
                    $memberIds = array_unique(array_filter(array_map('esc', $item->member)));
                    $quantity = count($memberIds);
                    $subTotal = $prod->amount * $quantity;
                    $payTotal += $subTotal;
                    $stripeItms[] = [
                        'name' => $prod->name,
                        'amount' => $evg->dollarInCents($prod->amount),
                        'quantity' => $quantity,
                        'item_id' => "$prod->code$idx"
                    ];
                    foreach ($memberIds as $mbmId) {
                        $payload[] = (object)[
                            'code' => $prod->code,
                            'title' => $prod->name,
                            'details' => $prod,
                            'sectionId' => null,
                            'affiliateId' => null,
                            'amount' => $prod->amount,
                            'beneficiary' => $mbmId,
                            'type' => 'product'
                        ];
                    }
                }
            }
            if ($payload) {
                $txnData['amount'] = $payTotal;
                $txnData['title'] = 'POS';
                $txnData['items'] = $payload;

                $nTxnId = $evg->createTxn($txnData);
                if ($nTxnId) {
                    $txnId = $pixdb->getRow(
                        'transactions',
                        ['txnid' => $nTxnId],
                        'id'
                    )->id;
                    $redirectUrl = DOMAIN . 'evgadmin/?page=transactions&sec=details&id=' . $txnId;
                    if ($paymentMode == 'make-payment') {
                        $stripeSession = loadModule('stripe')->createCheckoutSession($stripeItms, $nTxnId, $lgMemberData, $redirectUrl);

                        if (is_object($stripeSession) && $stripeSession->url) {
                            $r->success = 1;
                            $r->status = 'ok';
                            $r->redirect = $stripeSession->url;
                        } else {
                            $r->message = 'Oops! some error has been occured. Please, try again.';
                        }
                    } else {
                        if ($txnId) {
                            $res = $evg->markPaymentDone($txnId);
                            if ($res->marked) {
                                $r->status = 'ok';
                                $r->redirect = $redirectUrl;
                            } else {
                                $r->message = 'Oops! some error has been occured. Please, try again.';
                            }
                        }
                    }
                }
            }
        }
    } else {
        $r->message = 'Invalid payment method.';
    }
}
