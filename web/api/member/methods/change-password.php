<?php
(function ($pix, $pixdb, $r, $lgUser, $evg) {
    $pl = file_get_contents('php://input');
    $pl = $pl ? json_decode($pl) : false;
    if (!is_object($pl)) {
        $pl = false;
    }

    if (
        isset(
            $pl->currentPassword,
            $pl->newPassword,
            $pl->confirmPwd
        )
    ) {
        $curPass = esc($pl->currentPassword);
        $newPass = esc($pl->newPassword);
        $conPass = esc($pl->confirmPwd);

        if (
            $curPass &&
            $newPass &&
            $newPass == $conPass
        ) {

            $member = $pixdb->get(
                'members',
                [
                    'id' => $lgUser->id,
                    'single' => 1
                ]
            );

            if ($member->password == $pix->encrypt($curPass)) {
                $npwEnc = $pix->encrypt($newPass);

                // modifying db
                $pixdb->update(
                    'members',
                    [
                        'id' => $lgUser->id
                    ],
                    [
                        'password' => $npwEnc
                    ]
                );

                // creating new token
                $authToken = $evg->generateAuthToken();

                $pixdb->insert(
                    'members_auth',
                    [
                        'id' => $lgUser->id,
                        'token' => $authToken
                    ],
                    true
                );

                $r->status = 'ok';
                $r->success = 1;
                $r->message = 'Password changed successfully';
            } else {
                $r->message = 'Current password is incorrect';
            }
        }
    }
})($pix, $pixdb, $r, $lgUser, $evg);
