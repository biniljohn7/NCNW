<?php
$bid = esc($_GET['id'] ?? 'new');
$new = $bid == 'new';

//Collecting benefit category data
if (!$new) {
    $validBenefit = false;
    if ($bid) {
        $bfData = $pixdb->get(
            'categories',
            [
                'id' => $bid,
                'single' => 1
            ]
        );
        $validBenefit = !!$bfData;
    }
    if (!$validBenefit) {
        $pix->addmsg('Unknown benefit category');
        $pix->redirect('?page=ctry-benefit');
    }
}
loadStyle('pages/category/mod');
loadScript('pages/category/mod');

?>
<h1>
    <?php
    echo $new ? 'Create' : 'Modify';
    ?> Benefit Category
</h1>
<?php
breadcrumbs(
    [
        'Benefit Categories',
        '?page=ctry-benefit'
    ],
    !$new ? [
        $bfData->ctryName
    ] : null,
    [
        $new ? 'create' : 'Modify'
    ]
);
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" class="category" enctype="multipart/form-data">
    <input type="hidden" name="method" value="category-save" />
    <input type="hidden" name="type" value="Benefit" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="bid" value="<?php echo $bid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $bfData->ctryName; ?>" data-type="string" />
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Icon
        </div>
        <div class="fld-inp">
            <div class="icon-prev">
                <?php
                $icon = $new ? '' : $bfData->itryIcon;
                if ($icon) {
                    $bfData->itryIcon = $pix->uploadPath . 'category-image/' . $pix->thumb(
                        $bfData->itryIcon,
                        '150x150'
                    );
                ?>
                    <img src="<?php echo $bfData->itryIcon; ?>">
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
                $new || (!$new && $bfData->enable == 'Y'),
                isset($bfData->enabled)
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