<?php
if (!$pix->canAccess('members')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $evg) {
    $_ = $_POST;

    if (
        isset(
            $_['id'],
            $_['cfm']
        )
    ) {
        $id = esc($_['id']);
        $cfm = esc($_['cfm']);

        if (
            $id &&
            stripos($cfm, 'yes') !== false
        ) {
            $evg->removeMember($id);
            $pix->addmsg('Member removed', 1);
            $pix->redirect('?page=members');
        }
    }
})($pix, $evg);
