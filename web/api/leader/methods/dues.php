<?php
devMode();
$res = $evg->getLeaderCircles($lgUser->id, $userRoles);
$sections = $res->sections;
$affiliations = $res->affiliates;
$collegiates = $res->colgSectns;


$cnd = [
    '__QUERY__' => [],
    '__page' => 0,
    '__limit' => '10'
];
$adnlqry = [];

$shKey = esc($_GET['search'] ?? '');
if ($shKey) {
    $qSearch = q("%$shKey%");
    $cnd['__QUERY__'][] = "(concat(firstName, ' ', lastName) LIKE $qSearch OR email LIKE $qSearch OR memberId LIKE $qSearch)";
}

if ($sections) {
    $adnlqry[] = "i.cruntChptr IN (" . implode(',', $sections) . ")";
}

if ($affiliations) {
    $adnlqry[] = 'm.id in (select member from members_affiliations where affiliation in (' . implode(',', $affiliations) . '))';
}

if ($collegiates) {
    $adnlqry[] = "i.collegiateSection IN (" . implode(',', $collegiates) . ")";
}

if (!empty($adnlqry)) {
    $cnd['__QUERY__'][] = '(' . implode(' OR ', $adnlqry) . ')';
}

$memberIds = $pixdb->getCol(
    [
        ['members', 'm', 'id'],
        ['members_info', 'i', 'member']
    ],
    $cnd,
    'id'
);
$dues = $evg->getDues($memberIds);
$members = $evg->getMemberInfo($memberIds, ['id', 'firstName', 'lastName', 'memberId'], ['city', 'zipcode']);

foreach ($members as $mmbr) {
    $mmbr->dues = $dues[$mmbr->id] ?? [];
}

$r->data->list = $members;
$r->success = 1;
$r->status = 'ok';
$r->message = 'Membership details loaded!';
