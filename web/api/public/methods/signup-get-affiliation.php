<?php
$affiliation = $pixdb->get(
    'affiliates',
    [
        'enabled' => 'Y',
        '#SRT' => 'name ASC'
    ],
    'id, name'
);

$tmp = array();
foreach ($affiliation->data as $dta) {
    $tmp[] = (object)[
        'value' => (int)$dta->id,
        'label' => $dta->name
    ];
}

$r->status = 'ok';
$r->success = 1;
$r->data = $tmp;
$r->message = 'Data is retrieved successfully!';
?>