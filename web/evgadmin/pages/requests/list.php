<?php
(function ($pix, $pixdb, $evg) {
    $pgn = $pix->getPageNum();
    $sts = esc($_GET['list'] ?? '') ?: 'all';

    $allAdmins = [];
    $rCond = [];
    $flterAdmn = null;

    $hdesk = loadModule('helpdesk');

    $admins = $pixdb->get(
        'admins',
        ['#SRT' => 'name asc'],
        'id, name'
    );

    foreach ($admins->data as $row) {
        $allAdmins[$row->id] = $row->name;
    }

    if (isset($_GET['sh_raised'])) {
        $admnId = esc($_GET['sh_raised']);
        if (isset($allAdmins[$admnId])) {
            $flterAdmn = $allAdmins[$admnId];
        }
    }

    // Key search
    $shSearch = esc($_GET['sh_search'] ?? '');
    if ($shSearch) {
        $qs = q('%' . $shSearch . '%');
        $rCond[] = "(
            id like $qs or
            summary like $qs or
            `desc` like $qs or
            impact like $qs or
            comments like $qs
        )";
    }

    // Type search
    $shType = esc($_GET['sh_type'] ?? '');
    if ($shType) {
        switch ($shType) {
            case 'change':
                $rCond[] = 'reqType = "change"';
                break;
            case 'bug':
                $rCond[] = 'reqType = "bug"';
                break;
        }
    }

    // Priority search
    $shPriority = esc($_GET['sh_priority'] ?? '');
    if ($shPriority) {
        switch ($shPriority) {
            case 'low':
                $rCond[] = 'priority = "low"';
                break;
            case 'medium':
                $rCond[] = 'priority = "medium"';
                break;
            case 'high':
                $rCond[] = 'priority = "high"';
                break;
        }
    }

    $shPriorities = $_GET['sh_priorities'] ?? [];
    if (!empty($shPriorities)) {
        $shPriorities = array_filter(
            array_unique(
                array_map(
                    function ($itm) {
                        return "'" . esc($itm) . "'";
                    },
                    $shPriorities
                )
            )
        );
        $qs = implode(',', $shPriorities);
        $rCond[] = "priority IN($qs)";
    }
    // Status search
    $shStatuses = $_GET['sh_statuses'] ?? [];
    if (!empty($shStatuses)) {
        $shStatuses = array_filter(
            array_unique(
                array_map(
                    function ($itm) {
                        return "'" . esc($itm) . "'";
                    },
                    $shStatuses
                )
            )
        );
        $qs = implode(',', $shStatuses);
        $rCond[] = "status IN($qs)";
    }
    // Reised By search
    $shRaised = esc($_GET['sh_raised'] ?? '');
    if ($shRaised) {
        $rCond[] = 'raisedBy = ' . $shRaised;
    }

    $formatDate = function ($dateString) {
        return date('Y-m-d', strtotime(
            str_replace(' / ', '-', $dateString)
        ));
    };

    // Created On search
    $shCrtFrm = esc($_GET['sh_crtFrm'] ?? '');
    if ($shCrtFrm) {
        $shCrtFrm = $formatDate($shCrtFrm);
        $rCond[] = 'createdAt >= ' . q($shCrtFrm . ' 00:00:00');
    }

    $shCrtEnd = esc($_GET['sh_crtEnd'] ?? '');
    if ($shCrtEnd) {
        $shCrtEnd = $formatDate($shCrtEnd);
        $rCond[] = 'createdAt <= ' . q($shCrtEnd . ' 23:59:59');
    }

    // Last Activity search
    $shLacFrm = esc($_GET['sh_lacFrm'] ?? '');
    if ($shLacFrm) {
        $shLacFrm = $formatDate($shLacFrm);
        $rCond[] = 'lastActivity >= ' . q($shLacFrm . ' 00:00:00');
    }

    $shLacEnd = esc($_GET['sh_lacEnd'] ?? '');
    if ($shLacEnd) {
        $shLacEnd = $formatDate($shLacEnd);
        $rCond[] = 'lastActivity <= ' . q($shLacEnd . ' 23:59:59');
    }

    // Sorting
    $sortQry = 'id desc';
    $shSort = esc($_GET['sh_sort'] ?? '');
    if ($shSort) {
        switch ($shSort) {
            case 'new':
                $sortQry = 'createdAt desc';
                break;
            case 'older':
                $sortQry = 'createdAt asc';
                break;
            case 'asc':
                $sortQry = 'id asc';
                break;
            case 'desc':
                $sortQry = 'id desc';
                break;
        }
    }

    if ($sts == 'pending') {
        $rCond[] = "status NOT IN(
            'open',
            'resolved'
        )";
    }

    if ($sts == 'resolved') {
        $rCond[] = "status IN(
            'resolved'
        )";
    }

    $listConds = [
        '__QUERY__' => [],
        '__page' => $pgn,
        '__limit' => 20,
        '#SRT' => $sortQry
    ];
    if (!empty($rCond)) {
        $listConds['__QUERY__'][] = implode(' and ', $rCond);
    }
    $reqList = $pixdb->get(
        'help_desk',
        $listConds,
        'id,
        status,
        priority,
        reqType,
        summary,
        lastActivity,
        raisedBy,
        attachments,
        ttlComments,
        createdAt'
    );

    $raisedIds = [];
    foreach ($reqList->data as $row) {
        if ($row->raisedBy) {
            $raisedIds[] = $row->raisedBy;
        }
    }
    $raisedData = $evg->getAnyAdmins($raisedIds, 'id, name, type');

    $reqIcn = [
        // 'open' => 'folder_open',
        // 'in-process' => 'pending_actions',
        // 'resolved' => 'check',
        'bug' => 'star',
        'change' => 'bolt',

    ];
    $iconsReq = [
        'pending' => 'work_alert',
        'estimated' => 'price_check',
        'est-verified' => 'preliminary',
        'approved' => 'priority',
        'started' => 'play_arrow',
        'deployed' => 'deployed_code',
        'revise-update' => 'cycle',
        'resolved' => 'add_task',
    ];

    $sortOps = [
        'new' => 'Newer Firts',
        'older' => 'Older First',
        'asc' => 'Ascending',
        'desc' => 'Descending'
    ];

    loadStyle('pages/request/list');
    !$pix->canAccess('helpdesk/ncnw-team') ?:
        StickyButton($pix->adminURL . '?page=requests&sec=mod&id=new', 'add');
?>
    <div class="req-hed">
        <div class="hed-lf">
            <h1>
                Requests
            </h1>
            <?php
            breadcrumbs(
                ['Requests']
            );
            ?>
        </div>
        <div class="hed-rt">
            <span class="pix-btn site rounded mr5" id="listFilterButton">
                <span class="material-symbols-outlined fltr">
                    page_info
                </span>
                Filter Results
            </span>
        </div>
    </div>
    <div class="top-sec">
        <?php
        TabGroup(
            [
                [
                    'pending_actions',
                    'Pending',
                    ADMINURL . '?page=requests&list=pending',
                    $sts == 'pending'
                ],
                [
                    'verified',
                    'Resolved',
                    ADMINURL . '?page=requests&list=resolved',
                    $sts == 'resolved'
                ],
                [
                    'description',
                    'All',
                    ADMINURL . '?page=requests',
                    $sts == 'all'
                ]
            ]
        );
        ?>
        <div class="srch-fltr-box">
            <form action="" class="flt-form" method="get">
                <input type="hidden" name="page" value="requests" />
                <?php
                if ($sts != 'all') {
                    echo '<input type="hidden" name="list" value="', $sts, '" />';
                }
                ?>
                <div class="req-select type">
                    <select name="sh_type">
                        <option value="">
                            Any Type
                        </option>
                        <?php
                        foreach ($evg::reqTypes as $key => $val) {
                            echo '<option ', $shType == $key ? 'selected' : '', ' value="', $key, '">', $val, '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="req-select priority">
                    <select name="sh_priority">
                        <option value="">
                            Any Priority
                        </option>
                        <?php
                        foreach ($evg::priorities as $key => $val) {
                            echo '<option ', $shPriority == $key ? 'selected' : '', ' value="', $key, '">', $val, '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
                KeySearch(
                    'sh_search',
                    $shSearch,
                    'Search'
                );
                ?>
            </form>
        </div>
    </div>
    <?php
    filterBubble(
        [
            'items' => [
                'sh_search' => [
                    'label' => 'Keyword'
                ],
                'sh_type' => [
                    'label' => 'Type'
                ],
                'sh_priority' => [
                    'label' => 'Priority'
                ],
                'sh_priorities' => [
                    'label' => 'Priority'
                ],
                'sh_statuses' => [
                    'label' => 'Status'
                ],
                'sh_raised' => [
                    'label' => 'Raised By',
                    'value' => $flterAdmn
                ],
                'sh_crtFrm' => [
                    'label' => 'Created from'
                ],
                'sh_crtEnd' => [
                    'label' => 'Created upto'
                ],
                'sh_lacFrm' => [
                    'label' => 'Last activity from'
                ],
                'sh_lacEnd' => [
                    'label' => 'Last activity upto'
                ],
                'sh_sort' => [
                    'label' => 'Sort By',
                    'value' => $sortOps
                ]
            ]
        ]
    );

    $pix->pagination(
        $reqList->pages,
        $pgn,
        5,
        null,
        'text-left mb50 pt30'
    );
    ?>
    <div class="request-list">
        <?php
        foreach ($reqList->data as $req) {
            $isAdmin = ($raisedData[$req->raisedBy]->type ?? '') == 'admin';
        ?>
            <div class="req-card">
                <div class="card tp">
                    <div class="top left">
                        <div class="itm req-id">
                            <a href="<?php echo ADMINURL, '?page=requests&sec=details&id=' . $req->id; ?>">
                                <?php echo '#' . $req->id; ?>
                            </a>
                        </div>
                        <div class="itm">
                            <div class="req-sts <?php echo $req->status; ?>">
                                <span class="material-symbols-outlined icn">
                                    <?php echo $iconsReq[$req->status] ?? ''; ?>
                                </span>
                                <span class="">
                                    <?php echo $hdesk->getStatusName($req->status); //ucfirst($req->status); 
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="top right">
                        <div class="itm req-pri">
                            <span class="material-symbols-outlined icn <?php echo $req->priority; ?>">
                                fiber_manual_record
                            </span>
                            <span class="">
                                <?php echo $evg::priorities[$req->priority]; ?>
                            </span>
                        </div>
                        <div class="itm req-type">
                            <span class="material-symbols-outlined icn <?php echo $req->reqType; ?>">
                                <?php echo $reqIcn[$req->reqType]; ?>
                            </span>
                            <span class="">
                                <?php echo $evg::reqTypes[$req->reqType]; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card mid">
                    <?php echo ReadFullText($req->summary, 350); ?>
                </div>
                <div class="card btm">
                    <div class="btm-left">
                        <div class="btmlf left">
                            <div class="item req-last-act">
                                <div class="itm">
                                    <span class="material-symbols-outlined icn">
                                        calendar_today
                                    </span>
                                </div>
                                <div class="itm-info">
                                    <div class="itm-label">
                                        Last Activity
                                    </div>
                                    <div class="itm-val">
                                        <?php
                                        echo !empty($req->lastActivity) ?
                                            date('d M Y H:i A', strtotime($req->lastActivity))
                                            :
                                            date('d M Y H:i A', strtotime($req->createdAt));
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="item req-raised">
                                <div class="itm raised <?php echo $isAdmin ? 'admin' : ''; ?>">
                                    <?php
                                    echo $isAdmin ?
                                        '<img src="' . $pix->adminURL . 'assets/images/evergreen-logo.png' . '">' :
                                        strtoupper(
                                            substr(
                                                $raisedData[$req->raisedBy]->name,
                                                0,
                                                2
                                            )
                                        );
                                    ?>
                                </div>
                                <div class="itm-info">
                                    <div class="itm-label">
                                        Raised By
                                    </div>
                                    <div class="itm-val">
                                        <?php echo $raisedData[$req->raisedBy]->name ?? 'Anonymous User'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btmlf right">
                            <div class="item attach">
                                <div class="itm">
                                    <span class="material-symbols-outlined icn attch">
                                        attach_file
                                    </span>
                                </div>
                                <div class="itm-info">
                                    <div class="itm-label">
                                        Attachment<?php echo $req->attachments > 1 ? 's' : ''; ?>
                                    </div>
                                    <div class="itm-val">
                                        <?php echo $req->attachments; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="item cmnts">
                                <div class="itm">
                                    <span class="material-symbols-outlined icn">
                                        chat_bubble
                                    </span>
                                </div>
                                <div class="itm-info">
                                    <div class="itm-label">
                                        Comment<?php echo $req->ttlComments > 1 ? 's' : ''; ?>
                                    </div>
                                    <div class="itm-val">
                                        <?php echo $req->ttlComments; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btm-right">
                        <a href="<?php echo ADMINURL, '?page=requests&sec=details&id=' . $req->id; ?>" class="pix-btn sm primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
<?php
    $pix->pagination(
        $reqList->pages,
        $pgn,
        5,
        null,
        'pt30 mb50 text-left'
    );
    if (!$reqList->pages) {
        NoResult(
            'No request found',
            'We couldn\'t find any results. Maybe try a new search.'
        );
    }
    sidebarFilter(
        'Filter Requests',
        [
            [
                'type' => 'hidden',
                'name' => 'page',
                'value' => 'requests'
            ],
            [
                'type' => 'text',
                'label' => 'Keywords',
                'name' => 'sh_search',
                'getKey' => 'sh_search',
                'autocomplete' => 'off'
            ],
            [
                'type' => 'radio-group',
                'label' => 'Type',
                'name' => 'sh_type',
                'getKey' => 'sh_type',
                'options' => [
                    ['Any', '', true],
                    ['Change Request', 'change'],
                    ['System Error/Bug', 'bug']
                ]
            ],
            [
                'type' => 'check-group',
                'label' => 'Status',
                'name' => 'sh_statuses',
                'getKey' => 'sh_statuses',
                'options' => [
                    ['Pending', 'pending'],
                    ['Estimated', 'estimated'],
                    ['Estimate Verified', 'est-verified'],
                    ['Approved', 'approved'],
                    ['Started', 'started'],
                    ['Deployed', 'deployed'],
                    ['Revise Update', 'revise-update'],
                    ['Resolved', 'resolved']
                ]
            ],
            [
                'type' => 'check-group',
                'label' => 'Priority',
                'name' => 'sh_priorities',
                'getKey' => 'sh_priorities',
                'options' => [
                    ['Low', 'low'],
                    ['Medium', 'medium'],
                    ['High', 'high']
                ]
            ],
            [
                'type' => 'select',
                'label' => 'Raised By',
                'name' => 'sh_raised',
                'getKey' => 'sh_raised',
                'option' => $allAdmins
            ],
            [
                'type' => 'date-range',
                'label' => 'Created On',
                'name' => ['sh_crtFrm', 'sh_crtEnd'],
                'getKey' => ['sh_crtFrm', 'sh_crtEnd']
            ],
            [
                'type' => 'date-range',
                'label' => 'Last Activity',
                'name' => ['sh_lacFrm', 'sh_lacEnd'],
                'getKey' => ['sh_lacFrm', 'sh_lacEnd']
            ],
            [
                'type' => 'select',
                'label' => 'Sort By',
                'name' => 'sh_sort',
                'getKey' => 'sh_sort',
                'option' => $sortOps
            ]
        ]

    );
})($pix, $pixdb, $evg);
