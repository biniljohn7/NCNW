<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if (
    $lgUser->verified == 'Y' &&
    isset(
        $pl->message,
        $pl->recipient
    )
) {
    $message = esc($pl->message);
    $recipient = esc($pl->recipient);

    if (
        $message &&
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

            $post = $pixMessages->post(
                $lgUser->id,
                $recipient,
                $message
            );

            if ($post->posted) {
                $r->status = 'ok';
                $r->success = 1;
                $r->data = (object)[
                    'id' => $recipient,
                    'text' => $message,
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
