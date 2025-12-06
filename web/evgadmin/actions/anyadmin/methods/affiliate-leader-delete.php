<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_REQUEST;

if (
    isset($_['affid'], $_['ldrid'])
) {
    $affid = esc($_['affid']);
    $ldrid = esc($_['ldrid']);

    if (
        $affid &&
        $ldrid
    ) {
        $data = $pixdb->get(
            'affiliate_leaders',
            [
                'mbrId' => $ldrid,
                'affId' => $affid,
                'single' => 1
            ]
        );

        if ($data) {
            $pixdb->delete(
                'affiliate_leaders',
                [
                    'mbrId' => $ldrid,
                    'affId' => $affid
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
            $updatedRoles = array_diff($rolesArray, ['affiliate-leader']);

            $updatedRolesString = implode(',', $updatedRoles);

            $pixdb->update(
                'members',
                ['id' => $ldrid],
                ['role' => $updatedRolesString != '' ? $updatedRolesString : null]
            );
            //update access token
            $evg->changeAccessToken($ldrid);

            $pix->addmsg('Affiliate leader deleted successfully.', 1);
            $pix->redirect("?page=affiliates&sec=details&id=$affid");
        }
    }
}
