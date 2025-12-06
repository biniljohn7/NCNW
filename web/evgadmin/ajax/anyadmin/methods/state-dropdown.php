<?php
if (!$pix->canAccess('location')) {
    $r->errorMsg = 'Permission denied';
    $pix->json($r);
}

$_ = $_REQUEST;

if (
    isset(
        $_['region']
    )
) {
    $region = esc($_['region'] ?? '');

    if (
        !empty($region)
    ) {
        $sDatas = [];
        $sData = $pixdb->get(
            'states',
            [
                'region' => $region
            ],
            'id, name'
        );
        if ($sData) {
            foreach ($sData->data as $row) {
                $sDatas[] = (object)[
                    'stId' => $row->id,
                    'stName' => $row->name
                ];
            }
            $r->status = 'ok';
            $r->sDatas = $sDatas;
        } else {
            $r->errorMsg = 'Invalid data';
        }
    } else {
        $r->errorMsg = 'nation id cannot be empty';
    }
} else {
    $r->errorMsg = 'Missing required params';
}
