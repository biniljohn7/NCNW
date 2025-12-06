<?php
$cid = esc($_GET['id'] ?? 0);

//Collecting chapter data
$founderInfo = false;
if ($cid) {
    $chptrData = $pixdb->get(
        'chapters',
        [
            'id' => $cid,
            'single' => 1
        ]
    );
}
if (!$chptrData) {
    $pix->addmsg('Unknown Section');
    $pix->redirect('?page=chapter');
}

if (
    $chptrData->firstName ||
    $chptrData->lastName ||
    $chptrData->email ||
    $chptrData->phone ||
    $chptrData->note ||
    $chptrData->appliedDate ||
    $chptrData->approvedDate
) {
    $founderInfo = true;
}

//fetch stste here
$stateData = $evg->getState($chptrData->state, 'id,name,region');
$regnDta = $stateData ? $evg->getRegion($stateData->region, 'id,name,nation') : false;
$ntData = $regnDta ? $evg->getNation($regnDta->nation, 'id,name') : false;

$cntInfo = $pixdb->get(
    'members_info',
    [
        'cruntChptr' => $cid,
        'single' => 1
    ],
    'COUNT(cruntChptr) AS cnt'
);

$pgData = (object)[
    'chptrId' => $chptrData->id
];

echo '<script> var pgData = ' . json_encode($pgData) . ';</script>';

loadStyle('pages/chapter/details');
loadScript('pages/chapter/details');

$leaders = $pixdb->get(
    'section_leaders',
    ['secId' => $chptrData->id]
);

$ldrIds = [];
foreach ($leaders->data as $ldr) {
    $ldrIds[] = $ldr->mbrId;
}

$leaderInfo = [];

if (!empty($ldrIds)) {
    $leaderInfo = $pixdb->fetchAssoc(
        'members',
        ['#QRY' => 'id in(' . implode(', ', $ldrIds) . ')'],
        'id, firstName, lastName, avatar',
        'id'
    );
}

$ldrArray = [];
$precedentAerr = [];
foreach ($leaders->data as $ldr) {
    if (isset($leaderInfo[$ldr->mbrId])) {
        if ($ldr->type == 'leader') {
            $ldrArray[] = $leaderInfo[$ldr->mbrId];
        } elseif ($ldr->type == 'president') {
            $precedentAerr[] = $leaderInfo[$ldr->mbrId];
        }
    }
}

?>
<h1 class="mb0">
    Section Details
</h1>
<?php
breadcrumbs(
    [
        'Sections',
        '?page=chapter'
    ],
    [
        $chptrData->name
    ]
);
?>
<div class="sec-details">
    <div class="sec-box">
        <div class="sec-info">
            <div class="sec-name">
                <?php echo $chptrData->name; ?>
            </div>

            <div class="sec-region items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        horizontal_split
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo $chptrData->secId ?? 'Unknown'; ?>
                </div>
            </div>

            <div class="sec-region items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        confirmation_number
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo $chptrData->ein ?? 'Unknown'; ?>
                </div>
            </div>

            <div class="sec-region items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        share_location
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo $stateData->name ?? 'Unknown'; ?>
                </div>
            </div>

            <div class="sec-region items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        explore
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo $regnDta->name ?? 'Unknown'; ?>
                </div>
            </div>

            <div class="sec-cntry items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        flag
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo $ntData->name ?? 'Unknown'; ?>
                </div>
            </div>
            <div class="mb15"></div>

            <?php
            if ($founderInfo) {
            ?>
                <div class="info-details">
                    <div class="dtl-top">
                        <?php
                        if (
                            $chptrData->firstName ||
                            $chptrData->lastName ||
                            $chptrData->email ||
                            $chptrData->phone
                        ) {
                        ?>
                            <div class="top-itm lf">
                                <div class="top-items">
                                    <span class="material-symbols-outlined itm-lb">
                                        <?php echo ($chptrData->firstName || $chptrData->lastName) ? 'account_circle' : ''; ?>
                                    </span>
                                    <div class="itm-vl">
                                        <?php echo ($chptrData->firstName ?? '') . ' ' . ($chptrData->lastName ?? ''); ?>
                                    </div>
                                </div>
                                <div class="top-items">
                                    <span class="material-symbols-outlined itm-lb">
                                        <?php echo $chptrData->email ? 'mail' : ''; ?>
                                    </span>
                                    <div class="itm-vl">
                                        <?php echo $chptrData->email ?? ''; ?>
                                    </div>
                                </div>
                                <div class="top-items">
                                    <span class="material-symbols-outlined itm-lb">
                                        <?php echo $chptrData->phone ? 'smartphone' : ''; ?>
                                    </span>
                                    <div class="itm-vl">
                                        <?php echo $chptrData->phone ?? ''; ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        if (
                            $chptrData->appliedDate ||
                            $chptrData->approvedDate ||
                            $chptrData->dateChartered
                        ) {
                        ?>
                            <div class="top-itm rg">
                                <div class="top-items dt">
                                    <div class="itm-lb">
                                        <?php echo $chptrData->appliedDate ? 'Applied On' : ''; ?>
                                    </div>
                                    <div class="itm-vl">
                                        <?php echo $chptrData->appliedDate ? date('d F, Y', strtotime($chptrData->appliedDate)) : ''; ?>
                                    </div>
                                </div>
                                <div class="top-items dt">
                                    <div class="itm-lb">
                                        <?php echo $chptrData->approvedDate ? 'Approved On' : ''; ?>
                                    </div>
                                    <div class="itm-vl">
                                        <?php echo $chptrData->approvedDate ? date('d F, Y', strtotime($chptrData->approvedDate)) : ''; ?>
                                    </div>
                                </div>
                                <div class="top-items dt">
                                    <div class="itm-lb">
                                        <?php echo $chptrData->dateChartered ? 'Date Chartered' : ''; ?>
                                    </div>
                                    <div class="itm-vl">
                                        <?php echo $chptrData->dateChartered ? date('d F, Y', strtotime($chptrData->dateChartered)) : ''; ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <?php
                    if ($chptrData->note) {
                    ?>
                        <div class="dtl-btm">
                            <?php echo $chptrData->note ?? ''; ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>

            <div class="mb15"></div>

            <div class="sec-sts mb15">
                <?php
                if ($chptrData->enabled == 'Y') {
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

            <div class="sec-date items">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        fact_check
                    </span>
                </div>
                <div class="itm-data">
                    <?php echo date('F d, Y', strtotime($chptrData->createdAt)) ?? '--'; ?>
                </div>
            </div>

            <div class="sec-ldr">
                <div class="heading">
                    Section Leaders
                </div>
                <div class="ldr-sec">
                    <?php
                    if (!empty($ldrArray)) {
                        foreach ($ldrArray as $ldr) {
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
                                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=section-leader-delete&ldrid=$ldr->id&secId=$cid&typ=L"; ?>" class="confirm">
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
                if (
                    count($ldrArray) < 3 &&
                    $pix->canAccess('elect')
                ) {

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




            <div class="sec-ldr">
                <div class="heading">
                    Section President
                </div>
                <div class="ldr-sec">
                    <?php
                    if (!empty($precedentAerr)) {
                        foreach ($precedentAerr as $ldr) {
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
                                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=section-leader-delete&ldrid=$ldr->id&secId=$cid&typ=P"; ?>" class="confirm">
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
                            'No president added yet.'
                        );
                    }
                    ?>
                </div>

                <?php
                if (
                    count($precedentAerr) < 1 &&
                    $pix->canAccess('elect')
                ) {
                ?>
                    <div class="btn">
                        <span class="pix-btn sm add-pre" id="addPrecedent">
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
                    <a href="<?php echo $pix->adminURL . "?page=members&sh_sc_sort=$chptrData->id"; ?>" class="btn mbr-view">
                        <span class="material-symbols-outlined icn">
                            visibility
                        </span>
                        <span class="txt">
                            View All
                        </span>
                    </a>
                </div>
            </div>

            <div class="sec-actions">
                <a href="<?php echo $pix->adminURL . "?page=chapter&sec=mod&id=$chptrData->id"; ?>" class="btn edt">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                    <span>
                        Edit
                    </span>
                </a>
                <a href="<?php echo ADMINURL . "actions/anyadmin/?method=chapter-delete&id=$chptrData->id"; ?>" class="btn dlt confirm">
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