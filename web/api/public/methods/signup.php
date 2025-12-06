<?php
(function ($pix, $pixdb, $evg, &$r, $datetime) {

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
            $pl->password
            // $pl->memberCode
        )
    ) {
        $firstName = ucwords(esc($pl->firstName));
        $lastName = ucwords(esc($pl->lastName));
        $email = esc($pl->email);
        $memberCode = esc($pl->memberCode ?? null);
        $pass = esc($pl->password);
        $section = have($pl->section);
        $affiliation = isset($pl->affiliation) && is_array($pl->affiliation) ? $pl->affiliation : [];
        $collegiate = have($pl->collegiate);

        if ($section) {
            $valid = $evg->getChapter($section, 'id');
            if (!$valid) {
                $section = null;
            }
        }

        if ($affiliation) {
            $affIds = collectObjData($affiliation, 'value');
            if ($affIds) {
                $affiliation = $evg->getAffiliations($affIds, 'id');
            }
        }

        if ($collegiate) {
            $valid = $evg->getCollgueSections([$collegiate], 'id');
            if (!$valid) {
                $collegiate = null;
            }
        }
        /* ($section || $affiliation || $collegiate) && */
        if (
            $firstName &&
            is_mail($email) &&
            $pass
        ) {
            $mdMembers = loadModule('members');
            $check = $mdMembers->checkMemberExist($email);

            if (!$memberCode) {
                $memberCode = $pix->makeMemberId();
            }

            if (
                $check &&
                !$check->exist
            ) {
                $pass = $pix->encrypt($pass);
                $data = [
                    'email' => $email,
                    'firstName'  => $firstName,
                    'lastName' => $lastName,
                    'memberId' => $memberCode,
                    'verified' => 'N',
                    'password' => $pass,
                    'regOn' => $datetime,
                ];

                $dbData = $data;

                $mmbrId = $pixdb->insert(
                    'members',
                    $dbData
                );
                if ($mmbrId) {

                    $refCode = $pix->makestring(8, 'un');

                    $memberInfo = [
                        'member' => $mmbrId,
                        'refCode' => substr($refCode, 0, 8) ?: null,
                        'cruntChptr' => $section ?: null,
                        'collegiateSection' => $collegiate ?: null
                    ];

                    $pixdb->insert(
                        'members_info',
                        $memberInfo
                    );

                    //  member affiliation
                    if ($affiliation) {
                        $affInsData = [];
                        foreach ($affiliation as $affili) {
                            $affInsData[] = [
                                'member' => $mmbrId,
                                'affiliation' => $affili->id
                            ];
                        }

                        $pixdb->multiInsert(
                            'members_affiliation',
                            ['member', 'affiliation'],
                            $affInsData
                        );
                    }

                    //send verification
                    loadModule('account-verification')->sendVerification(
                        (object)[
                            'id' => $mmbrId,
                            'email' => $email,
                            'firstName' => "$firstName $lastName"
                        ],
                        null,
                        false
                    );

                    // post notification
                    $evg->postNotification(
                        'admin',
                        $mmbrId,
                        'new-member',
                        'New Member',
                        "$firstName $lastName created new member account."
                    );

                    $r->status = 'ok';
                    $r->success = 1;
                    $r->message = 'Account created successfully! We have sent you an email on registered email address, please verify your account first!';
                }
            } else {
                $r->message = 'Email is already Registered!';
            }
        } else {
            $r->message = 'Some information is missing. Please fill out all required fields.';
        }
    }
})($pix, $pixdb, $evg, $r, $datetime);
