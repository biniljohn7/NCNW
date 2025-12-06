<?php
(function ($pix, $pixdb, $r, $evg) {
    $pl = file_get_contents('php://input');
    $pl = $pl ? json_decode($pl) : false;
    if (!is_object($pl)) {
        $pl = false;
    }

    if (
        isset(
            $pl->email,
            $pl->password
        )
    ) {
        $email = esc($pl->email);
        $password = esc($pl->password);

        if (
            $email &&
            $password
        ) {
            $member = $pixdb->get(
                'members',
                [
                    'email' => $email,
                    'single' => 1
                ],
                'id, 
            password, 
            verified,
            firstName,
            lastName,
            email,
            memberId,
            avatar'
            );
            if (
                $member &&
                $member->password == $pix->encrypt($password)
            ) {
                if ($member->verified == 'Y') {
                    // fetching existing token
                    $authToken = $pixdb->get(
                        'members_auth',
                        [
                            'id' => $member->id,
                            'single' => 1
                        ],
                        'token'
                    )->token ?? false;

                    // creating new token
                    if (!$authToken) {
                        $authToken = $evg->generateAuthToken();

                        $pixdb->insert(
                            'members_auth',
                            [
                                'id' => $member->id,
                                'token' => $authToken
                            ]
                        );
                    }

                    $r->success = 1;
                    $r->data = [
                        'memberId' => $member->id,
                        'prefix' => null,
                        'suffix' => 'None',
                        'firstName' => $member->firstName,
                        'lastName' => $member->lastName,
                        'email' => $member->email,
                        'profileImage' => $evg->getAvatar($member->avatar),
                        'membershipExpireDate' => null,
                        'accessToken' => $authToken,
                        'memberCode' => $member->memberId,
                        'referralCode' => null,
                        'referralPoints' => 0,
                        'currentChapter' => null,
                        'profileCreated' => true,
                        'notification' => true
                    ];
                    $r->message = 'Login successfully!';
                    // 
                } else {
                    $r->message = 'Email is not verified yet. Please verify your email first!';
                }
            } else {
                $r->message = 'Login failed. Invalid email or password.';
            }
        }
    }
})($pix, $pixdb, $r, $evg);
