<?php
if (!$pix->canAccess('members')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb, $evg, $datetime, $lgUser) {
    $_ = $_POST;

    devMode();
    if (isset($_['membership'])) {

        $membership = esc($_['membership']);
        $mid = esc($_['mid']);

        if ($membership && $mid) {
            $membershipPlan = $evg->getMembershipPlan(
                $membership,
                'id, 
                title, 
                duration,
                ttlCharge'
            );

            $member = $pixdb->getRow(
                'members',
                ['id' => $mid],
                'id'
            );
            if ($membershipPlan && $member) {
                $hvOffer = $pixdb->getRow(
                    'memberships',
                    [
                        'member' => $mid,
                        '#QRY' => 'offerBy IS NOT null'
                    ],
                    'id'
                );

                $membershipData = [
                    'member' => $member->id,
                    'giftedBy' => null,
                    'planId' => $membership ?? null,
                    'planName' => $membershipPlan->title ?? null,
                    'created' => $datetime,
                    'expiry' => $membershipPlan->duration == null ? null : $evg->calcOneYrExpiry(),
                    'amount' => $membershipPlan->ttlCharge,
                    'amtCalc' => null,
                    'payStatus' => 'completed',
                    'offerBy' => $lgUser->id
                ];

                if ($hvOffer) {
                    $iid = $hvOffer->id;
                    $pixdb->update(
                        'memberships',
                        ['id' => $iid],
                        $membershipData
                    );
                } else {
                    $iid = $pixdb->insert(
                        'memberships',
                        $membershipData
                    );
                }
                if ($iid) {
                    //update access token
                    $evg->changeAccessToken($mid);
                    $pix->addmsg('Membership saved', 1);
                    $pix->redirect('?page=members&sec=details&id=' . $member->id . '#membership');
                }
            } else {
                $pix->addmsg('Invalid request.');
            }
        } else {
            $pix->addmsg('Required data is missing');
        }
    }
})($pix, $pixdb, $evg, $datetime, $lgUser);
