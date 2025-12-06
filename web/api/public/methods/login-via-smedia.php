<?php
(function ($pix, $pixdb, $r, $evg, $datetime) {
    $pl = file_get_contents('php://input');
    $pl = $pl ? json_decode($pl) : false;
    if (!is_object($pl)) {
        $pl = false;
    }

    if (
        isset(
            $pl->firstName,
            $pl->lastName,
            $pl->email,
            $pl->facebookId,
            $pl->googleId,
            $pl->registerType,
            $pl->imageUrl
        )
    ) {
        $firstName = esc($pl->firstName);
        $lastName = esc($pl->lastName);
        $email = esc($pl->email);
        $facebookId = esc($pl->facebookId);
        $googleId = esc($pl->googleId);
        $regType = esc($pl->registerType);
        $imageUrl = esc($pl->imageUrl);

        if (is_mail($email) && $regType && ($facebookId || $googleId)) {
            $alias = $facebookId ?: $googleId;
            $member = $pixdb->get(
                'members',
                [
                    'email' => $email,
                    'alias' => $alias,
                    'account' => $regType,
                    'single' => 1
                ],
                'id, 
                firstName,
                lastName,
                email,
                memberId,
                avatar,
                enabled'
            );

            if (!$member && $firstName && $lastName) {
                $mdMembers = loadModule('members');
                $check = $mdMembers->checkMemberExist($email);

                if (
                    $check &&
                    !$check->exist
                ) {
                    $enabled = 'Y';
                    $data = [
                        'email' => $email,
                        'firstName'  => $firstName,
                        'lastName' => $lastName,
                        'verified' => 'Y',
                        'regOn' => $datetime,
                        'alias' => $alias,
                        'account' => $regType,
                        'enabled' => $enabled
                    ];

                    if ($imageUrl && strlen($imageUrl) < 80) {
                        $dbData['avatar'] = $imageUrl;
                    }

                    $dbData = $data;

                    $mmbrId = $pixdb->insert(
                        'members',
                        $dbData
                    );
                    if ($mmbrId) {
                        $member = (object)[
                            'id' => $mmbrId,
                            'enabled' => $enabled
                        ];
                    }
                }
            }

            if ($member) {
                if ($member->enabled == 'N') {
                    $r->message = 'Your profile has been inactivated for some reason.';
                } else {
                    $r->status = 'ok';
                    $r->success = 1;
                    $r->data = $evg->setLoginData($member->id);
                    $r->message = 'Login successfully!';
                }
            } else {
                $r->message = 'Login failed.';
            }
        }
    }
})($pix, $pixdb, $r, $evg, $datetime);
