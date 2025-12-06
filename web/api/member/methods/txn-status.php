<?php
(function ($pix, $pixdb, $evg, &$r, $datetime, $lgUser, $date) {

    $pl = file_get_contents('php://input');
    $pl = $pl ? json_decode($pl) : false;
    if (!is_object($pl)) {
        $pl = false;
    }

    if (
        isset(
            $pl->token,
            $pl->status
        )
    ) {
        $txnId = esc($pl->token);
        $status = esc($pl->status);

        if ($txnId && $status) {
            $txnData = $pixdb->getRow(
                'transactions',
                [
                    'txnid' => $txnId
                ],
                'id,member,status,amount'
            );

            if ($txnData) {
                $r->success = 1;
                $r->status = 'ok';
                $r->data->payStatus = $txnData->status;

                // For localhost, not consider the webhook, so make the transaction status accordingly
                if (ISLOCAL && $txnData->status != 'success') {
                    if ($status == 'success') {
                        $res = $evg->markPaymentDone($txnData->id);
                        if ($res->marked) {
                            $txnData->status = 'success';
                            $r->data->payStatus = 'success';

                            // notification
                            $memberInfo = $pixdb->getRow('members', ['id' => $txnData->member], 'firstName, lastName');
                            if ($memberInfo) {
                                $evg->postNotification(
                                    'admin',
                                    $txnData->member,
                                    'new-payment',
                                    'New Payment',
                                    "New payment of " . dollar($txnData->amount, 1, 1) . " recieved from $memberInfo->firstName $memberInfo->lastName.",
                                    ['id' => $txnData->id]
                                );
                            }
                        }
                    } elseif ($status == 'cancelled') {
                        $iid = $pixdb->update(
                            'transactions',
                            ['txnid' => $txnId],
                            ['status' => 'cancelled']
                        );
                        if ($iid) {
                            $txnData->status = 'cancelled';
                            $r->data->payStatus = 'cancelled';
                        }
                    }
                }

                if ($txnData->status == 'success') {
                    $mmbrShp = $pixdb->getRow(
                        'memberships',
                        [
                            'member' => $lgUser->id,
                            '#QRY' =>  '(expiry >= ' . q($date) . ' OR expiry IS NULL)',
                            'enabled' => 'Y',
                            '#SRT' => 'id desc'
                        ],
                        'expiry'
                    );
                    if ($mmbrShp) {
                        $r->data->loginData = (object)[
                            'membershipExpireDate' => $mmbrShp->expiry,
                            'membershipStatus' => 'active'
                        ];
                    }
                }
            }
        }
    }
})($pix, $pixdb, $evg, $r, $datetime, $lgUser, $date);
