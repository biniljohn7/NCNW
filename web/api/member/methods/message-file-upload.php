<?php
if (
    $lgUser->verified == 'Y' &&
    isset(
        $_['recipient'],
        $_FILES['image']
    )
) {
    $recipient = esc($_['recipient']);
    $image = $_FILES['image'];

    if (
        preg_match('/\.(jpe*g|png|gif)$/i', $image['name']) &&
        $recipient
    ) {
        $rcpUser = $pixdb->get(
            'members',
            array(
                'id' => $recipient,
                'verified' => 'Y',
                'single' => 1
            ),
            'id'
        );

        $chat = loadModule('chat');

        if ($rcpUser) {
            require $pix->basedir . 'lib/lib-messages.php';

            $post = $pixMessages->uploadImg(
                $lgUser->id,
                $recipient,
                $image
            );

            if ($post->upload) {
                $r->status = 'ok';
                $r->success = 1;
                $r->data = (object)[
                    'id' => $recipient,
                    'msgImg' => $post->thumb,
                    'time' => $chat->getDateTime($datetime),
                    'shortTime' => $chat->getShortTime($datetime),
                    'mkey' => $post->mkey,
                    'isSent' => true,
                ];
                $r->message = 'Done! Message Send';
            } else {
                $r->message = $post->errorMsg;
            }
        }
    }
}
