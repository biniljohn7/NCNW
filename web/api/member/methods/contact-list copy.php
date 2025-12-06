<?php
$r->status = 'ok';
$r->success = 1;
$list = [];

$srh = have($_['search'], null);
$pgn = have($_['pageId'], 1);
$pageLimit = 10;

$listCnds = [
    'verified' => 'Y',
    '__QUERY__' => ['id!=' . $lgUser->id],
    '#SRT' => 'firstName asc, lastName asc',
    '__page' => $pgn - 1,
    '__limit' => $pageLimit
];

if ($srh) {
    $qSearch = q("%$srh%");
    $listCnds['__QUERY__'][] = "(
        concat(firstName, ' ', lastName) like $qSearch OR 
        email like $qSearch OR
        memberId like $qSearch
    )";
}

$members = $pixdb->get(
    'members',
    $listCnds,
    'id as memberId,
    firstName,
    lastName,
    email,
    avatar'
);

foreach ($members->data as $mm) {
    $mm->fullName = html_entity_decode($mm->firstName . ' ' . $mm->lastName, ENT_QUOTES, 'UTF-8');
    $mm->phoneCode = null;
    $mm->phoneNumber = null;
    $mm->profileImage = $mm->avatar ? $pix->avatar($mm->avatar, '150x150', 'avatars') : null;
    unset($mm->firstName, $mm->lastName, $mm->avatar);
}

$r->data = (object)[
    'list' => $members->data,
    'currentPageNo' => $members->current + 1,
    'totalPages' => $members->pages
];

$r->message = 'Data Retrieved Successfully!';
