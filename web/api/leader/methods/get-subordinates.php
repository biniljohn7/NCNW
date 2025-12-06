<?php
devMode();

$res = $evg->getLeaderCircles($lgUser->id, $userRoles);
$sections = $res->sections;
$affiliations = $res->affiliates;
$collegiates = $res->colgSectns;

$pgn = max(0, intval($_GET['pgn'] ?? 0));

$shConds = [
    '#SRT' => 'firstName asc',
    '__page' => $pgn - 1,
    '__limit' => 20,
    '__QUERY__' => []
];

$mnCond = "mm.id != $lgUser->id";

$searchCond = '';
$search = esc($_GET['search'] ?? '');
if ($search) {
    $qSearch = q("%$search%");
    $searchCond = "(concat(firstName, ' ', lastName) LIKE $qSearch OR email LIKE $qSearch OR memberId LIKE $qSearch)";
}

$roleConds = [];

if (!empty($sections)) {
    $roleConds[] = "mi.cruntChptr IN (" . implode(',', $sections) . ")";
}

if (!empty($affiliations)) {
    $roleConds[] = "mi.affilateOrgzn IN (" . implode(',', $affiliations) . ")";
}

if (!empty($collegiates)) {
    $roleConds[] = "mi.collegiateSection IN (" . implode(',', $collegiates) . ")";
}

$membCond = $mnCond;

if ($searchCond) {
    $membCond .= " AND $searchCond";
}

if (!empty($roleConds)) {
    $membCond .= " AND (" . implode(' OR ', $roleConds) . ")";
}

$shConds['__QUERY__'][] = $membCond;

$members = $pixdb->get(
    [
        ['members', 'mm', 'id'],
        ['members_info', 'mi', 'member']
    ],
    $shConds
);


if($members) {
    $secIds = [];
    $affIds = [];

    foreach($members->data as $row) {
        if($row->cruntChptr) {
            $secIds[] = $row->cruntChptr;
        }
        if($row->affilateOrgzn) {
            $affIds[] = $row->affilateOrgzn;
        }
    }

    $secData = $evg->getChapters($secIds,'id, name');
    $affData = $evg->getAffiliations($affIds,'id, name');

    foreach ($members->data as $mbr) {
        $mbr->secName = $secData[$mbr->cruntChptr]->name ?? null;
        $mbr->affName = $affData[$mbr->affilateOrgzn]->name ?? null;
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
var_dump($r->data);
// exit;
?>
