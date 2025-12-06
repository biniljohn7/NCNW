<?php

$key = isset($_['key']) ? esc($_['key']) : null;

$filterArr =  [
    'enabled' => 'Y',
    '#SRT' => 'name ASC'
];

if ($key) {
    $filterArr['#QRY'] = "name like '%" . $key . "%'";
}

$sections = $pixdb->get(
    'chapters',
    $filterArr,
    'id,
    name'
);

$tmp = array();
foreach ($sections->data as $dta) {
    $tmp[] = (object)[
        'sectionId' => (int)$dta->id,
        'sectionName' => $dta->name
    ];
}

$r->status = 'ok';
$r->success = 1;
$r->data = $tmp;
$r->message = 'Data is retrieved successfully!';
