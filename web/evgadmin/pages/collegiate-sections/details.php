<?php
$csId = esc($_GET['id'] ?? 0);

//Collecting collegiate data
if ($csId) {
    $csData = $pixdb->get(
        'collegiate_sections',
        [
            'id' => $csId,
            'single' => 1
        ]
    );
}
if (!$csData) {
    $pix->addmsg('Unknown collegiate section');
    $pix->redirect('?page=collegiate-sections');
}

$cntInfo = $pixdb->get(
    'members_info',
    [
        'collegiateSection' => $csId,
        'single' => 1
    ],
    'COUNT(collegiateSection) AS cnt'
);

$pgData = (object)[
    'csId' => $csData->id
];

echo '<script> var pgData = ' . json_encode($pgData) . ';</script>';

loadStyle('pages/collegiate/details');
loadScript('pages/collegiate/details');

$leaderInfo = $pixdb->get(
    [
        ['members', 'm', 'id'],
        ['collegiate_leaders', 'c', 'mbrId']
    ],
    [
        'c.coliId' => $csData->id,
        'single' => 1
    ],
    'm.id,
    m.firstName,
    m.lastName,
    m.avatar'
);

?>

<h1 class="mb0">
    Collegiate Section Details
</h1>
<?php
breadcrumbs(
    [
        'Collegiate Sections',
        '?page=collegiate-sections'
    ],
    [
        $csData->name
    ]
);
?>

<div class="af-details">
    <div class="af-box">
        <div class="af-info">
            <div class="af-name">
                <?php echo $csData->name; ?>
            </div>

            <div class="mb15"></div>

            <div class="af-sts mb15">
                <?php
                if ($csData->enabled == 'Y') {
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
                    <?php echo date('F d, Y', strtotime($csData->createdAt)) ?? '--'; ?>
                </div>
            </div>

            <div class="af-ldr">
                <div class="heading">
                    Collegiate Liaison
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
                                <a href="<?php echo ADMINURL . "actions/anyadmin/?method=collegiate-leader-delete&ldrid=$leaderInfo->id&csId=$csId"; ?>" class="confirm">
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
                            'No collegiate liaison added yet.'
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
                    <a href="<?php echo $pix->adminURL . "?page=members&cs_sort=$csData->id"; ?>" class="btn mbr-view">
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
                <a href="<?php echo $pix->adminURL . "?page=collegiate-sections&sec=mod&id=$csData->id"; ?>" class="btn edt">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                    <span>
                        Edit
                    </span>
                </a>
                <a href="<?php echo ADMINURL . "actions/anyadmin/?method=collegiate-delete&id=$csData->id"; ?>" class="btn dlt confirm">
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