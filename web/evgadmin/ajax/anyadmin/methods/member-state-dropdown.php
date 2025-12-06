<?php
if (!$pix->canAccess('location')) {
    $r->errorMsg = 'Permission denied';
    $pix->json($r);
}

$_ = $_REQUEST;

if (
    isset(
        $_['nation']
    )
) {
    $nation = esc($_['nation'] ?? '');

    if (
        !empty($nation)
    ) {
        $stData = [];
        $sData = $pixdb->get(
            'states',
            [
                'nation' => $nation
            ],
            'id, name'
        );
        if ($sData) {
            foreach ($sData->data as $row) {
                $stData[] = (object)[
                    'stateId' => $row->id,
                    'stateName' => $row->name
                ];
            }
            $r->status = 'ok';
            $r->stData = $stData;
        } else {
            $r->errorMsg = 'Invalid data';
        }
    } else {
        $r->errorMsg = 'nation id cannot be empty';
    }
} else {
    $r->errorMsg = 'Missing required params';
}
