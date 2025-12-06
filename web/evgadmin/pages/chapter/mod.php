<?php
$cid = esc($_GET['id'] ?? 'new');
$new = $cid == 'new';

//Collecting nation data
if (!$new) {
    $validChapter = false;
    if ($cid) {
        $cpData = $pixdb->get(
            'chapters',
            [
                'id' => $cid,
                'single' => 1
            ]
        );
        $validChapter = !!$cpData;
    }
    if (!$validChapter) {
        $pix->addmsg('Unknown Section');
        $pix->redirect('?page=chapter');
    }
}
$ntData = $pixdb->get(
    'nations',
    [
        '#SRT' => 'id asc'
    ],
    'id, name'
)->data;

loadScript('pages/chapter/mod');
loadStyle('pages/benefits/mod');
?>
<h1>
    <?php
    echo $new ? 'Create' : 'Modify';
    ?> Section
</h1>
<?php
breadcrumbs(
    [
        'Sections',
        '?page=chapter'
    ],
    !$new ? [
        $cpData->name,
        '?page=chapter&sec=details&id=' . $cid
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
);
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="chapterSave">
    <input type="hidden" name="method" value="chapter-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="cid" value="<?php echo $cid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Section Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $cpData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            EIN
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="ein" value="<?php echo $new ? '' : $cpData->ein; ?>">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Section ID
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="secId" value="<?php echo $new ? '' : $cpData->secId; ?>">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Country
        </div>
        <div class="fld-inp">
            <select name="nation" data-type="string" id="nationSel">
                <option value="">
                    Choose Country
                </option>
                <?php
                if (!$new) {
                    $getSt = $evg->getState($cpData->state, 'region');
                    $getReg = $evg->getRegion($getSt->region, 'id,nation');
                    $getNat = $evg->getNation($getReg->nation, 'id,name');
                }
                $selNation = $new ? '' : $getNat->id;
                foreach ($ntData as $nt) {
                    echo '<option ', $selNation == $nt->id ? 'selected' : '', ' value="', $nt->id, '">', $nt->name, '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Region
        </div>
        <div class="fld-inp">
            <select name="region" data-type="string" id="regionSel">
                <option value="">
                    Choose Region
                </option>
                <?php
                if (!$new) {
                    $rgData = $pixdb->get(
                        'regions',
                        [
                            '#SRT' => 'id asc',
                            'nation' => $selNation
                        ],
                        'id, name'
                    )->data;
                    $selRegion = $new ? '' : $getReg->id;
                    foreach ($rgData as $rg) {
                        echo '<option ', $selRegion == $rg->id ? 'selected' : '', ' value="', $rg->id, '">', $rg->name, '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            State
        </div>
        <div class="fld-inp">
            <select name="state" data-type="string" id="stateSel">
                <option value="">
                    Choose State
                </option>
                <?php
                if (!$new) {
                    $stData = $pixdb->get(
                        'states',
                        [
                            '#SRT' => 'id asc',
                            'region' => $selRegion
                        ],
                        'id, name'
                    )->data;
                    $selState = $new ? '' : $cpData->state;
                    foreach ($stData as $st) {
                        echo '<option ', $selState == $st->id ? 'selected' : '', ' value="', $st->id, '">', $st->name, '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
           Founder Info
        </div>
        <div class="fld-inp">
            <div class="fndr-inp">
                <input type="text" size="35" name="firstName" value="<?php echo $new ? '' : $cpData->firstName; ?>" placeholder="First Name">
            </div>
            <div class="fndr-inp">
                <input type="text" size="35" name="lastName" value="<?php echo $new ? '' : $cpData->lastName; ?>" placeholder="Last Name">
            </div>
            <div class="fndr-inp">
                <input type="text" size="35" name="email" value="<?php echo $new ? '' : $cpData->email; ?>" placeholder="Email ID">
            </div>
            <div class="fndr-inp">
                <input type="text" size="35" name="phone" value="<?php echo $new ? '' : $cpData->phone; ?>" placeholder="Phone Number">
            </div>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
           Date Applied for Chartered
        </div>
        <div class="fld-inp">
            <input type="text" size="25" name="appliedDate" id="appliedDate" class="sectionDates" value="<?php echo $new ? '' : ($cpData->appliedDate ? date('d-m-Y', strtotime($cpData->appliedDate)) : ''); ?>" autocomplete="off">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
           Date Approved for Chartered
        </div>
        <div class="fld-inp">
            <input type="text" size="25" name="approvedDate" id="approvedDate" class="sectionDates" value="<?php echo $new ? '' : ($cpData->approvedDate ? date('d-m-Y', strtotime($cpData->approvedDate)) : ''); ?>" autocomplete="off">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
           Date Chartered
        </div>
        <div class="fld-inp">
            <input type="text" size="25" name="dateChartered" id="dateChartered" class="sectionDates" value="<?php echo $new ? '' : ($cpData->dateChartered ? date('d-m-Y', strtotime($cpData->dateChartered)) : ''); ?>" autocomplete="off">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Note
        </div>
        <div class="fld-inp">
            <textarea cols="70" rows="4" name="note"><?php echo $cpData->note ?? ''; ?></textarea>
            <br />
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable Section',
                'status',
                1,
                $new || (!$new && $cpData->enabled == 'Y'),
                isset($cpData->enabled)
            );
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
        </div>
        <div class="fld-inp">
            <input type="submit" class="pix-btn lg site bold-500" value="Submit">
        </div>
    </div>
</form>