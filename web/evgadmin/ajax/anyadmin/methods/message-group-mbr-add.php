<?php
$_ = $_REQUEST;

if (
    isset(
        $_['mbrId'],
        $_['grpId']
    )
) {
    $mbrId = intval($_['mbrId']);
    $grpId = intval($_['grpId']);

    if (
        $mbrId &&
        $grpId
    ) {

        $exist = $pixdb->get(
            'message_group_members',
            [
                'groupId' => $grpId,
                'member' => $mbrId,
                'single' => 1
            ]
        );

        if (!$exist) {
            $validGrp = $pixdb->get(
                'message_groups',
                [
                    'id' => $grpId,
                    'single' => 1
                ],
                'id'
            );

            $validMbr = $pixdb->get(
                'members',
                [
                    'id' => $mbrId,
                    'single' => 1
                ],
                'id'
            );

            if (
                $validGrp &&
                $validMbr
            ) {

                $pixdb->insert(
                    'message_group_members',
                    [
                        'groupId' => $grpId,
                        'member' => $mbrId
                    ]
                );

                $r->status = 'ok';
            } else {
                $r->msg = 'The member or group does not exist.';
            }
        } else {
            $r->msg = 'Member already added.';
        }
    }
}
