<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

$r->status = 'ok';
$r->success = 1;

$srh = have($pl->search, null);
$pgn = have($pl->pageId, 1);
$pageLimit = 20;

$typeIds = [];
$types = new stdClass();
$carrCnds = [
    'enabled' => 'Y',
    '__page' => $pgn - 1,
    '__limit' => $pageLimit
];

if ($srh) {
    $carrCnds['#QRY'] = '(title like "%' . $srh . '%" OR type IN (SELECT id FROM career_types WHERE name like "%' . $srh . '%"))';
}

$careers = $pixdb->get(
    'careers',
    $carrCnds,
    'id,
    title,
    type,
    source,
    address,
    description,
    image'
);

foreach ($careers->data as $itm) {
    $typeIds[] = $itm->type;
}
$typeIds = array_unique(array_filter(array_map('intval', $typeIds)));
if (!empty($typeIds)) {
    $types = $pixdb->fetchAssoc(
        'career_types',
        [
            'id' => $typeIds
        ],
        'id,
        name',
        'id'
    );
}

foreach ($careers->data as $itm) {
    $itm->careerType = isset($types[$itm->type]) ? $types[$itm->type]->name : null;
    $itm->careerId = $itm->id;
    $itm->sourceOfCareer = ucfirst($itm->source);
    unset($itm->type, $itm->id, $itm->source);
}

$r->data = (object)[
    'list' => $careers->data,
    'currentPageNo' => $careers->current + 1,
    'totalPages' => $careers->pages
];

$r->message = 'Data Retrieved Successfully!';
