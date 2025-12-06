<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_REQUEST;

if (
    isset($_['stid'], $_['ldrid'])
) {
    $stid = esc($_['stid']);
    $ldrid = esc($_['ldrid']);

    if (
        $stid &&
        $ldrid
    ) {
        $data = $pixdb->get(
            'state_leaders',
            [
                'mbrId' => $ldrid,
                'stateId' => $stid,
                'single' => 1
            ]
        );
        if ($data) {
            $pixdb->delete(
                'state_leaders',
                [
                    'mbrId' => $ldrid,
                    'stateId' => $stid
                ]
            );

            $usrRoles = $pixdb->get(
                'members',
                [
                    'id' => $ldrid,
                    'single' => 1
                ],
                'email, role'
            );

            $rolesArray = explode(',', $usrRoles->role);
            $updatedRoles = array_diff($rolesArray, ['state-leader']);

            $updatedRolesString = implode(',', $updatedRoles);

            $pixdb->update(
                'members',
                ['id' => $ldrid],
                ['role' => $updatedRolesString != '' ? $updatedRolesString : null]
            );

            $adEmail = $usrRoles->email;
            $adminUser = $pixdb->get(
                'admins',
                [
                    'email' => $adEmail,
                    'single' => 1
                ],
                'id,type,email,perms'
            );
            if($adminUser) {
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

            $pix->addmsg('State leader deleted successfully.', 1);
            $pix->redirect("?page=state&sec=details&id=$stid");
        }
    }
}
