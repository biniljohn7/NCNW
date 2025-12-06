<?php
$afid = esc($_GET['id'] ?? 'new');
$new = $afid == 'new';

//Collecting affiliate data
if (!$new) {
    $validAffiliate = false;
    if ($afid) {
        $afData = $pixdb->get(
            'affiliates',
            [
                'id' => $afid,
                'single' => 1
            ]
        );
        $validAffiliate = !!$afData;
    }
    if (!$validAffiliate) {
        $pix->addmsg('Unknown affiliate');
        $pix->redirect('?page=affiliates');
    }
}
loadScript('pages/affiliate');
loadStyle('pages/benefits/mod');

?>
<h1 class="mb0">
    <?php echo $new ? 'Create' : 'Modify'; ?> Affiliate
</h1>
<?php
breadcrumbs(
    [
        'Affiliates',
        '?page=affiliates'
    ],
    !$new ? [
        $afData->name,
        '?page=affiliates&sec=details&id=' . $afData->id
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
);
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="affiliateSave">
    <input type="hidden" name="method" value="affiliate-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="afid" value="<?php echo $afid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Affiliate Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $afData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable Affiliate',
                'status',
                1,
                $new || (!$new && $afData->enabled == 'Y'),
                isset($afData->enabled)
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