<?php
$sid = esc($_GET['id'] ?? 'new');
$new = $sid == 'new';

//Collecting country data
if (!$new) {
    $validState = false;
    if ($sid) {
        $stData = $pixdb->get(
            'states',
            [
                'id' => $sid,
                'single' => 1
            ]
        );
        $validState = !!$stData;
    }
    if (!$validState) {
        $pix->addmsg('Unknown state');
        $pix->redirect('?page=state');
    }
}
$ntData = $pixdb->get(
    'nations',
    [
        '#SRT' => 'id asc'
    ],
    'id, name'
)->data;


loadScript('pages/state/mod');
loadStyle('pages/benefits/mod');

?>
<h1 class="mb0">
    <?php echo $new ? 'Create' : 'Modify'; ?> State
</h1>
<?php
breadcrumbs(
    [
        'States',
        '?page=state'
    ],
    !$new ? [
        $stData->name,
        '?page=state&sec=details&id=' . $sid
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
);
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="stateSave">
    <input type="hidden" name="method" value="state-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            State Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $stData->name; ?>" data-type="string">
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
                    $getReg = $evg->getRegion($stData->region, 'nation');
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
                    $selRegion = $new ? '' : $stData->region;
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
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable state',
                'status',
                1,
                $new || (!$new && $stData->enabled == 'Y'),
                isset($stData->enabled)
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