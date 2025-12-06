<?php
$_ = $_REQUEST;
if ($pix->canAccess('elect')) {
    if (isset(
        $_['mId'],
        $_['chptrId'],
        $_['type']
    )) {

        $mId = intval($_['mId']);
        $chptrId = intval($_['chptrId']);
        $type = esc($_['type']);

        $validTypes = ['leader', 'president'];

        if (
            $mId &&
            $chptrId &&
            $type &&
            in_array($type, $validTypes)
        ) {
            $valid = 1;

            $secData = $pixdb->get(
                'section_leaders',
                [
                    'secId' => $chptrId
                ]
            );
            $section = $evg->getChapter($chptrId, 'id,name');
            if (!$section) {
                $valid = 0;
                $r->msg = 'Invalid request.';
            }

            if ($valid && $secData->data) {
                $lCnt = 0;
                $pCnt = 0;
                foreach ($secData->data as $info) {
                    $info->type == 'leader' ? $lCnt++ : 0;
                    $info->type == 'president' ? $pCnt++ : 0;
                }

                if ($type == 'leader') {
                    if ($lCnt >= 3) {
                        $valid = 0;
                        $r->msg = 'Already, three leaders have been added.';
                    }
                }

                if ($type == 'president') {
                    if ($pCnt >= 1) {
                        $valid = 0;
                        $r->msg = 'The section president has already added.';
                    }
                }

                if ($valid) {
                    foreach ($secData->data as $info) {
                        if ($info->mbrId == $mId) {
                            $valid = 0;
                            $r->msg = 'The member is already added as ' . $info->type;
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
                    'section_leaders',
                    [
                        'mbrId' => $mId,
                        'secId' => $chptrId,
                        'type' => $type
                    ]
                );

                $rolesArray = explode(',', $usrRoles->role ?? '');
                $rolesArray[] = $type == 'leader' ? 'section-leader' : 'section-president';
                $rolesArray = array_filter($rolesArray);

                $pixdb->update(
                    'members',
                    ['id' => $mId],
                    ['role' => !empty($rolesArray) ? implode(',', $rolesArray) : null]
                );
                if ($type == 'president') {
                    $email = $usrRoles->email;
                    $fullName = $usrRoles->firstName . ' ' . $usrRoles->lastName;
                    $uName = preg_replace('/[^a-z0-9._]/', '', strtolower(strstr($email, '@', true)));
                    $adminType = 'section-president';

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
                            //  email queue
                            $tplInfo = $pix->getData('email-templates/leaders-invite-access');
                            if (!is_object($tplInfo)) {
                                $tplInfo = (object)[];
                            }
                            if (isset($tplInfo->body)) {
                                $tplInfo->body = str_replace(
                                    ['[ROLE]', '[SECTION]'],
                                    ['Section ' . $type, $section->name],
                                    $tplInfo->body
                                );
                            } else {
                                $tplInfo->body = 'We are excited to inform you that you have been officially selected as the ' . "Section $type for $section->name" . '.<br/> 
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
                }
                //update access token
                $evg->changeAccessToken($mId);

                $r->status = 'ok';
            }
        }
    }
} else {
    $r->msg = 'Access Denied!';
}
