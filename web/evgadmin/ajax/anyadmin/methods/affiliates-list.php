<?php

// $key = isset($_['key']) ? esc($_['key']) : null;

$filterArr =  [
    'enabled' => 'Y',
    '#SRT' => 'id'
];

// if ($key) {
//     $filterArr['#QRY'] = "name like '%" . $key . "%'";
// }

$affiliates = $pixdb->get(
    'affiliates',
    $filterArr,
    'id,
    name'
);

$tmp = array();
foreach ($affiliates->data as $dta) {
    $tmp[] = (object)[
        'affiliateId' => (int)$dta->id,
        'affiliateName' => $dta->name
    ];
}

$r->status = 'ok';
$r->success = 1;
$r->data = $tmp;
$r->message = 'Data is retrieved successfully!';
