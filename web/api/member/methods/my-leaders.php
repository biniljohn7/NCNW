<?php
$ownCircles = [];
$teamData = [];
// member's affiliate leaders
$mmbrAffiliates = $pixdb->getCol(
    'members_affiliation',
    ['member' => $lgUser->id],
    'affiliation'
);
if ($mmbrAffiliates) {
    foreach ($mmbrAffiliates as $aff) {
        $ownCircles[] = [
            'circle' => 'affiliate',
            'circleId' => $aff
        ];
    }
    $hvLeaders = $pixdb->get(
        'affiliate_leaders',
        ['affId' => $mmbrAffiliates]
    )->data;
    foreach ($hvLeaders as $row) {
        $teamData[] = (object)[
            'title' => 'Affiliate Leader',
            'memberId' => $row->mbrId
        ];
    }
}

$memberCircle = $pixdb->getRow(
    'members_info',
    ['member' => $lgUser->id],
    'cruntChptr,
    collegiateSection'
);
if ($memberCircle) {
    if ($memberCircle->collegiateSection) {
        $ownCircles[] = [
            'circle' => 'collegiate',
            'circleId' => $memberCircle->collegiateSection
        ];
        $hvLeaders = $pixdb->get(
            'collegiate_leaders',
            ['coliId' => $memberCircle->collegiateSection]
        )->data;
        foreach ($hvLeaders as $row) {
            $teamData[] = (object)[
                'title' => 'Collegiate Liaison',
                'memberId' => $row->mbrId
            ];
        }
    }
    if ($memberCircle->cruntChptr) {
        $ownCircles[] = [
            'circle' => 'section',
            'circleId' => $memberCircle->cruntChptr
        ];
        $hvLeaders = $pixdb->get(
            'section_leaders',
            ['secId' => $memberCircle->cruntChptr],
            'type,
            mbrId'
        )->data;
        foreach ($hvLeaders as $row) {
            $teamData[] = (object)[
                'title' => "Section $row->type",
                'memberId' => $row->mbrId
            ];
        }
    }
}

$offcrCnds = '';
foreach ($ownCircles as $row) {
    $offcrCnds .= ($offcrCnds != '' ? ' OR ' : '') . '(circle=' . q($row['circle']) . ' and circleId=' . $row['circleId'] . ')';
}
if ($offcrCnds) {
    $getOfficers = $pixdb->get(
        [
            ['officers', 'o', 'title'],
            ['officers_titles', 't', 'id']
        ],
        ['#QRY' => $offcrCnds],
        't.title, o.memberId'
    )->data;

    foreach ($getOfficers as $row) {
        $teamData[] = (object)[
            'title' => $row->title,
            'memberId' => $row->memberId
        ];
    }
}

$memberIds = array_unique(collectObjData($teamData, 'memberId'));
$memberInfo = [];
if ($memberIds) {
    $memberInfo = $evg->getMemberInfo($memberIds, ['firstName', 'lastName', 'email', 'memberId', 'avatar'], ['city,zipcode'], true);
}
foreach ($teamData as $row) {
    if (isset($memberInfo[$row->memberId])) {
        $row->member = $memberInfo[$row->memberId];
    }
}

$groupedTeamData = [];
foreach ($teamData as $row) {
    $role = $row->title;
    $member = (object)[
        'name' => $row->member->firstName . ' ' . $row->member->lastName,
        'email' => $row->member->email,
        'memberId' => $row->member->memberId,
        'address' => implode(',', [$row->member->city, $row->member->zipcode]),
        'profileImage' => $row->member->avatar ? $pix->domain . 'uploads/avatars/' . $pix->thumb($row->member->avatar, '150x150') : null
    ];

    if (!isset($groupedTeamData[$role])) {
        $groupedTeamData[$role] = [
            'role' => $role,
            'members' => []
        ];
    }

    $groupedTeamData[$role]['members'][] = $member;
}
$finalTeamData = array_values($groupedTeamData);


$r->status = 'ok';
$r->success = 1;
$r->message = 'Data retrived successfully';
$r->data->teamData  = $finalTeamData;
