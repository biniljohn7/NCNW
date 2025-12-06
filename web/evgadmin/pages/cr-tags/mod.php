<?php
$tid = esc($_GET['id'] ?? 'new');
$new = $tid == 'new';

//Collecting nation data
if (!$new) {
    $validTag = false;
    if ($tid) {
        $tgData = $pixdb->get(
            'career_types',
            [
                'id' => $tid,
                'single' => 1
            ]
        );
        $validTag = !!$tgData;
    }
    if (!$validTag) {
        $pix->addmsg('Unknown career tag');
        $pix->redirect('?page=cr-tags');
    }
}
loadStyle('pages/benefits/mod');
loadScript('pages/cr-tags/career-tag');

?>
<h1 class="mb0">
    <?php
    echo $new ? 'Create' : 'Modify'
    ?>
</h1>
<?php
breadcrumbs(
    [
        'Career Tags',
        '?page=cr-tags'
    ],
    !$new ? [
        $tgData->name,
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="tagSave">
    <input type="hidden" name="method" value="career-tag-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="tid" value="<?php echo $tid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $tgData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable Tag',
                'status',
                1,
                $new || (!$new && $tgData->enabled == 'Y'),
                isset($tgData->enabled)
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