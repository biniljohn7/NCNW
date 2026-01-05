<?php
devMode();
$_ = $_REQUEST;

$key = isset($_['key']) ? esc($_['key']) : '';
$pgn = isset($_['pgn']) ? intval($_['pgn']) : 0;
$state = isset($_['state']) ? intval($_['state']) : 0;
$chptr = isset($_['chptr']) ? intval($_['chptr']) : 0;
$affId = isset($_['affId']) ? intval($_['affId']) : 0;
$csId = isset($_['csId']) ? intval($_['csId']) : 0;

$pageLimit = 20;

$filterArr =  [
    'mm.enabled' => 'Y',
    '__page' => $pgn,
    '__limit' => $pageLimit,
    '__QUERY__' => []
];

if ($state) {
    $filterArr['mi.state'] = $state;
}

if ($chptr) {
    $filterArr['mi.cruntChptr'] = $chptr;
}

if ($affId) {
    $filterArr['__QUERY__'][] = 'mm.id in (select member from members_affiliation where affiliation = ' . $affId . ')';
}

if ($csId) {
    $filterArr['mi.collegiateSection'] = $csId;
}

if ($key && $key != '') {
    $qSearch = q("%$key%");
    $filterArr['__QUERY__'][] = "(concat(mm.firstName, ' ', mm.lastName) LIKE $qSearch OR mm.email LIKE $qSearch OR mm.memberId LIKE $qSearch)";
}

$members = $pixdb->get(
    [
        ['members', 'mm', 'id'],
        ['members_info', 'mi', 'member']
    ],
    $filterArr,
    'mm.id,
    mm.firstName,
    mm.lastName,
    mm.email,
    mm.avatar,
    mm.memberId,
    mi.cruntChptr,
    mi.collegiateSection,
    mi.address,
    mi.address2,
    mi.city,
    mi.zipcode,
    mi.state'
);

if ($members) {
    $membIds = [];
    $crntChapters = [];

    foreach ($members->data as $row) {
        if ($row->id) {
            $membIds[] = $row->id;
        }
        if ($row->cruntChptr) {
            $crntChapters[] = $row->cruntChptr;
        }
    }

    $membIds = array_unique($membIds);
    $crntChapters = array_unique($crntChapters);

    $chapters = $pixdb->fetchAssoc(
        'chapters',
        ['id' => $crntChapters],
        'id,name',
        'id'
    );

    $cnd = [
        'h.enabled' => 'Y',
        '#QRY' => "(h.expiry >= '$date' OR h.expiry IS NULL)"
    ];

    if ($membIds) {
        $cnd['m.id'] = $membIds;
    }

    $memberships = $pixdb->get(
        [
            ['memberships', 'h', 'member'],
            ['members', 'm', 'id']
        ],
        $cnd,
        'm.id as member,
        h.planName'
    )->data;

    $membershipsArr = [];
    if ($memberships) {
        foreach ($memberships as $row) {
            $membershipsArr[$row->member] = $row;
        }
    }

    foreach ($members->data as $mbr) {
        $mbr->membership = $membershipsArr[$mbr->id]->planName ?? null;
        $mbr->avatar = $mbr->avatar ? $pix->domain . 'uploads/avatars/' . $pix->thumb($mbr->avatar, '150x150') : null;
        $mbr->name = $mbr->firstName . ' ' . $mbr->lastName;
        $mbr->secName = $chapters[$mbr->cruntChptr]->name ?? '';
    }
    $r->status = 'ok';
    $r->list = $members->data;
    $r->currentPageNo = $members->current + 1;
    $r->totalPages = $members->pages;
}
//var_dump($members->data);
// exit;
