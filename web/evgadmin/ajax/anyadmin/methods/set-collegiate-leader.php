<?php
$_ = $_REQUEST;

if (isset(
    $_['mId'],
    $_['csId']
)) {

    $mId = intval($_['mId']);
    $csId = intval($_['csId']);

    if (
        $mId &&
        $csId
    ) {
        $valid = 1;

        $csData = $pixdb->get(
            'collegiate_leaders',
            ['coliId' => $csId]
        );

        if ($csData->data) {
            if (count($csData->data) >= 1) {
                $valid = 0;
                $r->msg = 'A leader has already been added.';
            }
            if ($valid) {
                foreach ($csData->data as $info) {
                    if ($info->mbrId == $mId) {
                        $valid = 0;
                        $r->msg = 'The member is already added.';
                    }
                }
            }
        }

        if ($valid) {
            $pixdb->insert(
                'collegiate_leaders',
                [
                    'mbrId' => $mId,
                    'coliId' => $csId
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
            $rolesArray[] = 'collegiate-leaders';
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
