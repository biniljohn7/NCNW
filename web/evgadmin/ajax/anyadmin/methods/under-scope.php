<?php
if (!$pix->canAccess('advocacy')) {
    $r->errorMsg = 'Permission denied';
    $pix->json($r);
}

$_ = $_POST;

if (
    isset(
        $_['scope']
    )
) {
    $scope = esc($_['scope'] ?? '');
    $scopes = [
        'national' => 'nations',
        'regional' => 'regions',
        'state' => 'states',
        'chapter' => 'sections'
    ];

    if (
        $scope &&
        isset($scopes[$scope])
    ) {
        $spDatas = [];
        $spData = $pixdb->get(
            $scopes[$scope],
            [
                '#SRT' => 'id asc'
            ],
            'id, name'
        );
        if ($spData) {
            foreach ($spData->data as $row) {
                $spDatas[] = (object)[
                    'spId' => $row->id,
                    'spName' => $row->name
                ];
            }
            $r->status = 'ok';
            $r->spDatas = $spDatas;
            $r->scope = ucfirst($scopes[$scope]);
        } else {
            $r->errorMsg = 'Invalid spData';
        }
    } else {
        $r->errorMsg = 'scope cannot be empty';
    }
} else {
    $r->errorMsg = 'Missing required params';
}
// exit;
