<?php
if (!$pix->canAccess('elect')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_REQUEST;

if (
    isset($_['secId'], $_['ldrid'], $_['typ'])
) {
    $secId = esc($_['secId']);
    $ldrid = esc($_['ldrid']);
    $typ = esc($_['typ']);

    if (
        $secId &&
        $ldrid &&
        $typ
    ) {
        $type = '';
        $typ == 'L' ? $type = 'leader' : 0;
        $typ == 'P' ? $type = 'president' : 0;

        $data = $pixdb->get(
            'section_leaders',
            [
                'mbrId' => $ldrid,
                'secId' => $secId,
                'single' => 1
            ]
        );
        if (
            $data &&
            $data->type == $type
        ) {
            $pixdb->delete(
                'section_leaders',
                [
                    'mbrId' => $ldrid,
                    'secId' => $secId
                ]
            );

            $usrRoles = $pixdb->get(
                'members',
                [
                    'id' => $ldrid,
                    'single' => 1
                ],
                'email,role'
            );

            $rmvRol = [];
            if ($type == 'leader') {
                $rmvRol[] = 'section-leader';
            } elseif ($type == 'president') {
                $rmvRol[] = 'section-president';
            }

            $rolesArray = explode(',', $usrRoles->role);
            $updatedRoles = array_diff($rolesArray, $rmvRol);

            $updatedRolesString = implode(',', $updatedRoles);

            $pixdb->update(
                'members',
                ['id' => $ldrid],
                ['role' => $updatedRolesString != '' ? $updatedRolesString : null]
            );
            //
            $adEmail = $usrRoles->email;
            $adminUser = $pixdb->get(
                'admins',
                [
                    'email' => $adEmail,
                    'single' => 1
                ],
                'id,type,email,perms'
            );
            if ($adminUser) {
                if ($adminUser->type == 'sub-admin') {
                    $perms = array_filter(explode(',', $adminUser->perms));
                    if (
                        !empty($perms) &&
                        in_array('elect', $perms)
                    ) {
                        $perms = array_diff($perms, ['elect']);
                    }
                    $pixdb->update(
                        'admins',
                        [
                            'id' => $adminUser->id,
                        ],
                        [
                            'perms' => implode(',', array_unique($perms)) ?: NULL,
                            'memberid' => NULL
                        ]
                    );
                } else {
                    $pixdb->delete(
                        'admins',
                        [
                            'id' => $adminUser->id,
                            'email' => $adEmail
                        ]
                    );
                }
            }
            //update access token
            $evg->changeAccessToken($ldrid);


            $pix->addmsg('Section ' . $type . ' deleted successfully.', 1);
            $pix->redirect("?page=chapter&sec=details&id=$secId");
        }
    }
}
