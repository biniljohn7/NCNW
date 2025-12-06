<?php
$cid = esc($_GET['id'] ?? 'new');
$new = $cid == 'new';

// Collecting provider data
if (!$new) {
    $validCareer = false;
    if ($cid) {
        $crData = $pixdb->get(
            'careers',
            [
                'id' => $cid,
                'single' => 1
            ]
        );
        $validCareer = !!$crData;
    }
    if (!$validCareer) {
        $pix->addmsg('Unknown career');
        $pix->redirect('?page=career');
    }
}
$crType = $pixdb->get(
    'career_types',
    [
        '#SRT' => 'id asc'
    ],
    'id, name'
)->data;

loadStyle('pages/career/mod');
loadScript('pages/career/mod');

?>
<h1>
    <?php
    echo $new ? 'Create' : 'Modify'
    ?> Career
</h1>
<?php
breadcrumbs(
    [
        'Careers',
        '?page=career'
    ],
    !$new ? [
        $crData->title,
        "?page=career&sec=details&id=$crData->id"
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="careerSave">
    <input type="hidden" name="method" value="career-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="cid" value="<?php echo $cid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Title
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="title" value="<?php echo $new ? '' : $crData->title; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Career Type
        </div>
        <div class="fld-inp">
            <select name="type" data-type="string">
                <option value="">
                    Choose Type
                </option>
                <?php
                $selCat = $new ? '' : $crData->type;
                foreach ($crType as $tp) {
                    echo '<option ', $selCat == $tp->id ? 'selected' : '', ' value="', $tp->id, '">', $tp->name, '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Address
        </div>
        <div class="fld-inp">
            <textarea cols="70" rows="3" name="address" data-type="string"><?php echo $new ? '' : $crData->address; ?></textarea>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Description
        </div>
        <div class="fld-inp">
            <textarea cols="70" rows="8" name="desc" data-type="string" data-label="description"><?php echo $new ? '' : $crData->description; ?></textarea>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable Career',
                'status',
                1,
                $new || (!$new && $crData->enabled == 'Y'),
                isset($crData->enabled)
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