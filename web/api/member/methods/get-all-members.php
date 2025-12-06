<?php
$inp = getJsonBody();

$key = isset($inp->key) ? esc($inp->key) : null;
$pgn = isset($inp->pgn) ? intval($inp->pgn) : 0;

$pageLimit = 20;

$filterArr =  [
    '#QRY' => 'id != ' . $lgUser->id,
    '__page' => $pgn - 1,
    '__limit' => $pageLimit,
    '__QUERY__' => []
];

if ($key) {
    $filterArr['__QUERY__'][] = "(firstName like '%" . $key . "%' or lastName like '%" . $key . "%')";
}

$members = $pixdb->get(
    'members',
    $filterArr,
    'id,
    firstName,
    lastName,
    email,
    avatar'
);

if ($members) {
    foreach ($members->data as $mbr) {
        $mbr->avatar = $mbr->avatar ? $pix->domain . 'uploads/avatars/' . $pix->thumb($mbr->avatar, '150x150') : null;
        $mbr->name = $mbr->firstName . ' ' . $mbr->lastName;
    }
    $r->status = 'ok';
    $r->success = 1;
    $r->message = 'Viewed Successfully!';

    $r->data = (object)[
        'list' => $members->data,
        'currentPageNo' => $members->current + 1,
        'totalPages' => $members->pages
    ];
}
