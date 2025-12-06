<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_REQUEST;

if (
    isset($_['csId'], $_['ldrid'])
) {
    $csId = esc($_['csId']);
    $ldrid = esc($_['ldrid']);

    if (
        $csId &&
        $ldrid
    ) {
        $data = $pixdb->get(
            'collegiate_leaders',
            [
                'mbrId' => $ldrid,
                'coliId' => $csId,
                'single' => 1
            ]
        );

        if ($data) {
            $pixdb->delete(
                'collegiate_leaders',
                [
                    'mbrId' => $ldrid,
                    'coliId' => $csId
                ]
            );

            $usrRoles = $pixdb->get(
                'members',
                [
                    'id' => $ldrid,
                    'single' => 1
                ],
                'role'
            );

            $rolesArray = explode(',', $usrRoles->role);
            $updatedRoles = array_diff($rolesArray, ['collegiate-leaders']);

            $updatedRolesString = implode(',', $updatedRoles);

            $pixdb->update(
                'members',
                ['id' => $ldrid],
                ['role' => $updatedRolesString != '' ? $updatedRolesString : null]
            );
            //update access token
            $evg->changeAccessToken($ldrid);

            $pix->addmsg('Collegiate leader deleted successfully.', 1);
            $pix->redirect("?page=collegiate-sections&sec=details&id=$csId");
        }
    }
}
