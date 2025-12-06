<?php
$r->messages = array();

if (isset($_POST['id'])) {
    $pgn = isset($_['pgn']) ? max(0, intval($_['pgn'])) : 0;
    $gpId = intval($_POST['id']);


    if ($gpId) {
        require $pix->basedir . 'lib/gp-messages.php';

        //load history
        $msgData = $pixGpMessages->getChat(
            null,
            $gpId,
            $pgn
        );
        $r->status = 'ok';
        $r->id = $gpId;
        $r->messages = $msgData->chats;
        $r->totalPages = $msgData->totalPages;
    }
}
