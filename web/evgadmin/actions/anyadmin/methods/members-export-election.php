<?php
if (!$pix->canAccess('members') || !$pix->canAccess('elect')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function () use ($pix, $pixdb, $evg, $date, $lgUser) {

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

    $mConds = [
        '__QUERY__' => [
            'm.enabled="Y"',
            'FIND_IN_SET("section-officer", m.role)'
        ],
        '#SRT' => $sortBy
        // . ' limit 150'
    ];

    if ($lgUser->type != 'admin') {
        $mConds['__QUERY__'][] = 'EXISTS (
            SELECT 1
            FROM
                members_info mci
                JOIN members_info mcii 
            ON mci.cruntChptr = mcii.cruntChptr
            WHERE
                mcii.`member` = ' . $lgUser->memberid . '
                AND m.id = mci.member
        )';
    }

    function filterInputArray($array, $mapFun = 'intval')
    {
        return array_filter(
            array_unique(
                array_map($mapFun, $array)
            )
        );
    }

    // searching
    $shKey =  esc($_GET['shkey'] ?? '');
    if ($shKey) {
        $qSearch = q("%$shKey%");
        $mConds['__QUERY__'][] = "concat(firstName, ' ', lastName) like $qSearch
        OR email like $qSearch
        OR memberId like $qSearch";
    }

    // state key
    $stateKey = $_GET['st_sort'] ?? [];
    if ($stateKey && is_array($stateKey)) {
        $stateKey = filterInputArray($stateKey);
        if ($stateKey) {
            $mConds['i.state'] = $stateKey;
        }
    }

    // section search
    $shSectionKey = $_GET['sh_sc_sort'] ?? [];
    if ($shSectionKey && is_array($shSectionKey)) {
        $shSectionKey = filterInputArray($shSectionKey);
        if ($shSectionKey) {
            $mConds['i.cruntChptr'] = $shSectionKey;
        }
    }

    // Affiliate
    $affliateKey = $_GET['af_sort'] ?? [];
    if ($affliateKey && is_array($affliateKey)) {
        $affliateKey = filterInputArray($affliateKey);
        if ($affliateKey) {
            $mConds['__QUERY__'][] = 'm.id in (
                select member
                from members_affiliation
                where affiliation in (
                    ' . implode(', ', $affliateKey) . '
                )
            )';
        }
    }

    // Regions
    $regionKey = $_GET['rg_sort'] ?? [];
    if ($regionKey && is_array($regionKey)) {
        $regionKey = filterInputArray($regionKey);
        if ($regionKey) {
            $mConds['regionId'] = $regionKey;
        }
    }

    // title seaech
    $shTitleKey = $_GET['ttl_sort'] ?? [];
    if ($shTitleKey && is_array($shTitleKey)) {
        $shTitleKey = filterInputArray($shTitleKey);
        if ($shTitleKey) {
            $mConds['__QUERY__'][] = 'id in (
                SELECT memberId FROM officers WHERE title IN (
                    ' . implode(', ', $shTitleKey) . '
                )
            )';
        }
    }

    //collegiate section
    $collegiateSecKey = esc($_GET['cs_sort'] ?? '');
    if ($collegiateSecKey) {
        $mConds['i.collegiateSection'] = $collegiateSecKey;
    }

    // Membership plan
    $mshipFlConds = [];
    $memPlanKey = esc($_GET['mp_sort'] ?? '');
    if ($memPlanKey) {
        $mshipFlConds[] = 'planId=' . q($memPlanKey);
    }


    // membership status search
    $shMembSts = $_GET['sh_memb_sts'] ?? [];
    if ($shMembSts && is_array($shMembSts)) {
        $shMembSts = filterInputArray($shMembSts, 'esc');
        if ($shMembSts) {
            $statusConds = [];
            foreach ($shMembSts as $status) {
                switch ($status) {
                    case 'active':
                        $statusConds[] = 'enabled="Y"';
                        break;

                    case 'inactive':
                        $statusConds[] = 'enabled="N"';
                        break;

                    case 'expired':
                        $statusConds[] = '(
                            expiry IS NOT NULL AND 
                            expiry < "' . $date . '" AND 
                            payStatus = "completed"
                        )';
                        break;

                    case 'paid-completely':
                        $statusConds[] = 'payStatus="completed"';
                        break;

                    case 'payment-pending':
                        $statusConds[] = 'payStatus="pending"';
                        break;

                    case 'ongoing-installment':
                        $statusConds[] = 'installment IS NOT NULL';
                        break;
                }
            }
            if ($statusConds) {
                $mshipFlConds[] = '(' . implode(' OR ', $statusConds) . ')';
            }
        }
    }

    $deadOrAlive = $_GET['sh_life_sts'] ?? '';
    if ($deadOrAlive && $deadOrAlive === 'deceased') {
        $mConds['i.deceased'] = 'Y';
    }

    $formatDate = function ($dateString) {
        return date('Y-m-d', strtotime(str_replace(' / ', '-', $dateString)));
    };

    $expStartDate = esc($_GET['sh_expStr'] ?? '');
    $expEndDate = esc($_GET['sh_expEnd'] ?? '');
    if (
        $expStartDate  &&
        $expEndDate
    ) {
        $expStartDate = $formatDate($expStartDate);
        $expEndDate = $formatDate($expEndDate);
        $mshipFlConds[] = 'ms.expiry BETWEEN "' . $expStartDate . '" AND "' . $expEndDate . '"';
        // 
    } elseif ($expStartDate) {
        $expStartDate = $formatDate($expStartDate);
        $mshipFlConds[] = 'ms.expiry >= "' . $expStartDate . '"';
        // 
    } elseif ($expEndDate) {
        $expEndDate = $formatDate($expEndDate);
        $mshipFlConds[] = 'ms.expiry <= "' . $expEndDate . '"';
    }

    if ($mshipFlConds) {
        $mConds['__QUERY__'][] = 'EXISTS (
            SELECT 1
            FROM memberships ms
            where 
                ms.member = m.id AND 
                ' . implode(' AND ', $mshipFlConds) . '
        )';
    }

    // $mConds['__SHOW_QUERY__'] = 1;

    $members = $pixdb->pullData(
        [
            ['members', 'm', 'id'],
            ['members_info', 'i', 'member']
        ],
        $mConds,
        'm.id,
        m.firstName,
        m.lastName,
        m.memberId,            
        m.role,
        null "sectionName",
        i.cruntChptr "sectionId",
        i.regionId "regionName",
        m.email,
        i.country "country",
        i.state "state",
        i.city,
        i.address,
        i.zipcode,
        i.phone,
        i.deceased',
        true
    )['data'];

    // echo "<h1>Total Users: ", count($members), "</h1>";

    $nations = $pixdb->fetchAssoc(
        'nations',
        [],
        'id, name',
        'id',
        true
    );
    $states = $pixdb->fetchAssoc(
        'states',
        [],
        'id, name',
        'id',
        true
    );
    $regions = $pixdb->fetchAssoc(
        'regions',
        [],
        'id, name',
        'id',
        true
    );
    $chapters = $pixdb->fetchAssoc(
        'chapters',
        [],
        'id, name',
        'id',
        true
    );
    $mshipPlans = $pixdb->fetchAssoc(
        'membership_plans',
        [],
        'id, code',
        'id',
        true
    );

    // preloading memberships
    $memberships = $pixdb->fetchAssoc(
        'memberships',
        [
            '__QUERY__' => [
                '(
                    (
                        giftedBy IS NOT NULL AND 
                        accepted = "Y"
                    ) OR 
                    giftedBy IS NULL
                )',
                'id IN (
                    SELECT max(id) 
                    FROM memberships 
                    GROUP BY member
                )'
            ],
            '#GRP' => 'member'
        ],
        '`member`,
        planId,
        planName,
        created,
        expiry,
        amount,
        installment,
        instllmntPhase',
        'member',
        true
    );

    $memArray = [];
    $curTime = time();

    $x = 0;

    $showDate = function ($str) {
        if ($str) {
            return date('Y-m-d', strtotime($str));
        }
        return '';
    };

    foreach ($members as $ar) {
        $mship = $memberships[$ar['id']] ?? false;
        if (!empty($ar['regOn'])) {
            $ar['regOn'] = '="' . date('d-m-Y h:i A', strtotime($ar['regOn'])) . '"';
        }

        if ($ar['role']) {
            $roles = loadModule('members')->getRoles(
                (object)[
                    'id' => $ar['id'],
                    'role' => $ar['role']
                ]
            );
            $rlStr = '';
            foreach ($roles as $rl) {
                $rlStr .= ($rlStr != '' ? ', ' : '') . $rl->role;
            }
            $ar['role'] = $rlStr;
        }

        $ar['country'] = $nations[$ar['country']]['name'] ?? '';
        $ar['state'] = $states[$ar['state']]['name'] ?? '';
        $ar['regionName'] = $regions[$ar['regionName']]['name'] ?? '';
        $ar['sectionName'] = $chapters[$ar['sectionId']]['name'] ?? '';

        //
        $ar['deceased'] = ($ar['deceased'] == 'Y') ? 'Yes' : 'No';
        //
        $ar['membership'] = '';
        $ar['prodcutCode'] = '';
        $ar['joinDate'] = '';
        $ar['lastPaidDate'] = '';
        $ar['membershipAmt'] = '';
        $ar['totlaLegacy'] = '';
        $ar['totalLife'] = '';
        $ar['expiry'] = '';
        $ar['Status'] = 'Active';

        if ($mship) {
            $ar['membership'] = $mship['planName'];
            $ar['prodcutCode'] = $mshipPlans[$mship['planId']]['code'] ?? '';
            $ar['joinDate'] = $showDate($mship['created']);
            $ar['lastPaidDate'] = $showDate($mship['created']);
            $ar['membershipAmt'] = $mship['amount'];

            if ($mship['planName'] == 'Legacy Life Membership') {
                $ar['totlaLegacy'] = $mship['amount'];
                if ($mship['installment']) {
                    $ar['totlaLegacy'] = (
                        $mship['amount'] /
                        $mship['installment']
                    ) * $mship['instllmntPhase'];
                }
            }
            if ($mship['planName'] == 'Life Membership') {
                $ar['totalLife'] = $mship['amount'];
                if ($mship['installment']) {
                    $ar['totalLife'] = (
                        $mship['amount'] /
                        $mship['installment']
                    ) * $mship['instllmntPhase'];
                }
            }

            $ar['expiry'] = $showDate($mship['expiry']);
            if ($mship['expiry']) {
                if (strtotime($mship['expiry']) < $curTime) {
                    $ar['Status'] = 'Inactive';
                }
            }
        }
        unset($ar['id']);
        $memArray[] = $ar;

        // $x++;
        // if ($x > 10) {
        //     break;
        // }
    }
    // // CSV Head 
    $data = [
        [
            'First Name',
            'Last Name',
            'Member ID',
            'Elected/Appointed Title',
            'Section Name',
            'Section ID',
            'Region Name',
            'Email',
            'Country',
            'State',
            'City',
            'Address',
            'Zip Code',
            'Phone',
            'Deceased',
            'Membership',
            'Product Code',
            'Join Date',
            'Last Paid Date',
            'Membership Amount',
            'Sum of Legacy Life Payment',
            'Sum of Life Payment',
            'Membership Expiry',
            'Status'
        ]
    ];

    $data = array_merge($data, $memArray);

    $saveFile = true;
    if (!$saveFile) {
        printCsvData($data);
    }

    if ($saveFile) {
        $fileDir = 'uploads/tmp-files/export/';
        $fileName = $fileDir . 'Officers_Export_' . date('YmdHis') . '_' . $pix->makestring(30, 'ln') . '.csv';
        $pix->logTmpFile($fileName);

        if (!is_dir(BASEDIR . $fileDir)) {
            mkdir(BASEDIR . $fileDir, 0755, true);
        }

        $file = fopen(BASEDIR . $fileName, 'w');
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        // redirect
        $pix->remsg();
        $pix->redirect(DOMAIN . $fileName);
    }
})();
