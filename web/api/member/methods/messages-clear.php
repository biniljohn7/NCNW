<?php
if (isset($_['member'])) {
    $id = esc($_['member']);

    if ($id) {
        require $pix->basedir . 'lib/lib-messages.php';

        $pixMessages->clear(
            $lgUser->id,
            $id
        );

        $r->status = 'ok';
        $r->success = 1;
        $r->message = 'Done! Chat deleted';
    }
}
