<?php
devMode();
$inp = getJsonBody();

$search = isset($inp->search) ? esc($inp->search) : null;
$pgn = isset($inp->pgn) ? intval($inp->pgn) : 0;

$pageLimit = 20;

$shConds = [
    'enabled' => 'Y',
    '#SRT' => 'firstName asc',
    '#QRY' => 'id != ' . $lgUser->id,
    '__page' => $pgn - 1,
    '__limit' => $pageLimit,
    '__QUERY__' => []
];
if ($search) {
    if ($search) {
        $qSearch = q("%$search%");
        $shConds['__QUERY__'][] = '(
            firstName like ' . $qSearch . ' OR 
            lastName like ' . $qSearch . ' OR 
            email like ' . $qSearch . ' OR 
            memberId like ' . $qSearch . '
        )';
    }
} else {
    $shConds['__QUERY__'][] = '(firstName IS NOT NULL OR firstName != "")';
}

$memIDs = [];
if (isset($inp->affiliate) && is_array($inp->affiliate)) {
    $affiliate = array_unique(array_filter(array_map('intval', $inp->affiliate)));
    if ($affiliate) {
        $membId = $pixdb->get(
            'members_affiliation',
            ['affiliation' => $affiliate],
            'member'
        )->data;
        $mArr = [];
        foreach ($membId as $mb) {
            $mArr[] = $mb->member;
        }

        $memIDs = !empty($memIDs) ? array_intersect($memIDs, $mArr) : $mArr;
    }
}

if (isset($inp->section)) {
    $section = intval($inp->section);
    if ($section) {
        $membId = $pixdb->get(
            'members_info',
            ['cruntChptr' => $section],
            'member'
        )->data;
        $scArr = [];
        foreach ($membId as $mb) {
            $scArr[] = $mb->member;
        }

        $memIDs = !empty($memIDs) ? array_intersect($memIDs, $scArr) : $scArr;
    }
}

if (!empty($memIDs)) {
    $unique = array_unique($memIDs);
    $idString = '';
    foreach ($unique as $uni) {
        $idString .= $uni . ',';
    }
    $idString = substr($idString, 0, strlen($idString) - 1);
    if (strlen($idString) > 0) {
        $shConds['__QUERY__'][] = '`id` IN (' . $idString . ')';
    } else {
        // $shConds['__QUERY__'][] = '`id` IN (0)';
    }
} else {
    // $shConds['__QUERY__'][] = '`id` IN (0)';
}

$members = $pixdb->get(
    [
        ['members', 'mm', 'id'],
        ['members_info', 'mi', 'member']
    ],
    $shConds,
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
    mi.state'
);

if ($members) {
    $secIds = [];
    $affIds = [];
    $membIds = [];
    $stateIds = [];
    $affData = null;
    $mbrAffiliate = [];

    foreach ($members->data as $row) {
        if ($row->cruntChptr) {
            $secIds[] = $row->cruntChptr;
        }
        if ($row->state) {
            $stateIds[] = $row->state;
        }
        if ($row->id) {
            $membIds[] = $row->id;
        }
    }

    $membIds = array_unique($membIds);
    $cnd = [
        'h.enabled' => 'Y',
        '#QRY' => "(h.expiry >= '$date' OR h.expiry IS NULL)"
    ];
    if ($membIds) {
        $cnd['m.id'] = $membIds;

        $affData = $pixdb->get(
            'members_affiliation',
            [
                '#QRY' => 'member in (' . implode(',',  $membIds) . ')'
            ]
        );
        foreach ($affData->data as $aff) {
            $affIds[] = $aff->affiliation;
        }
        $affIds = array_unique($affIds);

        if (!empty($affIds)) {
            $affNames = $evg->getAffiliations($affIds, 'id, name');

            foreach ($affData->data as $aff) {
                if (
                    !isset($mbrAffiliate[$aff->member])
                ) {
                    $mbrAffiliate[$aff->member] = $affNames[$aff->affiliation]->name;
                } else {
                    $mbrAffiliate[$aff->member] .= ', ' . $affNames[$aff->affiliation]->name;
                }
            }
        }
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
    $secData = $evg->getChapters($secIds, 'id, name');
    $states = $evg->getStates($stateIds, 'id, name');


    foreach ($members->data as $mbr) {
        $mbr->membership = $membershipsArr[$mbr->id]->planName ?? null;
        $mbr->secName = $secData[$mbr->cruntChptr]->name ?? null;
        $mbr->affName = $mbrAffiliate[$mbr->id] ?? null;
        $mbr->avatar = $mbr->avatar ? $pix->domain . 'uploads/avatars/' . $pix->thumb($mbr->avatar, '150x150') : null;
        $mbr->name = html_entity_decode($mbr->firstName . ' ' . $mbr->lastName, ENT_QUOTES, 'UTF-8');
        $mbr->stateName = $states[$mbr->state]->name ?? null;
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
