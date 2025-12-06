<?php
if (!$pix->canAccess('location')) {
    $r->errorMsg = 'Permission denied';
    $pix->json($r);
}

$_ = $_POST;

if (
    isset(
        $_['nation']
    )
) {
    $nation = esc($_['nation'] ?? '');

    if (
        !empty($nation)
    ) {
        $rDatas = [];
        $rData = $pixdb->get(
            'regions',
            [
                'nation' => $nation
            ],
            'id,name'
        );
        if ($rData) {
            foreach ($rData->data as $row) {
                $rDatas[] = (object)[
                    'regId' => $row->id,
                    'regName' => $row->name
                ];
            }
            $r->status = 'ok';
            $r->rDatas = $rDatas;
        } else {
            $r->errorMsg = 'Invalid data';
        }
    } else {
        $r->errorMsg = 'nation id cannot be empty';
    }
} else {
    $r->errorMsg = 'Missing required params';
}
// exit;
