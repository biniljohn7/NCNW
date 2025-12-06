<?php
(function ($pix, $pixdb, $evg, $r, $authUser) {

    $getNoti = $pixdb->getRow(
        'members',
        ['id' => $authUser->id],
        'id, 
        notifications'
    );

    $tmp = '';
    $tmp = explode(',', $getNoti->notifications ?? '');

    $r->status = 'ok';
    $r->success = 1;
    $r->data = $tmp;
    $r->message = 'Data is retrieved successfully!';
})($pix, $pixdb, $evg, $r, $authUser);
?>