<?php
if ($pix->canAccess('messages')) {
    if (
        isset(
            $_['groupId'],
            $_FILES['image']
        )
    ) {
        $groupId = esc($_['groupId']);
        $image = $_FILES['image'];

        if (
            preg_match('/\.(jpe*g|png|gif)$/i', $image['name']) &&
            $groupId
        ) {
            $getGp = $pixdb->getRow(
                'message_groups',
                ['id' => $groupId],
                'id'
            );

            if ($getGp) {
                $chat = loadModule('chat');
                require $pix->basedir . 'lib/gp-messages.php';

                $post = $pixGpMessages->uploadImg(
                    $groupId,
                    $image
                );

                if ($post->upload) {
                    $r->status = 'ok';
                    $r->photo = $post->photo;
                    $r->thumb = $post->thumb;
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
