<?php
$eid = esc($_GET['id'] ?? 'new');
$new = $eid == 'new';

//Collecting event category data
if (!$new) {
    $validEvent = false;
    if ($eid) {
        $eData = $pixdb->get(
            'categories',
            [
                'id' => $eid,
                'single' => 1
            ]
        );
        $validEvent = !!$eData;
    }
    if (!$validEvent) {
        $pix->addmsg('Unknown event category');
        $pix->redirect('?page=ctry-event');
    }
}
loadStyle('pages/category/mod');
loadScript('pages/category/mod');
?>

<h1>
    <?php
    echo $new ? 'Create' : 'Modify';
    ?> Event Category
</h1>
<?php
breadcrumbs(
    [
        'Event Categories',
        '?page=ctry-event'
    ],
    !$new ? [
        $eData->ctryName
    ] : null,
    [
        $new ? 'create' : 'Modify'
    ]
);
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" class="category" enctype="multipart/form-data">
    <input type="hidden" name="method" value="category-save" />
    <input type="hidden" name="type" value="Event" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="eid" value="<?php echo $eid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $eData->ctryName; ?>" data-type="string" />
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Icon
        </div>
        <div class="fld-inp">
            <div class="icon-prev">
                <?php
                $icon = $new ? '' : $eData->itryIcon;
                if ($icon) {
                    $itryIcon = $pix->uploadPath . 'category-image/' . $pix->thumb(
                        $eData->itryIcon,
                        '150x150'
                    );
                ?>
                    <img src="<?php echo $itryIcon; ?>">
                <?php
                } else {
                ?>
                    <div class="no-img">
                        <span class="material-symbols-outlined img-icn">
                            wallpaper
                        </span>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="file-up">
                <input type="file" name="ctryIcon" class="ctry-img" data-type="files" data-extensions="jpeg,jpg,png,gif" data-label="icon" data-optional="1" />
            </div>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable category',
                'status',
                1,
                $new || (!$new && $eData->enable == 'Y'),
                isset($eData->enabled)
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