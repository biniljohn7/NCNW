<?php
if ($pix->canAccess('messages')) {
    if (
        isset(
            $_['message'],
            $_['group']
        )
    ) {
        $message = esc($_['message']);
        $group = esc($_['group']);
        if (
            $message &&
            $group
        ) {
            $getGp = $pixdb->getRow(
                'message_groups',
                ['id' => $group],
                'id'
            );

            $chat = loadModule('chat');

            if ($getGp) {

                require $pix->basedir . 'lib/gp-messages.php';

                $post = $pixGpMessages->post(
                    $group,
                    $message
                );

                if ($post->posted) {
                    $r->status = 'ok';
                    $r->msg = $message;
                    $r->time = $chat->getDateTime($datetime);
                    $r->shortTime = $chat->getShortTime($datetime);
                    $r->mkey = $post->mkey;
                } else {
                    $r->errorMsg = $post->errorMsg;
                }
            }
        }
    }
} else {
    $r->msg = 'Access Denied!';
}
