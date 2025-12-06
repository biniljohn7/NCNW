<?php
if (!$pix->canAccess('location')) {
    $r->errorMsg = 'Permission denied';
    $pix->json($r);
}

$_ = $_REQUEST;

if (isset($_['id'])) {
    $state = esc($_['id']);
    if ($state) {
        $r->list = $pixdb->get(
            'chapters',
            [
                'state' => $state,
                '#SRT' => 'name asc'
            ],
            'id, name'
        )->data;
        $r->status = 'ok';
        // 
    } else {
        $r->errorMsg = 'state id cannot be empty';
    }
} else {
    $r->errorMsg = 'Missing required params';
}
