<?php
$_ = $_REQUEST;

if (isset(
    $_['mId'],
    $_['stId']
)) {

    $mId = intval($_['mId']);
    $stId = intval($_['stId']);

    if (
        $mId &&
        $stId
    ) {
        $valid = 1;

        $stData = $pixdb->get(
            'state_leaders',
            ['stateId' => $stId]
        );
        $state = $evg->getState($stId, 'id, name');
        if (!$state) {
            $valid = 0;
            $r->msg = 'Invalid request.';
        }

        if ($valid && $stData->data) {
            if (count($stData->data) >= 2) {
                $valid = 0;
                $r->msg = 'Already, two leaders have been added.';
            }
            if ($valid) {
                foreach ($stData->data as $info) {
                    if ($info->mbrId == $mId) {
                        $valid = 0;
                        $r->msg = 'The member is already added.';
                    }
                }
            }
        }

        if ($valid) {
            $usrRoles = $pixdb->get(
                'members',
                [
                    'id' => $mId,
                    'single' => 1
                ],
                'email,
                password,
                firstName,
                lastName,
                role'
            );

            if ($usrRoles && $usrRoles->email && $usrRoles->firstName) {
                $valid = 1;
            } else {
                $valid = 0;
                $r->msg = 'This member is ineligible for selection as State Leader because required information (email, first name) is incomplete.';
            }
        }

        if ($valid) {
            $pixdb->insert(
                'state_leaders',
                [
                    'mbrId' => $mId,
                    'stateId' => $stId
                ]
            );

            $rolesArray = explode(',', $usrRoles->role ?? '');
            $rolesArray[] = 'state-leader';
            $rolesArray = array_filter($rolesArray);

            $pixdb->update(
                'members',
                ['id' => $mId],
                ['role' => !empty($rolesArray) ? implode(',', $rolesArray) : null]
            );

            $email = $usrRoles->email;

            $fullName = $usrRoles->firstName . ' ' . $usrRoles->lastName;
            $uName = preg_replace('/[^a-z0-9._]/', '', strtolower(strstr($email, '@', true)));
            $adminType = 'state-leader';

            $checkUser = $pixdb->getRow(
                'admins',
                ['email' => $email],
                'id,
                type,
                name,
                perms,
                memberid'
            );

            if ($checkUser) {
                $userId = $checkUser->id;
                $uType = $checkUser->type;
                $perms = array_filter(explode(',', $checkUser->perms));
                //
                $upData = [];
                $upData['name'] = $checkUser->name ?: $fullName;
                $upData['memberid'] = $checkUser->memberid ?: $mId;
                if ($uType == 'sub-admin') {
                    if (
                        !empty($perms) &&
                        !in_array('elect', $perms)
                    ) {
                        $perms[] = 'elect';
                    }
                    $upData['perms'] = implode(',', array_unique($perms)) ?: NULL;
                }
                $pixdb->update(
                    'admins',
                    ['id' => $userId],
                    $upData
                );
            } else {
                $password = $pix->encrypt('abcd.123');
                $adminData = [
                    'type' => $adminType,
                    'enabled' => 'Y',
                    'name' => $fullName,
                    'username' => $uName,
                    'email' => $email,
                    'phone' => NULL,
                    'password' => $password,
                    'memberid' => $mId
                ];

                $newId = $pixdb->insert(
                    'admins',
                    $adminData
                );

                if ($newId) {
                    $tplInfo = $pix->getData('email-templates/leaders-invite-access');
                    if (!is_object($tplInfo)) {
                        $tplInfo = (object)[];
                    }
                    if (isset($tplInfo->body)) {
                        $tplInfo->body = str_replace(
                            ['[ROLE]', '[SECTION]'],
                            ['State leader', $state->name],
                            $tplInfo->body
                        );
                    } else {
                        $tplInfo->body = 'We are excited to inform you that you have been officially selected as the ' . "State leader for $state->name" . '.<br/> 
                        This is a significant role, and were confident that your leadership will make a meaningful impact within the community.';
                    }
                    $mlArgs = [
                        'CONTENT' => nl2br($tplInfo->body ?? ''),
                        'BTNTEXT' => nl2br($tplInfo->btntxt ?? 'To begin, please activate your leadership account using the link below:'),
                        'BTNLABEL' => $tplInfo->btnlabel ?? 'Get Started',
                    ];

                    loadModule('admin-account-verification')->sendVerification(
                        (object)array(
                            'id' => $newId,
                            'email' => $email,
                            'name' => $fullName
                        ),
                        null,
                        false,
                        'leaders-invite-access',
                        $mlArgs
                    );
                }
            }

            //update access token
            $evg->changeAccessToken($mId);

            $r->status = 'ok';
        }
    }
}
