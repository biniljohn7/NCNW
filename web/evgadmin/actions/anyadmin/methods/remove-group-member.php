<?php
$_ = $_REQUEST;

if (
    isset(
        $_['group-id'],
        $_['member']
    )
) {
    $group = intval($_['group-id']);
    $member = intval($_['member']);

    if (
        $group &&
        $member
    ) {
        $check = $pixdb->getrow(
            'message_group_members',
            [
                'groupId' => $group,
                'member' => $member
            ]
        );
        if ($check) {
            $pixdb->delete(
                'message_group_members',
                [
                    'groupId' => $group,
                    'member' => $member
                ]
            );
            $pix->addmsg('Member removed from group successfully', 1);
            $pix->redirect();
        }
    }
}
//exit;
