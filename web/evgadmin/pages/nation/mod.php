<?php
$nid = esc($_GET['id'] ?? 'new');
$new = $nid == 'new';

//Collecting country data
if (!$new) {
    $validNation = false;
    if ($nid) {
        $ntData = $pixdb->get(
            'nations',
            [
                'id' => $nid,
                'single' => 1
            ]
        );
        $validNation = !!$ntData;
    }
    if (!$validNation) {
        $pix->addmsg('Unknown country');
        $pix->redirect('?page=nation');
    }
}
loadScript('pages/nation');
loadStyle('pages/benefits/mod');

?>
<h1 class="mb0">
    <?php echo $new ? 'Create' : 'Modify'; ?> Country
</h1>
<?php
breadcrumbs(
    [
        'Nations',
        '?page=nation'
    ],
    !$new ? [
        $ntData->name
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
);
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="nationSave">
    <input type="hidden" name="method" value="nation-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="nid" value="<?php echo $nid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Country Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $ntData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable Country',
                'status',
                1,
                $new || (!$new && $ntData->enabled == 'Y'),
                isset($ntData->enabled)
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