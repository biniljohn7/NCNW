<?php
if (isset($_['id'])) {
    $pgn = isset($_['pgn']) ? max(0, intval($_['pgn'])) : 0;
    $msngr = esc($_['id']);


    if ($msngr) {
        if (preg_match('/^gp_(\d+)$/', $msngr, $matches)) {
            $groupId = (int)$matches[1];
            require $pix->basedir . 'lib/gp-messages.php';

            //load history
            $msgData = $pixGpMessages->getChat(
                $lgUser->id,
                $groupId,
                $pgn
            );

            $r->status = 'ok';
            $r->success = 1;
            $r->data = (object)[
                'id' => $msngr,
                'messages' => $msgData->chats,
                'totalPages' => $msgData->totalPages
            ];
            $r->message = 'Done!';
        } else {
            require $pix->basedir . 'lib/lib-messages.php';

            //load history
            $msgData = $pixMessages->getChat(
                $lgUser->id,
                $msngr,
                $pgn
            );

            $r->status = 'ok';
            $r->success = 1;
            $r->data = (object)[
                'id' => $msngr,
                'messages' => $msgData->chats,
                'totalPages' => $msgData->totalPages
            ];
            $r->message = 'Done!';
        }
    }
}
