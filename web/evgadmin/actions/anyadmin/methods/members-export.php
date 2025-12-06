<?php
if (!$pix->canAccess('members') || !$pix->canAccess('elect')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb, $evg, $date) {
    $lgUser = $pix->getLoggedUser();
    $conds = [
        '#SRT' => 'id desc',
        //'__page' => $pgn,
        //'__limit' => 24,
        '__QUERY__' => array(),

    ];
    $memIDs = [];
    if (
        $lgUser->type == 'section-president'
    ) {
        $email = $lgUser->email;
        $memberId = $pixdb->get(
            'admins',
            ['email' => $email, 'single' => 1],
            'memberid'
        )->memberid;
        $sectionMembers = $pixdb->custom_query(
            'SELECT i.member 
            FROM members_info i
            WHERE i.cruntChptr
            IN(
                SELECT s.secId
                FROM section_leaders s
                WHERE s.mbrId = ' . $memberId . '
                AND s.type = "president"
            )
                AND i.member != ' . $memberId
        )->data;
        $secArr = [];
        foreach ($sectionMembers as $sm) {
            $secArr[] = $sm->member;
        }
        $memIDs = !empty($memIDs) ? array_intersect($memIDs, $secArr) : $secArr;
    }

    // searching
    $shKey =  esc($_GET['shkey'] ?? '');
    if ($shKey) {
        $qSearch = q("%$shKey%");
        $search = $pixdb->get(
            'members',
            ['__QUERY__' => "concat(firstName, ' ', lastName) like $qSearch OR email like $qSearch OR memberId like $qSearch"],
            'id'
        )->data;

        $seaArr = [];
        foreach ($search as $st) {
            $seaArr[] = $st->id;
        }
        $memIDs = !empty($memIDs) ? array_intersect($memIDs, $seaArr) : $seaArr;
    }

    // state key
    $stateKey = $_GET['st_sort'] ?? [];
    $leaderMemberId = false;
    if ($lgUser->type == 'state-leader') {
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
    }

    // section search
    $shSectionKey = $_GET['sh_sc_sort'] ?? [];
    $presidentMemberId = false;
    if ($lgUser->type == 'section-president') {
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
    }

    //Affiliate
    //Affiliate
    $affliateKey = $_GET['af_sort'] ?? [];
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
            'member'
        )->data;
        $afArr = [];
        foreach ($affMems as $af) {
            $afArr[] = $af->member;
        }
        $memIDs = !empty($memIDs) ? array_intersect($memIDs, $afArr) : $afArr;
    }

    //collegiate section
    $collegiateSecKey = esc($_GET['cs_sort'] ?? '');
    if ($collegiateSecKey) {
        $csMems = $pixdb->get(
            'members_info',
            ['collegiateSection' => $collegiateSecKey],
            'member'
        )->data;
        $csArr = [];
        foreach ($csMems as $af) {
            $csArr[] = $af->member;
        }

        $memIDs = !empty($memIDs) ? array_intersect($memIDs, $csArr) : $csArr;
    }

    //Membership plan
    $memPlanKey = esc($_GET['mp_sort'] ?? '');
    if ($memPlanKey) {
        $memPlan = $pixdb->get(
            'memberships',
            ['#QRY' => $memPlanKey == 'null' ? 'planId IS NOT NULL' : 'planId = ' . $memPlanKey],
            'member'
        )->data;
        $mArr = [];
        foreach ($memPlan as $mf) {
            $mArr[] = $mf->member;
        }
        if ($memPlanKey == 'null') {
            $nonMemPlan = $pixdb->get(
                'members',
                ['#QRY' => 'id NOT IN(' . implode(', ', $mArr) . ')'],
                'id'
            )->data;
            $nMArr = [];
            foreach ($nonMemPlan as $nmf) {
                $nMArr[] = $nmf->id;
            }
            $memIDs = !empty($memIDs) ? array_intersect($memIDs, $nMArr) : $nMArr;
        } elseif ($memPlanKey != 'null') {
            $memIDs = !empty($memIDs) ? array_intersect($memIDs, $mArr) : $mArr;
        }
    }

    // sorting
    $sortBy = ' m.firstName asc, m.lastName asc';
    $shSort = esc($_GET['sh_sort'] ?? '');
    if ($shSort) {
        switch ($shSort) {
            case 'a-z':
                $sortBy = ' m.firstName asc, m.lastName asc';
                break;
            case 'z-a':
                $sortBy = ' m.firstName desc, m.lastName desc';
                break;
            case 'new':
                $sortBy = ' m.id desc';
                break;
            case 'older':
                $sortBy = ' m.id asc';
                break;
        }
    }
    $conds['#SRT'] = $sortBy;

    // membership status search
    $conds['enabled'] = 'Y';
    $shMembSts = $_GET['sh_memb_sts'] ?? [];
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

        if (in_array('archieved', $shMembSts)) {
            $conds['enabled'] = 'N';
        }
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
    ///

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
    //
    $pinStatus = esc($_GET['sh_pin_sts'] ?? '');
    if ($pinStatus) {
        $pinQry = $pixdb->get(
            'memberships',
            ['pinStatus' => $pinStatus],
            'member'
        )->data;
        $mPinArr = [];

        foreach ($pinQry as $pq) {
            $mPinArr[] = $pq->member;
        }
        $memIDs = !empty($memIDs) ? array_intersect($memIDs, $mPinArr) : $mPinArr;
    }
    //

    $idString = 0;
    if (
        $shKey ||
        $stateKey ||
        $shSectionKey ||
        $affliateKey ||
        $collegiateSecKey ||
        $memPlanKey ||
        !empty($shMembSts)  ||
        $deadOrAlive ||
        $expStartDate ||
        $expEndDate ||
        $pinStatus ||
        $lgUser->type == 'section-president'
    ) {
        if (
            !empty($memIDs)
        ) {
            $unique = array_unique($memIDs);

            foreach ($unique as $uni) {
                $idString .= $uni . ',';
            }
            $idString = substr($idString, 0, strlen($idString) - 1);
        }
    }
    $members = [];
    $whereClause = '';

    if ($idString) {
        $whereClause = ' WHERE m.id IN (' . $idString . ')';
    }
    $members = $pixdb->fetchAll(
        'SELECT 
            m.firstName, 
            m.lastName,
            m.memberId,
            m.email,
            m.regOn,
            if(m.verified="Y", "Yes", "No") AS verified,
            i.refcode,
            i.pointsBalance,
            i.referralsTotal,  
            c.name AS section,
            c.secId As sectionID,          
            n.name AS country,
            s.name As state,
            r.name AS region,
            i.city,
            i.address,
            i.zipcode,
            i.phone,
            i.deceased,
            ms.pinStatus,
            ms.shpdOn,
            o.title
        FROM
            members m 
        LEFT JOIN members_info i ON m.id=i.member    
        LEFT JOIN nations n ON n.id=i.country 
        LEFT JOIN states s ON s.id=i.state 
        LEFT JOIN memberships ms ON m.id = ms.member 
        LEFT JOIN officers o ON m.id = o.memberId AND o.circle = "section" AND o.circleId = i.cruntChptr
        LEFT JOIN chapters c ON c.id = i.cruntChptr
        LEFT JOIN regions r ON r.id = i.regionId' .
            $whereClause . '
        ORDER BY ' . $sortBy,
        PDO::FETCH_NUM
    );



    $memArray = [];
    foreach ($members as $obj) {
        $ar = (array) $obj;
        if (!empty($ar[4])) {
            $ar[4] = '="' . date('d-m-Y h:i A', strtotime($ar[4])) . '"';
        }
        $ar[19] = $ar[19] ? ucfirst($ar[19]) : '';
        if (!empty($ar[20])) {
            $ar[20] = '="' . date('d-m-Y', strtotime($ar[20])) . '"';
        }
        if (!empty($ar[21])) {
            //$ar[20] = '="' . date('d-m-Y', strtotime($ar[20])) . '"';
            $titleId = intval($ar[21]);
            $title = $pixdb->getRow(
                'officers_titles',
                ['id' => $titleId],
                'title'
            );
            $ar[21] = $title->title;
        } else {
            $ar[21] = "";
        }
        $ar[18] = ($ar[18] == 'Y') ? 'Yes' : 'No';
        $memArray[] = $ar;
    }
    //var_dump($memArray);
    //exit;
    // CSV Head 
    $data = [
        [
            'First Name',
            'Last Name',
            'Member ID',
            'Email',
            'Registered On',
            'Verified',
            'Referral Code',
            'Points Balance',
            'Total Referrals',
            'Section',
            'Section ID',
            'Country',
            'State',
            'Region',
            'City',
            'Address',
            'Zip Code',
            'Phone',
            'Deceased',
            'Pin Distribution Status',
            'Pin Shipped on',
            'Elected Title'
        ]
    ];

    $data = array_merge($data, $memArray);

    $filename = 'Members_Export_' . date('Y_m(F)_d') . '-' . time() . '.csv';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    $output = fopen('php://output', 'w');
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    $pix->remsg();
    exit;
})($pix, $pixdb, $evg, $date);
