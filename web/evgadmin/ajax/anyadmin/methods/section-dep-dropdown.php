<?php
if (!$pix->canAccess('location')) {
    $r->errorMsg = 'Permission denied';
    $pix->json($r);
}

$_ = $_REQUEST;

if (
    isset(
        $_['state']
    )
) {
    $state = esc($_['state'] ?? '');

    if (
        !empty($state)
    ) {
        $sDatas = [];
        $sData = $pixdb->get(
            'chapters',
            [
                'state' => $state
            ],
            'id, name'
        );
        if ($sData) {
            foreach ($sData->data as $row) {
                $sDatas[] = (object)[
                    'scId' => $row->id,
                    'scName' => $row->name
                ];
            }
            $r->status = 'ok';
            $r->sDatas = $sDatas;
        } else {
            $r->errorMsg = 'Invalid data';
        }
    } else {
        $r->errorMsg = 'state id cannot be empty';
    }
} else {
    $r->errorMsg = 'Missing required params';
}
