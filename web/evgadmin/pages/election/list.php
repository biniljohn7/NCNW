<?php
$isStateLeader = $lgUser->type == 'state-leader';
$isSectionPresident = $lgUser->type == 'section-president';
$notAdmin = !($isSectionPresident || $isStateLeader);
$presidentMemberId = false;
$leaderMemberId = false;
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$elected = new stdClass();
$selTitles = [];
$selStates = [];
$selSections = [];
$selRegions = [];
$selAffiliates = [];

$sortOps = array(
    'a-z' => 'Name Ascending',
    'z-a' => 'Name Descending',
    'new' => 'Newer First',
    'older' => 'Older First'
);
$titleDrop = [];
$statesDrop = [];
$planDrop = [];
$sectionDrop = [];
$regionDrop = [];
$affDrop = [];
$csDrop = [];
//
if ($notAdmin) {
    $states = $pixdb->get(
        'states',
        ['#SRT' => 'name asc'],
        'id, name'
    );
    foreach ($states->data as $row) {
        $statesDrop[$row->id] = $row->name;
    }
}
//
$plans = $pixdb->get(
    'membership_plans',
    ['#SRT' => 'title asc'],
    'id, title, code'
);
foreach ($plans->data as $row) {
    $co = $row->code ? " ($row->code)" : '';
    $planDrop[$row->id] = $row->title . $co;
}
//
$affliates = $pixdb->get(
    'affiliates',
    ['#SRT' => 'name asc'],
    'id, name'
);
foreach ($affliates->data as $row) {
    $affDrop[$row->id] = $row->name;
}
//
$regions = $pixdb->get(
    'regions',
    ['#SRT' => 'name asc'],
    'id, name'
);
foreach ($regions->data as $row) {
    $regionDrop[$row->id] = $row->name;
}
//
$titles = $pixdb->get(
    'officers_titles',
    ['#SRT' => 'title asc'],
    'id, title'
);
foreach ($titles->data as $row) {
    $titleDrop[$row->id] = $row->title;
}
//
$sections = $pixdb->get(
    'chapters',
    ['#SRT' => 'name asc'],
    'id, name'
);
foreach ($sections->data as $row) {
    $sectionDrop[$row->id] = $row->name;
}
//
$collegiateSec = $pixdb->get(
    'collegiate_sections',
    ['#SRT' => 'name asc'],
    'id, name'
);
foreach ($collegiateSec->data as $row) {
    $csDrop[$row->id] = $row->name;
}
// if logged user is state leader
$stateLdrId = null;
if ($lgUser->type == 'state-leader') {
    $stateLdrId = $pixdb->getRow(
        'members_info',
        ['member' => $lgUser->memberid],
        'state'
    );
}

// if logged user is section president
$sectionPrntId = null;
if ($lgUser->type == 'section-president') {
    $sectionPrntId = $pixdb->getRow(
        'members_info',
        ['member' => $lgUser->memberid],
        'cruntChptr'
    );
}
//

//
$conds = [
    '#SRT' => 'id desc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array(),

];
$memIDs = [];
// searching
$shKey =  esc($_GET['sh_key'] ?? '');
if ($shKey) {
    $qSearch = q("%$shKey%");
    $search = $pixdb->get(
        'members',
        ['__QUERY__' => "concat(firstName, ' ', lastName) like $qSearch OR email like $qSearch OR memberId like $qSearch OR stripeCusId like $qSearch"],
        'id'
    )->data;

    $seaArr = [];
    foreach ($search as $st) {
        $seaArr[] = $st->id;
    }
    //if (!empty($seaArr)) {
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $seaArr) : $seaArr;
    //}
}

// state key
$fltState = '';
$stateKey = $_GET['st_sort'] ?? [];
if (!is_array($stateKey)) {
    $stateKey = [$stateKey];
}
if ($isStateLeader) {
    $searchState = $pixdb->qry(
        'SELECT s.stateId, s.mbrId 
        FROM state_leaders s 
        WHERE s.mbrId = (
            SELECT a.memberid 
            FROM admins a 
            WHERE a.type = "state-leader" 
            AND a.id =   ' . $lgUser->id . ')'
    );
    $stateKey[] = $searchState->stateId;
    $leaderMemberId = $searchState->mbrId;
}
if (!empty($stateKey)) {
    $stateKey = array_filter(
        array_unique(
            array_map(
                'esc',
                $stateKey
            )
        )
    );
    $stMems = $pixdb->get(
        'members_info',
        ['#QRY' => 'state IN(' . implode(', ', $stateKey) . ')'],
        'member'
    )->data;
    $stArr = [];
    foreach ($stMems as $st) {
        $stArr[] = $st->member;
    }
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $stArr) : $stArr;
    if ($leaderMemberId && in_array($leaderMemberId, $memIDs)) {
        $memIDs = array_diff($memIDs, [$leaderMemberId]);
    }
    foreach ($stateKey as $row) {
        $selStates[] = [
            'id' => $row,
            'name' => $statesDrop[$row] ?? ''
        ];
        $fltState .= ($fltState == '' ? '' : ', ') . ($statesDrop[$row] ?? '');
    }
}

// section search
$fltSection = '';
$shSectionKey = $_GET['sh_sc_sort'] ?? [];
if (!is_array($shSectionKey)) {
    $shSectionKey = [$shSectionKey];
}
if ($isSectionPresident) {
    $searchSection = $pixdb->qry(
        'SELECT s.secId, s.mbrId 
        FROM section_leaders s 
        WHERE s.type="president" 
        AND s.mbrId = (
            SELECT a.memberid 
            FROM admins a 
            WHERE a.type = "section-president" 
            AND a.id =   ' . $lgUser->id . ')'
    );
    $shSectionKey[] = $searchSection->secId;
    $presidentMemberId = $searchSection->mbrId;
}
if (!empty($shSectionKey)) {
    $shSectionKey = array_filter(
        array_unique(
            array_map(
                'esc',
                $shSectionKey
            )
        )
    );
    $secMems = $pixdb->get(
        'members_info',
        ['#QRY' => 'cruntChptr IN(' . implode(', ', $shSectionKey) . ')'],
        'member'
    )->data;
    $scArr = [];
    foreach ($secMems as $sc) {
        $scArr[] = $sc->member;
    }
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $scArr) : $scArr;
    if ($presidentMemberId && in_array($presidentMemberId, $memIDs)) {
        $memIDs = array_diff($memIDs, [$presidentMemberId]);
    }
    foreach ($shSectionKey as $row) {
        $selSections[] = [
            'id' => $row,
            'name' =>  $sectionDrop[$row] ?? ''
        ];
        $fltSection .= ($fltSection == '' ? '' : ', ') . ($sectionDrop[$row]);
    }
}

//Affiliate
$fltAffiliate = '';
$affliateKey = $_GET['af_sort'] ?? [];
if (!is_array($affliateKey)) {
    $affliateKey = [$affliateKey];
}
if (!empty($affliateKey)) {
    $affliateKey = array_filter(
        array_unique(
            array_map(
                'esc',
                $affliateKey
            )
        )
    );
    $affMems = $pixdb->get(
        'members_affiliation',
        ['#QRY' => 'affiliation IN(' . implode(', ', $affliateKey) . ')'],
        'member, affiliation'
    )->data;
    $afArr = [];
    foreach ($affMems as $af) {
        $afArr[] = $af->member;
    }
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $afArr) : $afArr;
    foreach ($affliateKey as $row) {
        $selAffiliates[] = [
            'id' => $row,
            'name' => $affDrop[$row]
        ];
        $fltAffiliate .= ($fltAffiliate == '' ? '' : ', ') . ($affDrop[$row]);
    }
}

//Regions
$fltRegions = '';
$regionKey = $_GET['rg_sort'] ?? [];
if (!is_array($regionKey)) {
    $regionKey = [$regionKey];
}
if (!empty($regionKey)) {
    $regionKey = array_filter(
        array_unique(
            array_map(
                'esc',
                $regionKey
            )
        )
    );
    $regMems = $pixdb->get(
        'members_info',
        ['#QRY' => 'regionId IN(' . implode(', ', $regionKey) . ')'],
        'member'
    )->data;
    $rgArr = [];
    foreach ($regMems as $rg) {
        $rgArr[] = $rg->member;
    }
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $rgArr) : $rgArr;
    foreach ($regionKey as $row) {
        $selRegions[] = [
            'id' => $row,
            'name' => $regionDrop[$row]
        ];
        $fltRegions .= ($fltRegions == '' ? '' : ', ') . ($regionDrop[$row]);
    }
}

//title seaech
$fltTitle = '';
$shTitleKey = $_GET['ttl_sort'] ?? [];
if (!is_array($shTitleKey)) {
    $shTitleKey = [$shTitleKey];
}
if (!empty($shTitleKey)) {
    $shTitleKey = array_filter(
        array_unique(
            array_map(
                'esc',
                $shTitleKey
            )
        )
    );
    $ttlMems = $pixdb->get(
        'officers',
        ['#QRY' => 'title IN(' . implode(', ', $shTitleKey) . ')'],
        'memberId'
    )->data;
    $ttlArr = [];
    foreach ($ttlMems as $ttl) {
        $ttlArr[] = $ttl->memberId;
    }
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $ttlArr) : $ttlArr;
    foreach ($shTitleKey as $row) {
        $selTitles[] = [
            'id' => $row,
            'name' => $titleDrop[$row]
        ];
        $fltTitle .= ($fltTitle == '' ? '' : ', ') . ($titleDrop[$row]);
    }
}

//collegiate section
$collegiateSecKey = esc($_GET['cs_sort'] ?? '');
if ($collegiateSecKey) {
    $csMems = $pixdb->get(
        'members_info',
        ['collegiateSection' =>  $collegiateSecKey],
        'member'
    )->data;
    $csArr = [];
    foreach ($csMems as $cs) {
        $csArr[] = $cs->member;
    }
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $csArr) :  $csArr;
}

//Membership plan
$memPlanKey = esc($_GET['mp_sort'] ?? '');
if ($memPlanKey) {
    $memPlan = $pixdb->get(
        'memberships',
        ['planId' => $memPlanKey],
        'member'
    )->data;
    $mArr = [];
    foreach ($memPlan as $mf) {
        $mArr[] = $mf->member;
    }
    //if (!empty($mArr)) {
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $mArr) : $mArr;
    //}
}
// sorting
$sortBy = '(
    SELECT 1 
    FROM officers 
    WHERE memberId = members.id
    LIMIT 1
) IS NULL, id';

$shSort = esc($_GET['sh_sort'] ?? '');
if ($shSort) {
    switch ($shSort) {
        case 'a-z':
            $sortBy = '`firstName` asc';
            break;
        case 'z-a':
            $sortBy = '`firstName` desc';
            break;
        case 'new':
            $sortBy = '`id` desc';
            break;
        case 'older':
            $sortBy = '`id` asc';
            break;
    }
}
$conds['#SRT'] = $sortBy;

// membership status search
$conds['enabled'] = 'Y';
$shMembSts = $_GET['sh_memb_sts'] ?? [];
if (!is_array($shMembSts)) {
    $shMembSts = [$shMembSts];
}
if (!empty($shMembSts)) {
    $shCond = [];

    $shMembSts = array_filter(
        array_unique(
            array_map(
                'esc',
                $shMembSts
            )
        )
    );

    if (in_array('active', $shMembSts)) {
        $shCond[] = 'm.enabled = ' . q('Y');
    }
    if (in_array('inactive', $shMembSts)) {
        $shCond[] = 'm.enabled = ' . q('N');
    }
    if (in_array('expired', $shMembSts)) {
        $shCond[] = '(m.expiry IS NOT NULL AND m.expiry < "' . $date . '" AND m.payStatus = "completed")';
    }
    if (in_array('paid-completely', $shMembSts)) {
        $shCond[] = 'm.payStatus = "completed"';
    }
    if (in_array('payment-pending', $shMembSts)) {
        $shCond[] = 'm.payStatus = "pending"';
    }
    if (in_array('ongoing-installment', $shMembSts)) {
        $shCond[] = 'm.installment IS NOT NULL';
    }

    $shFiltrConds = '';
    if (!empty($shCond)) {
        $shFiltrConds = 'AND (' . implode(' OR ', $shCond) . ')';
    }

    $membActive = $pixdb->fetchAll(
        'SELECT *
        FROM memberships m
        WHERE m.created = (
            SELECT MAX(created)
            FROM memberships
            WHERE member = m.member
            AND (
                (giftedBy IS NOT NULL AND accepted = "Y")
                OR giftedBy IS NULL
            )
        )
        ' . $shFiltrConds . '
        ORDER BY m.created DESC'
    );

    $mArr = [];
    foreach ($membActive as $ma) {
        $mArr[] = $ma->member;
    }

    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $mArr) : $mArr;
}

$deadOrAlive = $_GET['sh_life_sts'] ?? '';
if ($deadOrAlive && $deadOrAlive === 'deceased') {
    $memDeceased = $pixdb->get(
        'members_info',
        ['deceased' => 'Y'],
        'member'
    )->data;
    $mDeces = [];

    foreach ($memDeceased as $md) {
        $mDeces[] = $md->member;
    }
    //if (!empty($mArr)) { 
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $mDeces) : $mDeces;
    //}
}
$formatDate = function ($dateString) {
    return date('Y-m-d', strtotime(str_replace(' / ', '-', $dateString)));
};
$expStartDate = esc($_GET['sh_expStr'] ?? '');
$expEndDate = esc($_GET['sh_expStr'] ?? '');

$adnlqry = false;

if (
    $expStartDate != '' &&
    $expEndDate != ''
) {
    $expStartDate = $formatDate($expStartDate);
    $expEndDate = $formatDate($expEndDate);
    $adnlqry = 'expiry BETWEEN "' . $expStartDate . '" AND "' . $expEndDate . '"';
} elseif ($expStartDate != '') {
    $expStartDate = $formatDate($expStartDate);
    $adnlqry = 'date >= "' . $expStartDate . '"';
} elseif ($expEndDate != '') {
    $expEndDate = $formatDate($expEndDate);
    $adnlqry = 'date <= "' . $expEndDate . '"';
}
if ($adnlqry) {
    $expMembs = $pixdb->get(
        'memberships',
        ['#QRY' => $adnlqry],
        'member'
    )->data;
    $mExpArr = [];

    foreach ($expMembs as $me) {
        $mExpArr[] = $me->member;
    }
    $memIDs = !empty($memIDs) ? array_intersect($memIDs, $mExpArr) : $mExpArr;
}
if (
    $shKey ||
    $stateKey ||
    $shSectionKey ||
    $regionKey ||
    $shTitleKey ||
    $affliateKey ||
    $collegiateSecKey ||
    $memPlanKey ||
    !empty($shMembSts) ||
    $deadOrAlive ||
    $expStartDate ||
    $expEndDate
) {
    if (
        !empty($memIDs)
    ) {
        $unique = array_unique($memIDs);
        $idString = '';
        foreach ($unique as $uni) {
            $idString .= $uni . ',';
        }
        $idString = substr($idString, 0, strlen($idString) - 1);
        if (strlen($idString) > 0) {
            $conds['__QUERY__'][] = '`id` IN (' . $idString . ')';
        } else {
            $conds['__QUERY__'][] = '`id` IN (0)';
        }
    } else {
        $conds['__QUERY__'][] = '`id` IN (0)';
    }
}
$members = $pixdb->get(
    'members',
    $conds,
    'id,
    firstName,
    lastName,
    email,
    memberId,
    avatar,
    verified,
    role'
);

$mbrIds = [];
$mbrInfos = [];
$stateIds = [];
$countryIds = [];
$colSections = [];
$affOrgs = [];
$mmbrAffiliations = [];
foreach ($members->data as $mbr) {
    $mbrIds[] = $mbr->id;
}
if (!empty($mbrIds)) {
    $mbrInfos = $pixdb->fetchAssoc(
        'members_info',
        ['member' => $mbrIds],
        'member,
        refCode,
        pointsBalance,
        referralsTotal,
        country,
        state,
        city,
        address,
        address2,
        zipcode,
        phone,
        collegiateSection,
        cruntChptr',
        'member'
    );

    $memberShips = $pixdb->fetchAssoc(
        'memberships',
        ['member' => $mbrIds],
        'member,
        planName,
        expiry',
        'member'
    );

    $getMmbrAffilans = $pixdb->get(
        'members_affiliation',
        ['member' => $mbrIds],
        'member, affiliation'
    )->data;
    $affOrgs = collectObjData($getMmbrAffilans, 'affiliation');
    foreach ($getMmbrAffilans as $aff) {
        if (!isset($mmbrAffiliations[$aff->member])) {
            $mmbrAffiliations[$aff->member] = [];
        }
        $mmbrAffiliations[$aff->member][] = $aff->affiliation;
    }

    $secLeaders = $pixdb->fetchAssoc(
        'section_leaders',
        [
            'mbrId' => $mbrIds,
            'type' => 'delegate'
        ],
        'secId,mbrId,type',
        'mbrId'
    );

    $secOfficers = $pixdb->fetchAssoc(
        'officers',
        [
            'memberId' => $mbrIds,
            'circle' => 'section'
        ],
        'memberId,
        title,
        circleId',
        'memberId'
    );
}
foreach ($mbrInfos as $sts) {
    $stateIds[] = $sts->state;
    $countryIds[] = $sts->country;
    $colSections[] = $sts->collegiateSection;
    $chapters[] = $sts->cruntChptr;
}

if (!empty($stateIds)) {
    $stateIds = array_filter($stateIds);
    $stateName = $pixdb->fetchAssoc(
        'states',
        ['id' => $stateIds],
        'id,name',
        'id'
    );
}
if (!empty($countryIds)) {
    $countryIds = array_filter($countryIds);
    $countries = $pixdb->fetchAssoc(
        'nations',
        ['id' => $countryIds],
        'id,name',
        'id'
    );
}
if (!empty($colSections)) {
    $colSections = array_filter($colSections);
    $collegiates = $pixdb->fetchAssoc(
        'collegiate_sections',
        ['id' => $colSections],
        'id,name',
        'id'
    );
}
if (!empty($affOrgs)) {
    $affOrgs = array_filter($affOrgs);
    $affiliates = $pixdb->fetchAssoc(
        'affiliates',
        ['id' => $affOrgs],
        'id,name',
        'id'
    );
}
if (!empty($chapters)) {
    $chapters = array_filter($chapters);
    $secChapters = $pixdb->fetchAssoc(
        'chapters',
        ['id' => $chapters],
        'id,name',
        'id'
    );
}
$rolesArray = [
    'state-leader' => 'State Leader',
    'section-leader' => 'Section Leader',
    'section-president' => 'Section President',
    'affiliate-leader' => 'Affiliate Leader',
    'collegiate-leaders' => 'Collegiate Leaders',
    'section-officer' => 'Section Officer',
    'section-delegate' => 'Section Delegate'
];
$officers_title = $pixdb->get(
    'officers_titles',
    ['#SRT' => 'id asc'],
    'id,title'

)->data;
$officersArray = [];
foreach ($officers_title as $row) {
    $officersArray[$row->id] = $row->title;
}

loadStyle('pages/members/list');
loadScript('pages/election/list');

?>
<div class="page-head">
    <div class="head-col">
        <h1>Election Management</h1>
        <?php
        breadcrumbs(
            ['Election Management']
        );
        ?>
    </div>
    <div class="sh-col">
        <a
            href="<?php echo ADMINURL, 'actions/anyadmin/?', http_build_query([
                        'method' => 'members-export-election',
                        'shkey' => $shKey,
                        'st_sort' => $stateKey,
                        'sh_sc_sort' => $shSectionKey,
                        'af_sort' => $affliateKey,
                        'rg_sort' => $regionKey,
                        'ttl_sort' => $shTitleKey,
                        'cs_sort' => $collegiateSecKey,
                        'mp_sort' => $memPlanKey,
                        'sh_sort' => $shSort,
                        'sh_memb_sts' => $shMembSts,
                        "sh_expStr" => $expStartDate,
                        "sh_expEnd" => $expEndDate,
                        'sh_life_sts' => $deadOrAlive,
                    ]); ?>"
            class="pix-btn site rounded mr5">
            <span class="material-symbols-outlined fltr">
                upgrade
            </span>
            Export Members
        </a>
        <span class="pix-btn site rounded mr5" id="listFilterButton">
            <span class="material-symbols-outlined fltr">
                page_info
            </span>
            Filter Results
        </span>
    </div>
</div>
<div class="members-count mb10">
    Total
    <strong>
        <?php echo $members->totalRows; ?>
    </strong>
    <?php echo 'member', $members->totalRows > 1 ? 's' : ''; ?>
</div>

<?php
filterBubble(
    array(
        'items' => array(
            'sh_key' => array(
                'label' => 'Keyword'
            ),
            'st_sort' => array(
                'label' => 'State',
                'value' => $fltState
            ),
            'sh_sc_sort' => array(
                'label' => 'Section',
                'value' => $fltSection
            ),
            'af_sort' => array(
                'label' => 'Affiliates',
                'value' => $fltAffiliate
            ),
            'rg_sort' => array(
                'label' => 'Regions',
                'value' => $fltRegions
            ),
            'ttl_sort' => array(
                'label' => 'Titles',
                'value' => $fltTitle
            ),
            'cs_sort' => array(
                'label' => 'Collegiate Section',
                'value' => $csDrop
            ),
            'mp_sort' => array(
                'label' => 'Membership Plan',
                'value' => $planDrop
            ),
            'sh_sort' => array(
                'label' => 'Sort By',
                'value' => $sortOps
            ),
            'sh_memb_sts' => [
                'label' => 'Membership Status'
            ],
            'sh_life_sts' => [
                'label' => 'Member Status',
                'value' => ucfirst($deadOrAlive),
            ]
        )
    )
);
$pix->pagination(
    $members->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="member-container">
    <?php
    foreach ($members->data as $mbr) {
        $info = isset($mbrInfos[$mbr->id]) ? $mbrInfos[$mbr->id] : (object)[];
        $name = $mbr->firstName . $mbr->lastName;
    ?>
        <div class="member-box">
            <div class="mbr-item left">
                <div class="mbr-itm usr">
                    <div class="usr-thumb">
                        <span class="user-thumb <?php echo $mbr->avatar ? '' : 'letter-' . strtolower($name[0]) ?? ''; ?>">
                            <?php
                            echo $mbr->avatar ?
                                '<img src="' . $evg->getAvatar($mbr->avatar) . '">' :
                                strtoupper($name[0] ?? '');
                            ?>
                        </span>
                    </div>
                    <div class="usr-dtls">
                        <div class="usr-info">
                            <a href="<?php echo $pix->adminURL, '?page=members&sec=details&id=', $mbr->id; ?>" class="info-name">
                                <?php echo $mbr->firstName, ' ', $mbr->lastName;  ?>
                            </a>
                            <div class="info-contact">
                                <div class="inf-cnt">
                                    <span class="material-symbols-outlined">email</span>
                                    <span class="cnt"><?php echo $mbr->email ?? '--'; ?></span>
                                </div>
                                <div class="inf-cnt">
                                    <span class="material-symbols-outlined">phone</span>
                                    <span class="cnt"><?php echo $info->phone ?? '--'; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="usr-roles">
                            <div class="itm-hed">
                                <span class="material-symbols-outlined">person_shield</span> Roles assigned
                            </div>
                            <div class="itm-lst">
                                <?php
                                $memRoles = array_filter(explode(',', $mbr->role ?? ''));

                                if (!empty($memRoles)) {
                                    foreach ($memRoles as $mems) {
                                        if (isset($rolesArray[$mems])) {
                                            echo $rolesArray[$mems] . '<br>';
                                        }
                                    }
                                } else {
                                    echo 'No roles assigned';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mbr-itm mrmb-info">
                    <div class="itm-hed">
                        <span class="material-symbols-outlined">badge</span> Membership info
                    </div>
                    <div class="itm-wrap">
                        <small class="wrp-ttl">Member ID</small>
                        <span>
                            <?php echo $mbr->memberId ?? '--'; ?>
                        </span>
                    </div>
                    <div class="itm-wrap">
                        <small class="wrp-ttl">Membership </small>
                        <span>
                            <?php
                            echo isset($memberShips[$mbr->id]) ?
                                $memberShips[$mbr->id]->planName :
                                '--';
                            ?>
                        </span>
                    </div>
                    <div class="itm-wrap">
                        <small class="wrp-ttl">Expiry</small>
                        <span>
                            <?php
                            echo (isset($memberShips[$mbr->id]) && !empty($memberShips[$mbr->id]->expiry))
                                ? date('d-m-Y', strtotime($memberShips[$mbr->id]->expiry))
                                : '--';
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="mbr-item right">
                <div class="mbr-itm loc-addr">
                    <div class="itm-hed">
                        <span class="material-symbols-outlined">location_on</span> Location and Address
                    </div>
                    <div class="itm-wrap">
                        <small class="wrp-ttl">Address line 1 </small>
                        <span>
                            <?php echo $info->address ?? '--'; ?>
                        </span>
                    </div>
                    <div class="itm-wrap">
                        <small class="wrp-ttl">Address line 2 </small>
                        <span>
                            <?php echo $info->address2 ?? '--'; ?>
                        </span>
                    </div>
                    <div class="itm-flx">
                        <div class="itm-wrap">
                            <small class="wrp-ttl">City</small>
                            <span>
                                <?php echo $info->city ?? '--'; ?>
                            </span>
                        </div>
                        <div class="itm-wrap">
                            <small class="wrp-ttl">State</small>
                            <span>
                                <?php
                                echo $info &&  isset($info->state, $stateName[$info->state]) ?
                                    $stateName[$info->state]->name :
                                    '--';
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="itm-flx">
                        <div class="itm-wrap">
                            <small class="wrp-ttl">Country</small>
                            <span>
                                <?php
                                echo $info &&  isset($info->country, $countries[$info->country]) ?
                                    $countries[$info->country]->name :
                                    '--';
                                ?>
                            </span>
                        </div>
                        <div class="itm-wrap">
                            <small class="wrp-ttl">Zip</small>
                            <span>
                                <?php echo $info->zipcode ?? '--'; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mbr-itm aff-sec">
                    <div class="itm-hed">
                        <span class="material-symbols-outlined">view_quilt</span> Affiliates / Sections
                    </div>
                    <div class="itm-wrap">
                        <small class="wrp-ttl">Affiliate Organization</small>
                        <span>
                            <?php
                            $mAff = '';
                            if (isset($mmbrAffiliations[$mbr->id])) {
                                foreach ($mmbrAffiliations[$mbr->id] as $aff) {
                                    if (isset($affiliates[$aff])) {
                                        $mAff .= ($mAff != '' ? ', ' : '') . $affiliates[$aff]->name;
                                    }
                                }
                            }
                            echo $mAff ?? '--';
                            ?>
                        </span>
                    </div>
                    <div class="itm-wrap">
                        <small class="wrp-ttl">Collegiate Section</small>
                        <span>
                            <?php
                            echo $info && isset($info->collegiateSection, $collegiates[$info->collegiateSection]) ?
                                $collegiates[$info->collegiateSection]->name :
                                '--';
                            ?>
                        </span>
                    </div>
                    <div class="itm-wrap">
                        <small class="wrp-ttl">Section</small>
                        <span>
                            <?php
                            echo $info &&  isset($info->cruntChptr, $secChapters[$info->cruntChptr]) ?
                                $secChapters[$info->cruntChptr]->name :
                                '--';
                            ?>
                        </span>
                    </div>
                </div>
                <div class="mbr-itm tnxs">
                    <?php
                    $curChapter = $info->cruntChptr ?? '';
                    if ($curChapter) {
                    ?>
                        <div class="itm-hed">
                            <span class="material-symbols-outlined">checklist_rtl</span> Elect As
                        </div>
                        <div class="box-infos">
                            <div class="pt-10">
                                <?php
                                $officerMbr = isset($secOfficers[$mbr->id]) ? $secOfficers[$mbr->id] : '';
                                ?>
                                <select
                                    name="member_elect"
                                    id="member_elect_<?php echo $mbr->id; ?>"
                                    class="member-elect">
                                    <option value="-1" data-member="<?php echo $mbr->id; ?>">None</option>
                                    <?php
                                    foreach ($officersArray as $key => $officer) {
                                        $sel = '';
                                        if (
                                            isset($officerMbr->circleId) &&
                                            $officerMbr->circleId == $curChapter
                                        ) {
                                            if (
                                                isset($officerMbr->title) &&
                                                $officerMbr->title == $key
                                            ) {
                                                $sel = 'selected';
                                            }
                                        }
                                        $has = isset($officerMbr->title) ? 'yes' : 'no';
                                    ?>
                                        <option
                                            value="<?php echo $key; ?>"
                                            data-member="<?php echo $mbr->id; ?>"
                                            data-has="<?php echo $has; ?>"
                                            <?php echo $sel; ?>>
                                            <?php
                                            echo ucfirst($officer);
                                            ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="itm-hed">
                            Not eligible for election
                        </div>

                        <div class="box-infos">
                            <div class="pt-10">No chapter assigned</div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>
<?php
$pix->pagination(
    $members->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
//filter options
$filterOptions = [
    [
        'type' => 'hidden',
        'name' => 'page',
        'value' => 'election',
    ],
    [
        'type' => 'text',
        'label' => 'Keyword',
        'name' => 'sh_key',
        'getKey' => 'sh_key',
        'autocomplete' => 'off',
        'placeholder' => 'Member ID / First Name / Last Name'
    ],

];
$filterOptions[] = [
    'type' => 'multiple-select',
    'funName' => 'multipleSelect',
    'label' => 'Elected/Appointed Titles',
    'btnLabel' => 'Titles',
    'getData' => 'officers_titles',
    'name' => 'ttl_sort',
    'getKey' => 'ttl_sort'
];
if ($notAdmin) {
    $filterOptions[] = [
        'type' => 'multiple-select',
        'funName' => 'multipleSelect',
        'label' => 'States',
        'btnLabel' => 'States',
        'getData' => 'states',
        'name' => 'st_sort',
        'getKey' => 'st_sort'
    ];
}
if ($lgUser->type != 'section-president') {
    $filterOptions[] = [
        'type' => 'multiple-select',
        'funName' => 'multipleSelect',
        'label' => 'Sections',
        'btnLabel' => 'Sections',
        'getData' => 'chapters',
        'name' => 'sh_sc_sort',
        'getKey' => 'sh_sc_sort'
    ];
}
$filterOptions[] = [
    'type' => 'multiple-select',
    'funName' => 'multipleSelect',
    'label' => 'Regions',
    'btnLabel' => 'Regions',
    'getData' => 'regions',
    'name' => 'rg_sort',
    'getKey' => 'rg_sort'
];
$filterOptions[] = [
    'type' => 'multiple-select',
    'funName' => 'multipleSelect',
    'label' => 'Affliates',
    'btnLabel' => 'Affliates',
    'getData' => 'affiliates',
    'name' => 'af_sort',
    'getKey' => 'af_sort'
];
if ($notAdmin) {
    $filterOptions[] = [
        'type' => 'select',
        'label' => 'Collegiate Sections',
        'name' => 'cs_sort',
        'getKey' => 'cs_sort',
        'option' => $csDrop
    ];
}
$filterOptions[] = [
    'type' => 'select',
    'label' => 'Membership Plans',
    'name' => 'mp_sort',
    'getKey' => 'mp_sort',
    'option' => $planDrop
];
$filterOptions[] = [
    'type' => 'select',
    'label' => 'Sort By',
    'name' => 'sh_sort',
    'getKey' => 'sh_sort',
    'option' => $sortOps
];

$filterOptions[] = [
    'type' => 'check-group',
    'label' => 'Membership Status',
    'name' => 'sh_memb_sts',
    'getKey' => 'sh_memb_sts',
    'options' => [
        ['Active', 'active'],
        ['Inactive', 'inactive'],
        ['Expired', 'expired'],
        ['Paid Completely', 'paid-completely'],
        ['Payment Pending', 'payment-pending'],
        ['Ongoing Installment', 'ongoing-installment'],
    ]
];
$filterOptions[] = [
    'type' => 'date-range',
    'label' => 'Membership Expiration',
    'name' => [
        'sh_expStr',
        'sh_expEnd'
    ],
    'getKey' => [
        'sh_expStr',
        'sh_expEnd'
    ]
];
$filterOptions[] = [
    'type' => 'radio-group',
    'label' => 'Member Status',
    'name' => 'sh_life_sts',
    'getKey' => 'sh_life_sts',
    'options' => [
        ['Any', '', true],
        ['Deceased', 'deceased'],
    ]
];

sidebarFilter(
    'Filter Members',
    $filterOptions
);

function multipleSelect($args)
{
    global $evg;
    $selInfo = false;
    $selId = [];

    $args = (object)$args;
    $getKey = have($args->getKey);

    if (
        $getKey &&
        isset($_GET[$getKey])
    ) {
        $getVal = $_GET[$getKey];
        if (!empty($getVal)) {
            $selId = array_filter(
                array_unique(
                    array_map(
                        'esc',
                        $getVal
                    )
                )
            );
        }
    }

    if (!empty($selId)) {
        $selInfo = $evg->getSelForFilter($getVal, $args->getData);
    }
?>
    <div class="ch-itms" id="chosed<?php echo $args->btnLabel ?? ''; ?>ForFltr">
        <?php
        if (!empty($selInfo)) {
            foreach ($selInfo as $row) {
        ?>
                <input type="hidden" name="<?php echo $args->name . '[]'; ?>" value="<?php echo $row->id; ?>" />
                <span class="sel-name"><?php echo $row->name ?? ''; ?></span>
        <?php
            }
        }
        ?>
    </div>
    <?php
    if ($args->btnLabel) {
    ?>
        <span
            class="pix-btn md choose-items-for-filter"
            data-label="<?php echo $args->btnLabel ?? ''; ?>"
            data-table="<?php echo $args->getData ?? ''; ?>"
            data-name="<?php echo $args->name ?? ''; ?>">
            <?php echo 'Choose ' . $args->btnLabel ?? ''; ?>
        </span>
    <?php
    }
    ?>
<?php
}
$elected->selTitles = !empty($selTitles) ? $selTitles : [];
$elected->selStates = !empty($selStates) ? $selStates : [];
$elected->selSections = !empty($selSections) ? $selSections : [];
$elected->selRegions = !empty($selRegions) ? $selRegions : [];
$elected->selAffiliates = !empty($selAffiliates) ? $selAffiliates : [];
?>
<script type="text/javascript">
    var elected = <?php echo json_encode($elected); ?>
</script>
<?php
if (!$members->pages) {
    NoResult(
        'No members found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
