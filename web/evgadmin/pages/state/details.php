<?php
$sid = esc($_GET['id'] ?? 0);

//Collecting state data
if ($sid) {
    $stData = $pixdb->get(
        'states',
        [
            'id' => $sid,
            'single' => 1
        ]
    );
}
if (!$stData) {
    $pix->addmsg('Unknown state');
    $pix->redirect('?page=state');
}

$regnDta = $evg->getRegions($stData->region, 'id,name,nation');
$nationIds = [];
foreach ($regnDta as $row) {
    if ($row->nation) {
        $nationIds[] = $row->nation;
    }
}
$ntData = $evg->getNations($nationIds, 'id,name');

$cntInfo = $pixdb->getRow(
    [
        ['members', 'm', 'id'],
        ['members_info', 'mi', 'member']
    ],
    [
        '#QRY' => 'm.enabled="Y"',
        'state' => $sid
    ],
    'COUNT(state) AS cnt'
);

$pgData = (object)[
    'stId' => $stData->id
];

echo '<script> var pgData = ' . json_encode($pgData) . ';</script>';

loadStyle('pages/state/details');
loadScript('pages/state/details');

$leaders = $pixdb->getCol(
    'state_leaders',
    ['stateId' => $stData->id],
    'mbrId'
);

$leaderInfo = [];
if (!empty($leaders)) {
    $leaderInfo = $pixdb->get(
        'members',
        ['#QRY' => 'id in(' . implode(', ', $leaders) . ')'],
        'id, firstName, lastName, avatar'
    )->data;
}
?>
<h1 class="mb0">
    State Details
</h1>
<?php
breadcrumbs(
    [
        'States',
        '?page=state'
    ],
    [
        $stData->name
    ]
);

$rDatas = $regnDta[$stData->region] ?? null;
$nDatas = $rDatas ? $ntData[$rDatas->nation] ?? null : null;
?>


<div class="state-details">
    <div class="st-box">
        <div class="st-info">
            <div class="st-name">
                <?php echo $stData->name; ?>
            </div>

            <div class="st-region items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        explore
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo $rDatas->name ?? 'Unknown'; ?>
                </div>
            </div>

            <div class="st-cntry items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        flag
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo $nDatas->name ?? 'Unknown'; ?>
                </div>
            </div>

            <div class="mb15"></div>

            <div class="st-sts mb15">
                <?php
                if ($stData->enabled == 'Y') {
                ?>
                    <span class="sts active">
                        Active
                    </span>
                <?php
                } else {
                ?>
                    <span class="sts inactive">
                        Inactive
                    </span>
                <?php
                }
                ?>
            </div>

            <div class="st-date items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        fact_check
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo date('F d, Y', strtotime($stData->createdAt)) ?? '--'; ?>
                </div>
            </div>

            <div class="state-ldr">
                <div class="heading">
                    State Leaders
                </div>
                <div class="ldr-sec">
                    <?php
                    if (!empty($leaderInfo)) {
                        foreach ($leaderInfo as $ldr) {
                    ?>
                            <div class="ech-ldr">
                                <div class="avatar-sec">
                                    <?php
                                    echo $ldr->avatar ?
                                        '<div class="mbr-img">
                                        <img src="' . $evg->getAvatar($ldr->avatar) . '" alt="" />
                                    </div>' :
                                        '<div class="no-img">
                                        <span class="material-symbols-outlined icn">
                                            person
                                        </span>
                                    </div>';
                                    ?>
                                </div>
                                <div class="nam-sec"><?php echo $ldr->firstName, ' ', $ldr->lastName; ?></div>
                                <div class="btn-sec">
                                    <a href="<?php echo $pix->adminURL . "?page=members&sec=details&id=$ldr->id" ?>" target="_blank" class="mr5">
                                        <span class="material-symbols-outlined icn">
                                            visibility
                                        </span>
                                    </a>
                                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=state-leader-delete&ldrid=$ldr->id&stid=$sid"; ?>" class="confirm">
                                        <span class="material-symbols-outlined icn">
                                            delete
                                        </span>
                                    </a>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        NoResult(
                            '',
                            'No leaders added yet.'
                        );
                    }
                    ?>
                </div>

                <?php
                if (count($leaders) < 2) {
                ?>
                    <div class="btn">
                        <span class="pix-btn sm add-ldr" id="addLeader">
                            <span class="material-symbols-outlined icn">
                                person_add
                            </span>
                            <span class="txt">
                                Add
                            </span>
                        </span>
                    </div>
                <?php
                }
                ?>
            </div>

            <div class="members items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        groups
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo $cntInfo->cnt ?? '0', ' members'; ?>
                </div>
                <div class="btn">
                    <a href="<?php echo $pix->adminURL . "?page=members&st_sort=$stData->id"; ?>" class="btn mbr-view">
                        <span class="material-symbols-outlined icn">
                            visibility
                        </span>
                        <span class="txt">
                            View All
                        </span>
                    </a>
                </div>
            </div>

            <div class="st-actions">
                <a href="<?php echo $pix->adminURL . "?page=state&sec=mod&id=$stData->id"; ?>" class="btn edt">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                    <span>
                        Edit
                    </span>
                </a>
                <a href="<?php echo ADMINURL . "actions/anyadmin/?method=state-delete&id=$stData->id"; ?>" class="btn dlt confirm">
                    <span class="material-symbols-outlined icn">
                        delete
                    </span>
                    <span>
                        Delete
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>