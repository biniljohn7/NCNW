<?php
if (isset(
    $_['groupId'],
    $_['id']
)) {
    $groupId = esc($_['groupId']);
    $id = esc($_['id']);
    if (
        $groupId &&
        $id
    ) {
        require $pix->basedir . 'lib/gp-messages.php';

        $del = $pixGpMessages->deleteMessage(
            $groupId,
            $id
        );
        $r->status = 'ok';
        $r->lastMsg = $del->lastMsg;
        $r->time = $del->time;
        $r->shortTime = $del->shortTime;
    }
}
