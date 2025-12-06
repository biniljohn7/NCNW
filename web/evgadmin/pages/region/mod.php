<?php
$rid = esc($_GET['id'] ?? 'new');
$new = $rid == 'new';

//Collecting region data
if (!$new) {
    $validRegion = false;
    if ($rid) {
        $rgData = $pixdb->get(
            'regions',
            [
                'id' => $rid,
                'single' => 1
            ]
        );
        $validRegion = !!$rgData;
    }
    if (!$validRegion) {
        $pix->addmsg('Unknown region');
        $pix->redirect('?page=region');
    }
}
$ntData = $pixdb->get(
    'nations',
    [
        '#SRT' => 'id asc'
    ],
    'id, name'
)->data;
loadScript('pages/region');
loadStyle('pages/benefits/mod');
?>
<h1><?php echo $new ? 'Create' : 'Modify'; ?> Region</h1>
<?php
breadcrumbs(
    [
        'Regions',
        '?page=region'
    ],
    !$new ? [
        $rgData->name
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
);
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="regionSave">
    <input type="hidden" name="method" value="region-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Region Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $rgData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Country
        </div>
        <div class="fld-inp">
            <select name="nation" data-type="string">
                <option value="">
                    Choose Country
                </option>
                <?php
                $selNation = $new ? '' : $rgData->nation;
                foreach ($ntData as $nt) {
                    echo '<option ', $selNation == $nt->id ? 'selected' : '', ' value="', $nt->id, '">', $nt->name, '</option>';
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
                'Enable Region',
                'status',
                1,
                $new || (!$new && $rgData->enabled == 'Y'),
                isset($rgData->enabled)
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