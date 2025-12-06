<?php
(function ($pix, $pixdb, $evg, $r, $authUser) {
    $_ = $_REQUEST;

    $status = esc($_['status'] ?? 0) == 'true';

    $pixdb->update(
        'members',
        ['id' => $authUser->id],
        ['notifications' => $status ? 'Y' : 'N']
    );

    $r->success = 1;
    $r->status = 'ok';
    $r->message = 'Notifications ' . ($status ? 'en' : 'dis') . 'abled';
    // 
})($pix, $pixdb, $evg, $r, $authUser);
