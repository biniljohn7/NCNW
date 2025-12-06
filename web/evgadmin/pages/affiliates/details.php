<?php
$affId = esc($_GET['id'] ?? 0);

//Collecting affiliate data
if ($affId) {
    $affData = $pixdb->get(
        'affiliates',
        [
            'id' => $affId,
            'single' => 1
        ]
    );
}
if (!$affData) {
    $pix->addmsg('Unknown affiliate');
    $pix->redirect('?page=affiliates');
}

$cntInfo = $pixdb->get(
    'members_affiliation',
    [
        'affiliation' => $affId,
        'single' => 1
    ],
    'COUNT(member) AS cnt'
);

$pgData = (object)[
    'affId' => $affData->id
];

echo '<script> var pgData = ' . json_encode($pgData) . ';</script>';

loadStyle('pages/affiliate/details');
loadScript('pages/affiliate/details');

$leaderInfo = $pixdb->get(
    [
        ['members', 'm', 'id'],
        ['affiliate_leaders', 'a', 'mbrId']
    ],
    [
        'a.affId' => $affData->id,
        'single' => 1
    ],
    'm.id,
    m.firstName,
    m.lastName,
    m.avatar'
);
?>

<h1 class="mb0">
    Affiliate Details
</h1>
<?php
breadcrumbs(
    [
        'Affiliates',
        '?page=affiliates'
    ],
    [
        $affData->name
    ]
);
?>

<div class="af-details">
    <div class="af-box">
        <div class="af-info">
            <div class="af-name">
                <?php echo $affData->name; ?>
            </div>

            <div class="mb15"></div>

            <div class="af-sts mb15">
                <?php
                if ($affData->enabled == 'Y') {
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

            <div class="af-date items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        fact_check
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo date('F d, Y', strtotime($affData->createdAt)) ?? '--'; ?>
                </div>
            </div>

            <div class="af-ldr">
                <div class="heading">
                    Affiliate Leader
                </div>
                <div class="ldr-sec">
                    <?php
                    if ($leaderInfo) {
                    ?>
                        <div class="ech-ldr">
                            <div class="avatar-sec">
                                <?php
                                echo $leaderInfo->avatar ?
                                    '<div class="mbr-img">
                                        <img src="' . $evg->getAvatar($leaderInfo->avatar) . '" alt="" />
                                    </div>' :
                                    '<div class="no-img">
                                        <span class="material-symbols-outlined icn">
                                            person
                                        </span>
                                    </div>';
                                ?>
                            </div>
                            <div class="nam-sec"><?php echo $leaderInfo->firstName, ' ', $leaderInfo->lastName; ?></div>
                            <div class="btn-sec">
                                <a href="<?php echo $pix->adminURL . "?page=members&sec=details&id=$leaderInfo->id" ?>" target="_blank" class="mr5">
                                    <span class="material-symbols-outlined icn">
                                        visibility
                                    </span>
                                </a>
                                <a href="<?php echo ADMINURL . "actions/anyadmin/?method=affiliate-leader-delete&ldrid=$leaderInfo->id&affid=$affId"; ?>" class="confirm">
                                    <span class="material-symbols-outlined icn">
                                        delete
                                    </span>
                                </a>
                            </div>
                        </div>
                    <?php
                    } else {
                        NoResult(
                            '',
                            'No leader added yet.'
                        );
                    }
                    ?>
                </div>

                <?php
                if (!$leaderInfo) {
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
                    <a href="<?php echo $pix->adminURL . "?page=members&af_sort=$affData->id"; ?>" class="btn mbr-view">
                        <span class="material-symbols-outlined icn">
                            visibility
                        </span>
                        <span class="txt">
                            View All
                        </span>
                    </a>
                </div>
            </div>

            <div class="af-actions">
                <a href="<?php echo $pix->adminURL . "?page=affiliates&sec=mod&id=$affData->id"; ?>" class="btn edt">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                    <span>
                        Edit
                    </span>
                </a>
                <a href="<?php echo ADMINURL . "actions/anyadmin/?method=affiliate-delete&id=$affData->id"; ?>" class="btn dlt confirm">
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