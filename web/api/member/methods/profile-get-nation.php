<?php
$nation = $pixdb->get(
    'nations',
    [
        'enabled' => 'Y',
        '#SRT' => 'id'
    ],
    'id, name'
);

$tmp = array();
foreach ($nation->data as $dta) {
    $tmp[] = (object)[
        'nationId' => (int)$dta->id,
        'nationName' => $dta->name
    ];
}

$r->status = 'ok';
$r->success = 1;
$r->data = $tmp;
$r->message = 'Data is retrieved successfully!';
