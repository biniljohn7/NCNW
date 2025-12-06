<?php
$id = esc($_GET['id'] ?? 'new');
$new = $id == 'new';

if (!$new) {
    $dataOk = false;
    if ($id) {
        $tpData = $pixdb->getRow('membership_types', ['id' => $id]);
        $dataOk = !!$tpData;
    }
    if (!$dataOk) {
        $pix->addmsg('Unknown type');
        $pix->redirect('?page=member-packages');
    }
}

loadStyle('pages/member-packages/mod-type');
loadScript('pages/member-packages/mod-type');
?>
<h1>
    <?php
    echo $new ? 'Add a' : 'Modify'
    ?>
    Plan Type
</h1>
<?php
breadcrumbs(
    [
        'Membership Plans',
        '?page=member-packages'
    ],
    !$new ? [
        $tpData->name,
        "?page=member-packages&sec=details&id=$tpData->id"
    ] : null,
    [
        $new ?
            'Create a Plan Type' :
            'Modify Plan Type'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="typeForm">
    <input type="hidden" name="method" value="membership-package-type-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Name
        </div>
        <div class="fld-inp">
            <input type="text" size="70" name="name" value="<?php echo $new ? '' : $tpData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Visibility
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Publish type',
                'visibility',
                1,
                $new ||
                    (!$new && $tpData->active == 'Y')
            );
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
        </div>
        <div class="fld-inp">
            <input type="submit" class="pix-btn lg site bold-500" value="Save Details">
        </div>
    </div>
</form>