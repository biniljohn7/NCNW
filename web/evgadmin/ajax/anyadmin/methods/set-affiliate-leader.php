<?php
$_ = $_REQUEST;

if (isset(
    $_['mId'],
    $_['affId']
)) {

    $mId = intval($_['mId']);
    $affId = intval($_['affId']);

    if (
        $mId &&
        $affId
    ) {
        $valid = 1;

        $affData = $pixdb->get(
            'affiliate_leaders',
            ['affId' => $affId]
        );

        if ($affData->data) {
            if (count($affData->data) >= 1) {
                $valid = 0;
                $r->msg = 'A leader has already been added.';
            }
            if ($valid) {
                foreach ($affData->data as $info) {
                    if ($info->mbrId == $mId) {
                        $valid = 0;
                        $r->msg = 'The member is already added.';
                    }
                }
            }
        }

        if ($valid) {
            $pixdb->insert(
                'affiliate_leaders',
                [
                    'mbrId' => $mId,
                    'affId' => $affId
                ]
            );

            $usrRoles = $pixdb->get(
                'members',
                [
                    'id' => $mId,
                    'single' => 1
                ],
                'role'
            );

            $rolesArray = explode(',', $usrRoles->role ?? '');
            $rolesArray[] = 'affiliate-leader';
            $rolesArray = array_filter($rolesArray);

            $pixdb->update(
                'members',
                ['id' => $mId],
                ['role' => !empty($rolesArray) ? implode(',', $rolesArray) : null]
            );
            //update access token
            $evg->changeAccessToken($mId);

            $r->status = 'ok';
        }
    }
}
