<?php
$csId = esc($_GET['id'] ?? 'new');
$new = $csId == 'new';

//Collecting collegiate sections data
if (!$new) {
    $validCollegiate = false;
    if ($csId) {
        $csData = $pixdb->get(
            'collegiate_sections',
            [
                'id' => $csId,
                'single' => 1
            ]
        );
        $validCollegiate = !!$csData;
    }
    if (!$validCollegiate) {
        $pix->addmsg('Unknown collegiate section');
        $pix->redirect('?page=collegiate-sections');
    }
}
loadScript('pages/collegiate/mod');
loadStyle('pages/collegiate/mod');

?>
<h1 class="mb0">
    <?php echo $new ? 'Create' : 'Modify'; ?> Collegiate Section
</h1>
<?php
breadcrumbs(
    [
        'Collegiate Section',
        '?page=collegiate-sections'
    ],
    !$new ? [
        $csData->name,
        '?page=collegiate-sections&sec=details&id=' . $csData->id
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
);
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="collegiateSave">
    <input type="hidden" name="method" value="collegiate-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="csId" value="<?php echo $csId; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Collegiate Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $csData->name; ?>" data-type="string" data-label="collegiate name">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable',
                'status',
                1,
                $new || (!$new && $csData->enabled == 'Y'),
                isset($csData->enabled)
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