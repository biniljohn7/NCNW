<?php
$metrics = [];
$res = $evg->getLeaderCircles($lgUser->id, $userRoles);
$sections = $res->sections;
$affiliations = $res->affiliates;
$collegiates = $res->colgSectns;
if ($isStateLeader) {
    $metrics[] = (object)[
        'title' => 'Sections',
        'value' => count($sections),
        'description' => ''
    ];
}

$cnd = [
    'enabled' => 'Y',
    '__QUERY__' => []
];
$adnlqry = [];
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

$members = $pixdb->getCol(
    [
        ['members', 'm', 'id'],
        ['members_info', 'i', 'member']
    ],
    $cnd,
    'COUNT(1) as total',
    'total'
);
if ($members) {
    $metrics[] = (object)[
        'title' => 'Members',
        'value' => $members[0],
        'description' => ''
    ];
}
$learshipTitles = [];
if ($isStateLeader) {
    $state = $pixdb->getRow(
        'state_leaders',
        ['mbrId' => $lgUser->id]
    );
    if ($state) {
        $learshipTitles[] = (object)[
            'title' => 'State Leader',
            'circle' => $evg->getState($state->stateId, 'id, name')
        ];
    }
}
if ($isSectionLeader) {
    $section = $pixdb->getRow(
        'section_leaders',
        [
            'mbrId' => $lgUser->id,
            'type' => 'leader'
        ]
    );
    if ($section) {
        $learshipTitles[] = (object)[
            'title' => 'Section Leader',
            'circle' => $evg->getChapter($section->secId, 'id, name')
        ];
    }
}
if ($isSectionPresident) {
    $section = $pixdb->getRow(
        'section_leaders',
        [
            'mbrId' => $lgUser->id,
            'type' => 'president'
        ]
    );
    if ($section) {
        $learshipTitles[] = (object)[
            'title' => 'Section President',
            'circle' => $evg->getChapter($section->secId, 'id, name')
        ];
    }
}
if ($isAffiliateLeader) {
    $affilliation = $pixdb->getRow(
        'affiliate_leaders',
        ['mbrId' => $lgUser->id]
    );
    if ($affilliation) {
        $learshipTitles[] = (object)[
            'title' => 'Affiliate Leader',
            'circle' => $evg->getAffiliation($affilliation->affId, 'id, name')
        ];
    }
}
if ($isCollegiateLeader) {
    $collegiate = $pixdb->getRow(
        'collegiate_leaders',
        ['mbrId' => $lgUser->id]
    );
    if ($collegiate) {
        $learshipTitles[] = (object)[
            'title' => 'Collegiate Liaison',
            'circle' => $evg->getCollgueSection($collegiate->coliId, 'id, name')
        ];
    }
}
if ($isOfficer) {
    $officer = $pixdb->getRow(
        [
            ['officers', 'o', 'title'],
            ['officers_titles', 't', 'id']
        ],
        ['memberId' => $lgUser->id],
        't.title,
        o.circle,
        o.circleId'
    );
    if ($officer) {
        $circle = null;
        if ($circle == 'state') {
            $state = $evg->getState($officer->circleId, 'id, name');
            if ($state) {
                $learshipTitles[] = (object)[
                    'title' => $officer->title,
                    'circle' => $state
                ];
            }
        }
        if ($circle == 'affiliate') {
            $affiliate = $evg->getAffiliation($officer->circleId, 'id, name');
            if ($affiliate) {
                $learshipTitles[] = (object)[
                    'title' => $officer->title,
                    'circle' => $affiliate
                ];
            }
        }
        if ($circle == 'collegiate') {
            $collegiate = $evg->getCollgueSection($officer->circleId, 'id, name');
            if ($collegiate) {
                $learshipTitles[] = (object)[
                    'title' => $officer->title,
                    'circle' => $collegiate
                ];
            }
        }
    }
}
$r->status = 'ok';
$r->success = 1;
$r->message = 'Data retrived successfully';
$r->data->metrics  = $metrics;
$r->data->learshipTitles  = $learshipTitles;
