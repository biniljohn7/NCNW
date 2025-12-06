<?php
$dataArr = array();

###  certification
$certification = $pixdb->get(
    'certification',
    [
        '#SRT' => 'name asc'
    ]
);
foreach ($certification->data as $cer) {
    $dataArr['certification'][] = (object)[
        'profileOptionsId' => (int)$cer->id,
        'name' => $cer->name
    ];
}

###  industry
$industry = $pixdb->get(
    'industry',
    [
        '#SRT' => 'name asc'
    ]
);
foreach ($industry->data as $dta) {
    $dataArr['industry'][] = (object)[
        'profileOptionsId' => (int)$dta->id,
        'name' => $dta->name
    ];
}

###  occupation
$occupation = $pixdb->get(
    'occupation',
    [
        '#SRT' => 'name asc'
    ]
);
foreach ($occupation->data as $dta) {
    $dataArr['occupation'][] = (object)[
        'profileOptionsId' => (int)$dta->id,
        'name' => $dta->name
    ];
}

###  university
$university = $pixdb->get(
    'university',
    [
        '#SRT' => 'name asc'
    ]
);
foreach ($university->data as $dta) {
    $dataArr['university'][] = (object)[
        'profileOptionsId' => (int)$dta->id,
        'name' => $dta->name
    ];
}

###  country
$country = $pixdb->get(
    'nations',
    [
        'enabled' => 'Y',
        '#SRT' => 'name desc'
    ],
    'id, name'
);
foreach ($country->data as $dta) {
    $dataArr['country'][] = (object)[
        'profileOptionsId' => (int)$dta->id,
        'name' => $dta->name
    ];
}

###  affiliation
$affiliation = $pixdb->get(
    'affiliates',
    [
        '#SRT' => 'name asc'
    ]
);
foreach ($affiliation->data as $dta) {
    $dataArr['affiliation'][] = (object)[
        'profileOptionsId' => (int)$dta->id,
        'name' => $dta->name
    ];
}


$r->status = 'ok';
$r->success = 1;
$r->data = $dataArr;
$r->message = 'Data is retrieved successfully!';
