<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if (isset(
    $pl->partner,
    $pl->id
)) {
    $partner = esc($pl->partner);
    $id = array_unique(array_filter(array_map('esc', $pl->id)));

    if (
        $partner &&
        !empty($id)
    ) {
        require $pix->basedir . 'lib/lib-messages.php';

        $pixMessages->deleteMessage(
            $lgUser->id,
            $partner,
            $id
        );

        $r->status = 'ok';
        $r->success = 1;
        $r->message = 'Messages deleted successfully';
    }
}
