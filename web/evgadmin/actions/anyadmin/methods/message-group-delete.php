<?php
if (!$pix->canAccess('messages')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_GET;

if (
    isset($_['id'])
) {
    $id = intval($_['id']);

    if ($id) {
        $data = $pixdb->get(
            'message_groups',
            [
                'id' => $id,
                'single' => 1
            ]
        );

        if ($data) {

            ///  delete messages here


            $pixdb->delete(
                'message_group_members',
                [
                    'groupId' => $id
                ]
            );

            $pixdb->delete(
                'message_groups',
                [
                    'id' => $id
                ]
            );

            $pix->addmsg('Message group deleted successfully.', 1);
            $pix->redirect('?page=groups');
        }
    }
}
