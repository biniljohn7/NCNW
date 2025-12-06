<?php
$inp = getJsonBody();
$page = isset($inp->page) ? intval($inp->page) : 0;
$key = isset($inp->key) ? esc($inp->key) : '';
$type = isset($inp->type) ? esc($inp->type) : '';

$lstTyp = '';

$filters = [
    '__page' => max(0, ($page - 1)),
    '__limit' => 15,
    '#SRT' => 'regOn desc',
    '#QRY' => 'm.id !=' . $lgUser->id,
    '__QUERY__' => []
];

if ($key && $key != '') {
    $filters['__QUERY__'][] = "(m.firstName like '%" . $key . "%' or m.lastName like '%" . $key . "%' or m.memberId like '%" . $key . "%')";
}

if ($type != '') {
    switch ($type) {
        case 'state':
            if ($isStateLeader) {
                $filters['__QUERY__'][] = stateFun();
                $lstTyp = 'state-members';
            }
            break;

        case 'section':
            if ($isSectionLeader || $isSectionPresident) {
                $filters['__QUERY__'][] = sectionFun();
                $lstTyp = 'section-members';
            }
            break;

        case 'affiliate':
            if ($isAffiliateLeader) {
                $filters['__QUERY__'][] = affiliateFun();
                $lstTyp = 'affiliate-members';
            }
            break;

        case 'collegiate':
            if ($isCollegiateLeader) {
                $filters['__QUERY__'][] = collegiateFun();
                $lstTyp = 'collegiate-members';
            }
            break;
    }
} else {
    if ($isStateLeader) {
        $filters['__QUERY__'][] = stateFun();
        $lstTyp = 'state-members';
        ///
    } elseif ($isSectionLeader || $isSectionPresident) {
        $filters['__QUERY__'][] = sectionFun();
        $lstTyp = 'section-members';
        ///
    } elseif ($isAffiliateLeader) {
        $filters['__QUERY__'][] = affiliateFun();
        $lstTyp = 'affiliate-members';
        ///
    } elseif ($isCollegiateLeader) {
        $filters['__QUERY__'][] = collegiateFun();
        $lstTyp = 'collegiate-members';
        ////
    }
}

$r->success = 1;
$r->data = (object)[
    'list' => [],
    'currentPageNo' => $page,
    'totalPages' => 0
];


///  fetch members
if ($lstTyp != '') {
    $members = $pixdb->get(
        [
            ['members', 'm', 'id'],
            ['members_info', 'i', 'member']
        ],
        $filters,
        'm.id, 
        m.avatar, 
        m.firstName, 
        m.lastName, 
        m.memberId, 
        m.email, 
        i.city, 
        i.zipcode, 
        i.address,
        i.phone,
        i.cruntChptr'
    );

    if ($members) {
        $secIds = [];
        $affIds = [];
        foreach ($members->data as $row) {
            if ($row->cruntChptr) {
                $secIds[] = $row->cruntChptr;
            }
            if ($row->affilateOrgzn) {
                $affIds[] = $row->affilateOrgzn;
            }
        }
        $secData = $evg->getChapters($secIds, 'id, name');
        $affData = $evg->getAffiliations($affIds, 'id, name');

        foreach ($members->data as $mbr) {
            $section = $secData[$mbr->cruntChptr]->name ?? '--';
            $affiliation = $affData[$mbr->affilateOrgzn]->name ?? '--';

            $mbr->avatar = $mbr->avatar ? $pix->domain . 'uploads/avatars/' . $pix->thumb($mbr->avatar, '150x150') : null;
            $mbr->name = $mbr->firstName . ' ' . $mbr->lastName;
            $mbr->section = $section;
            $mbr->affiliation = $affiliation;
        }

        $r->status = 'ok';
        $r->message = 'Data retrieved successfully.';
        $r->lstTyp = $lstTyp;
        $r->data->list = $members->data;
        $r->data->currentPageNo = $members->current + 1;
        $r->data->totalPages = $members->pages;
    }
}

function stateFun()
{
    global $pixdb, $lgUser;
    $secIds = [];
    $qry = '';

    $sections = $pixdb->get(
        [
            ['state_leaders', 'l', 'stateId'],
            ['chapters', 'c', 'state']
        ],
        ['l.mbrId' => $lgUser->id],
        'c.id'
    );

    if ($sections) {
        foreach ($sections->data as $sec) {
            $secIds[] = $sec->id;
        }
    }
    if (!empty($secIds)) {
        $qry = 'i.cruntChptr in (' . implode(',', $secIds) . ')';
    }
    return $qry;
}

function sectionFun()
{
    global $pixdb, $lgUser;
    $qry = '';

    $sections = $pixdb->get(
        'section_leaders',
        [
            'mbrId' => $lgUser->id,
            'single' => 1
        ],
        'secId'
    );

    if (
        $sections &&
        isset($sections->secId)
    ) {
        $qry = 'i.cruntChptr = ' . $sections->secId;
    }
    return $qry;
}

function affiliateFun()
{
    global $pixdb, $lgUser;
    $qry = '';

    $affInfo = $pixdb->get(
        'affiliate_leaders',
        [
            'mbrId' => $lgUser->id,
            'single' => 1
        ],
        'affId'
    );

    if (
        $affInfo &&
        isset($affInfo->affId)
    ) {
        $qry = 'i.affilateOrgzn = ' . $affInfo->affId;
    }
    return $qry;
}

function collegiateFun()
{
    global $pixdb, $lgUser;
    $qry = '';

    $colleInfo = $pixdb->get(
        'collegiate_leaders',
        [
            'mbrId' => $lgUser->id,
            'single' => 1
        ],
        'coliId'
    );

    if (
        $colleInfo &&
        isset($colleInfo->coliId)
    ) {
        $qry = 'i.collegiateSection = ' . $colleInfo->coliId;
    }

    return $qry;
}

// var_dump($r->data);
// exit;