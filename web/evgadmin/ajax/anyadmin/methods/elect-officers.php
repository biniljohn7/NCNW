<?php
$_ = $_REQUEST;
if ($pix->canAccess('elect')) {
    if (isset($_['uId'], $_['offId'])) {
        $userId = intval($_['uId']);
        $officerId = intval($_['offId']);
        if ($userId && $officerId) {
            //
            $memId = $pixdb->getRow(
                'admins',
                ['id' => $lgUser->id],
                'memberId'
            );
            //
            // $section = $pixdb->getRow(
            //     'section_leaders',
            //     [
            //         'type' => 'president',
            //         'mbrId' => $memId->memberId
            //     ],
            //     'secId'
            // );

            $section = $pixdb->getRow(
                'members_info',
                ['member' => $userId],
                'cruntChptr as secId'
            );
            //
            $officers = [
                1 => 'officer-president',
                2 => 'first-vice-president',
                3 => 'second-vice-president',
                8 => 'treasurer',
                19 => 'collegiate-liaison'
            ];
            //
            if ($section) {
                $chkOffExist = $pixdb->getRow(
                    'officers',
                    [
                        '#QRY' => 'memberId!=' . $userId,
                        'title' => $officerId,
                        'circleId' => $section->secId,
                        'circle' => 'section'
                    ]
                );
                //
                $roles = $pixdb->getRow(
                    'members',
                    [
                        'id' => $userId
                    ],
                    'role'
                );
                //
                if (!$chkOffExist) {
                    $exist = $pixdb->getRow(
                        'officers',
                        [
                            'memberId' => $userId,
                            'circleId' => $section->secId,
                            'circle' => 'section'
                        ],
                        'id,
                    memberId,
                    title'
                    );
                    //anonymus function for updating role
                    $updateMemberRole = function ($pixdb, $membId, $roles, $remove = false) {
                        if ($roles->role) {
                            $rolesArray = explode(',', $roles->role);
                            if (!in_array('section-officer', $rolesArray)) {
                                $rolesArray[] = "section-officer";
                            }
                            if ($remove) {
                                $rolesArray = array_diff($rolesArray, ['section-officer']);
                            }
                            $roleValue = implode(',', $rolesArray);
                        } else {
                            $roleValue = 'section-officer';
                        }

                        $pixdb->update(
                            'members',
                            ['id' => $membId],
                            ['role' => $roleValue != '' ? $roleValue : NULL]
                        );
                    };
                    //
                    if ($exist) {
                        if ($officerId > 0) {
                            if ($exist->title != $officerId) {
                                $pixdb->update(
                                    'officers',
                                    ['id' => $exist->id],
                                    ['title' => $officerId]
                                );
                                $updateMemberRole($pixdb, $userId, $roles);
                                $r->status = "ok";
                                $r->msg = 'Member elected title changed.';
                            } else {
                                echo "Already elected for the same title";
                            }
                        } else {
                            $pixdb->delete(
                                'officers',
                                ['id' => $exist->id]
                            );
                            $updateMemberRole($pixdb, $userId, $roles, true);
                            $r->status = "ok";
                            $r->msg = 'Member election Removed.';
                        }
                    } else {
                        if ($officerId > 0) {
                            $dbData = [
                                'memberId' => $userId,
                                'title' => $officerId,
                                'assignedBy' => $memId->memberId,
                                'circle' => 'section',
                                'circleId' => $section->secId
                            ];
                            $pixdb->insert(
                                'officers',
                                $dbData
                            );
                            $updateMemberRole($pixdb, $userId, $roles);
                            $r->status = "ok";
                            $r->msg = 'Member Elected';
                        } else {
                            $r->msg = 'Member never got elected';
                        }
                    }
                    //Allowing or removing access to admin panel
                    $memberData = $pixdb->getRow(
                        'members',
                        ['id' => $userId],
                        'id,
                            firstName,
                            lastName,
                            email,
                            password'
                    );
                    if ($memberData) {
                        $checkAdminExists = $pixdb->getRow(
                            'admins',
                            [
                                'email' => $memberData->email,
                                'memberid' => $memberData->id
                            ],
                            'id,
                            type'
                        );

                        //
                        if ($checkAdminExists) {
                            if (
                                in_array($checkAdminExists->type, $officers)
                            ) {
                                if (isset($officers[$officerId])) {
                                    $pixdb->update(
                                        'admins',
                                        ['id' => $checkAdminExists->id],
                                        ['type' => $officers[$officerId]]
                                    );
                                } else {
                                    $pixdb->delete(
                                        'admins',
                                        ['id' => $checkAdminExists->id]
                                    );
                                }
                            }
                        } else {
                            $newAdminId = false;
                            if (isset($officers[$officerId])) {
                                $newAdminId = $pixdb->insert(
                                    'admins',
                                    [
                                        'type' => $officers[$officerId],
                                        'enabled' => 'Y',
                                        'email' => $memberData->email,
                                        'name' => $memberData->firstName . ' ' . $memberData->lastName,
                                        'userName' => strtolower(str_replace(' ', '', $memberData->firstName)),
                                        'memberid' => $memberData->id
                                    ],
                                    true
                                );
                            }
                            if ($newAdminId) {
                                $getSection = $pixdb->getRow(
                                    'chapters',
                                    ['id' => $section->secId],
                                    'id,
                                    name'
                                );
                                $getOfficerTitle = $pixdb->getRow(
                                    'officers_titles',
                                    ['id' => $officerId],
                                    'id,
                                    title'
                                );
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
                                    $tplInfo->body = 'We are excited to inform you that you have been officially selected as the ' . "$getOfficerTitle->title for $getSection->name" . '.<br/> 
                            This is a significant role, and were confident that your leadership will make a meaningful impact within the community.';
                                }
                                $mlArgs = [
                                    'CONTENT' => nl2br($tplInfo->body ?? ''),
                                    'BTNTEXT' => nl2br($tplInfo->btntxt ?? 'To begin, please activate your leadership account using the link below:'),
                                    'BTNLABEL' => $tplInfo->btnlabel ?? 'Get Started',
                                ];

                                loadModule('admin-account-verification')->sendVerification(
                                    (object)array(
                                        'id' => $newAdminId,
                                        'email' => $memberData->email,
                                        'name' => $memberData->firstName . ' ' . $memberData->lastName,
                                    ),
                                    null,
                                    false,
                                    'leaders-invite-access',
                                    $mlArgs
                                );
                            }
                        }
                    }
                } else {
                    $r->msg = 'Only one officer per title is allowed.';
                }
            }
        }
    }
} else {
    $r->msg = 'Access Denied!';
}
