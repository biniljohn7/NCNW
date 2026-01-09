<?php
(function ($pix, $pixdb, $evg, $date) {
    $pgn = max(0, intval($_GET['pgn'] ?? 0));
    $trConds = [
        '#SRT' => 'id desc',
        '__page' => $pgn,
        '__limit' => 20,
        '__QUERY__' => array()
    ];
    $sectionDrop = [];
    $statesDrop = [];
    $planDrop = [];
    $affDrop = [];
    $prodDrop = [];
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
    $states = $pixdb->get(
        'states',
        ['#SRT' => 'name asc'],
        'id, name'
    );
    foreach ($states->data as $row) {
        $statesDrop[$row->id] = $row->name;
    }
    //
    $plans = $pixdb->get(
        'membership_plans',
        ['#SRT' => 'title asc'],
        'code, title'
    );
    foreach ($plans->data as $row) {
        $planDrop[$row->code] = $row->title;
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
    $products = $pixdb->get(
        'products',
        ['#SRT' => 'name asc'],
        'code, name'
    );
    foreach ($products->data as $row) {
        $prodDrop[$row->code] = $row->name;
    }
    //
    $adnlqry = [];
    //
    //Transaction id search            
    $txnSearch = esc($_GET['txn_search'] ?? '');
    if ($txnSearch) {
        $qtxn = q("%$txnSearch%");
        $adnlqry[] = 'txnid LIKE ' . $qtxn;
    }
    //member
    $member = esc($_GET['member'] ?? '');
    if ($member) {
        //$qmem = q("%$memSearch%");
        $adnlqry[] = 'member = ' . $member;
    }
    //Member id search            
    $memSearch = esc($_GET['mem_search'] ?? '');
    if ($memSearch) {
        $qmem = q("%$memSearch%");
        $adnlqry[] = 'member IN (SELECT id FROM members WHERE memberId LIKE ' . $qmem . ')';
    }
    // Section search
    $shSecSearch = esc($_GET['sh_sec_search'] ?? '');
    if ($shSecSearch) {
        $adnlqry[] = 'id IN (SELECT txnId FROM txn_items WHERE sectionId IN(SELECT id FROM chapters WHERE id = ' . $shSecSearch . '))';
    }
    ///State Search
    $stateKey = esc($_GET['st_sort'] ?? '');
    if ($stateKey) {
        $adnlqry[] = 'id IN (SELECT txnId FROM txn_items WHERE sectionId IN(SELECT id FROM chapters WHERE state = ' . $stateKey . '))';
    }
    //Affilations / organizations search
    $affliateKey = esc($_GET['af_sort'] ?? '');
    if ($affliateKey) {
        $adnlqry[] = 'id IN (SELECT txnId FROM txn_items WHERE affiliateId= ' . $affliateKey . ')';
    }
    //Membership plan
    $memPlanKey = esc($_GET['mp_sort'] ?? '');
    if ($memPlanKey) {
        $adnlqry[] = 'id IN (SELECT txnId FROM txn_items WHERE pdtCode= "' . $memPlanKey . '")';
    }
    //Product search
    $prodKey = esc($_GET['prod_sort'] ?? '');
    if ($prodKey) {
        $adnlqry[] = 'id IN (SELECT txnId FROM txn_items WHERE pdtCode= "' . $prodKey . '")';
    }
    // date covenrt to yyyy-mm-dd format 
    $formatDate = function ($dateString) {
        return date('Y-m-d', strtotime(str_replace(' / ', '-', $dateString)));
    };


    $shTrStr = esc($_GET['sh_trStr'] ?? '');
    $shTrEnd = esc($_GET['sh_trEnd'] ?? '');
    if (
        $shTrStr != '' &&
        $shTrEnd != ''
    ) {
        $shTrStr = $formatDate($shTrStr);
        $shTrEnd = $formatDate($shTrEnd);

        $adnlqry[] = 'date BETWEEN "' . $shTrStr . ' 00:00:00" AND "' . $shTrEnd . ' 23:59:00"';
    } elseif ($shTrStr != '') {
        $shTrStr = $formatDate($shTrStr);
        $adnlqry[] = 'date >= "' . $shTrStr . ' 00:00:00"';
    } elseif ($shTrEnd != '') {
        $shTrEnd = $formatDate($shTrEnd);
        $adnlqry[] = 'date <= "' . $shTrEnd . ' 23:59:00"';
    }

    // search by amount
    /* $shAmtfrm = max(0, floatval($_GET['sh_amtfrm'] ?? 0));
    $shAmtupto = max(0, floatval($_GET['sh_amtUpto'] ?? 0));
    if ($shAmtfrm && $shAmtupto) {
        $adnlqry[] = 'amount BETWEEN "' . $shAmtfrm . '" AND "' . $shAmtupto . '"';
    } elseif ($shAmtfrm) {
        $adnlqry[] = 'amount >= ' . $shAmtfrm;
    } elseif ($shAmtupto) {
        $adnlqry[] = 'amount <= ' . $shAmtupto;
    }
    $shAmtfrm = $shAmtfrm == 0 ? '' : $shAmtfrm;
    $shAmtupto = $shAmtupto == 0 ? '' : $shAmtupto; */

    // search by status
    $shStatus = esc($_GET['sh_status'] ?? '');
    if ($shStatus) {
        $adnlqry[] = 'status=' . q($shStatus);
    }

    //  filter by membership status
    $mbrSts = esc($_GET['mbr-sts'] ?? '');
    if ($mbrSts) {
        $mbrIds = [];

        if ($mbrSts == 'act') {
            $filtrConds = ' AND m.enabled = "Y" AND (expiry IS NULL OR expiry >= "' . $date . '")';
        } elseif ($mbrSts == 'inact') {
            $filtrConds = ' AND m.enabled = "N"';
        }

        $membActive = $pixdb->fetchAll(
            'SELECT m.member
            FROM memberships m
            WHERE (
                m.created = (
                    SELECT MAX(created)
                    FROM memberships
                    WHERE member = m.member
                    AND created IS NOT NULL
                    AND (
                        (giftedBy IS NOT NULL AND accepted = "Y")
                        OR giftedBy IS NULL
                    )
                )
                OR (
                    m.created IS NULL
                    AND NOT EXISTS (
                        SELECT 1
                        FROM memberships
                        WHERE member = m.member
                        AND created IS NOT NULL
                    )
                )
            )
            ' . $filtrConds . '
            ORDER BY m.created DESC'
        );
        foreach ($membActive as $ma) {
            $mbrIds[] = $ma->member;
        }

        if ($mbrIds) {
            $adnlqry[] = 'member IN (' . implode(', ', $mbrIds) . ')';
        }
    }

    if (!empty($adnlqry)) {
        $trConds['__QUERY__'][] = implode(' and ', $adnlqry);
    }
    $transactions = $pixdb->get(
        'transactions',
        $trConds,
        'id,
        member,
        date,
        amount,
        status,
        title'
    );

    $memberIds = [];
    foreach ($transactions->data as $row) {
        if ($row->member) {
            $memberIds[] = $row->member;
        }
    }

    $memberDatas = $evg->getMembers($memberIds, 'id,firstName,lastName,avatar');
    $trStatus = [
        'success' => 'Successful',
        'pending' => 'Pending'
    ];
    loadStyle('pages/transactions/list');
    loadScript('pages/transactions/list');
?>
    <h1>Transactions List</h1>
    <?php
    breadcrumbs(
        [
            'Transactions'
        ]
    )
    ?>

    <div class="trans-hed">
        <div class="hed-left">
            <?php
            echo $transactions->totalRows;
            echo ' Transaction', $transactions->totalRows > 1 ? 's' : '';
            ?>
        </div>
        <div class="hed-right">
            <span class="pix-btn site rounded mr5" id="listFilterButton">
                <span class="material-symbols-outlined fltr">
                    page_info
                </span>
                Filter Results
            </span>
        </div>
    </div>
    <?php
    filterBubble(
        array(
            'items' => array(
                'txn_search' => array(
                    'label' => 'Transaction ID'
                ),
                'st_sort' => array(
                    'label' => 'State',
                    'value' => $statesDrop
                ),
                'sh_sec_search' => array(
                    'label' => 'Section',
                    'value' => $sectionDrop
                ),
                'af_sort' => array(
                    'label' => 'Organizations',
                    'value' => $affDrop
                ),
                'mp_sort' => array(
                    'label' => 'Membership Plan',
                    'value' => $planDrop
                ),
                'prod_sort' => array(
                    'label' => 'Payment Category',
                    'value' => $prodDrop
                ),
                'sh_status' => array(
                    'label' => 'Status',
                    'value' => [
                        'pending' => 'Pending',
                        'success' => 'Success'
                    ]
                ),
                'sh_trStr' => array(
                    'label' => 'Start Date',
                ),
                'sh_trEnd' => array(
                    'label' => 'End Date',
                ),

            )
        )
    );
    $pix->pagination(
        $transactions->pages,
        $pgn,
        5,
        null,
        'pt30 mb50 text-left'
    );
    ?>
    <div class="transaction-card" style="display:<?php echo $transactions->pages > 0 ? 'block' : 'none'; ?>">
        <?php
        foreach ($transactions->data as $row) {
            $dtlLink = $pix->adminURL . '?page=transactions&sec=details&id=' . $row->id;
            $firstName = $memberDatas[$row->member]->firstName ?? '--';
            $lastName = $memberDatas[$row->member]->lastName ?? '--';
        ?>
            <div class="transaction-list">
                <a href="<?php echo $dtlLink; ?>">
                    <span class="trtn-item left">
                        <span class="trtn-itm id">
                            <span class="itm top">
                                #<span><?php echo $row->id; ?></span>
                            </span>
                            <span class="itm btm">
                                <?php
                                echo date('d M Y', strtotime($row->date));
                                ?>
                                at
                                <?php
                                echo date('H:i A', strtotime($row->date));
                                ?>
                            </span>
                        </span>
                        <span class="trtn-itm price">
                            <span class="itm top">
                                Amount
                            </span>
                            <span class="itm btm">
                                <?php echo dollar($row->amount); ?>
                            </span>
                        </span>
                    </span>
                    <span class="trtn-item right">
                        <span class="trtn-item memb">
                            <span class="memb-thumb">
                                <?php
                                if (isset($memberDatas[$row->member]->avatar)) {
                                ?>
                                    <img src="<?php echo $evg->getAvatar($memberDatas[$row->member]->avatar); ?>">
                                <?php
                                } else {
                                ?>
                                    <span class="no-thumb">
                                        <span class="material-symbols-outlined no-thmb">
                                            person
                                        </span>
                                    </span>
                                <?php
                                }
                                ?>
                            </span>
                            <span class="itm-info">
                                <span class="itm top">
                                    Member
                                </span>
                                <span class="itm btm">
                                    <?php
                                    echo $firstName, ' ', $lastName;
                                    ?>
                                </span>
                            </span>
                        </span>
                        <span class="trtn-item status">
                            <span class="itm top">
                                Status
                            </span>
                            <?php
                            if ($row->status) {
                                $sts = $row->status;
                            ?>
                                <span class="itm btm sts <?php echo $sts; ?>">
                                    <span class="material-symbols-outlined">
                                        <?php echo $sts == 'success' ? 'check_circle' : 'pending' ?>
                                    </span>
                                    <?php
                                    echo $trStatus[$sts] ?? $sts;
                                    ?>
                                </span>
                            <?php
                            }
                            ?>
                        </span>
                        <span class="trtn-item title">
                            <span class="itm top">
                                Title
                            </span>
                            <span class="itm btm">
                                <?php
                                echo $row->title;
                                ?>
                            </span>
                        </span>
                    </span>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
    <?php
    $pix->pagination(
        $transactions->pages,
        $pgn,
        5,
        null,
        'pt30 mb50 text-left'
    );
    if (!$transactions->pages) {
        NoResult(
            'No transactions found',
            'We couldn\'t find any results. Maybe try a new search.'
        );
    }
    ?>
<?php
    $filterOptions = [
        [
            'type' => 'hidden',
            'name' => 'page',
            'value' => 'transactions',
        ],
        [
            'type' => 'text',
            'label' => 'Transaction ID',
            'name' => 'txn_search',
            'getKey' => 'txn_search',
            'autocomplete' => 'off'
        ],
        [
            'type' => 'text',
            'label' => 'Member ID',
            'name' => 'mem_search',
            'getKey' => 'mem_search',
            'autocomplete' => 'off'
        ],
        [
            'type' => 'select',
            'label' => 'Section',
            'name' => 'sh_sec_search',
            'getKey' => 'sh_sec_search',
            'option' => $sectionDrop
        ],
        [
            'type' => 'select',
            'label' => 'State',
            'name' => 'st_sort',
            'getKey' => 'st_sort',
            'option' => $statesDrop
        ],
        [
            'type' => 'select',
            'label' => 'Organization',
            'name' => 'af_sort',
            'getKey' => 'af_sort',
            'option' => $affDrop
        ],
        [
            'type' => 'select',
            'label' => 'Membership Plans',
            'name' => 'mp_sort',
            'getKey' => 'mp_sort',
            'option' => $planDrop
        ],
        [
            'type' => 'select',
            'label' => 'Payment Categories',
            'name' => 'prod_sort',
            'getKey' => 'prod_sort',
            'option' => $prodDrop
        ],
        [
            'type' => 'date-range',
            'label' => 'Transaction Date',
            'name' => [
                'sh_trStr',
                'sh_trEnd'
            ],
            'getKey' => [
                'sh_trStr',
                'sh_trEnd'
            ]
        ],
        /* [
            'type' => 'number-range',
            'rangeType' => 'text',
            'label' => 'Amount',
            'name' => [
                'sh_amtfrm',
                'sh_amtUpto'
            ],
            'getKey' => [
                'sh_amtfrm',
                'sh_amtUpto'
            ]
        ], */
        [
            'type' => 'radio-group',
            'label' => 'Membership Status',
            'name' => 'mbr-sts',
            'getKey' => 'mbr-sts',
            'options' => [
                ['Any', '', true],
                ['Active', 'act'],
                ['Inactive', 'inact']
            ]
        ],
        [
            'type' => 'radio-group',
            'label' => 'Status',
            'name' => 'sh_status',
            'getKey' => 'sh_status',
            'options' => [
                ['Any', '', true],
                ['Pending', 'pending'],
                ['Success', 'success'],
                ['Cancelled', 'cancelled']
            ]
        ]
    ];
    sidebarFilter(
        'Filter Transactions',
        $filterOptions
    );
    StickMenu(

        [
            'upgrade',
            'Export Data',
            0,
            0,
            'actions/anyadmin/?' . http_build_query([
                'method' => 'transactions-export',
                'txn_search' => $txnSearch,
                'mem_search' => $memSearch,
                'sh_sec_search' => $shSecSearch,
                'st_sort' => $stateKey,
                'af_sort' => $affliateKey,
                'mp_sort' => $memPlanKey,
                'prod_sort' => $prodKey,
                'sh_trStr' => $shTrStr,
                'sh_trEnd' => $shTrEnd,
                'sh_status' => $shStatus,
                'member' => $member
            ]),
            '_blank'
        ],
        $pix->canAccess('pos') ? [
            'add_business',
            'POS',
            0,
            0,
            '?page=transactions&sec=cart'
        ] : null

    );
})($pix, $pixdb, $evg, $date);
?>