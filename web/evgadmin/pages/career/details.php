<?php
$crData = false;
if (isset($_GET['id'])) {
    $cid = esc($_GET['id']);
    if ($cid) {
        $crData = $pixdb->get(
            'careers',
            [
                'id' => $cid,
                'single' => 1
            ]
        );
    }
}
if (!$crData) {
    $pix->addmsg('Unknown career');
    $pix->redirect('?page=career');
}
loadStyle('pages/career/details');
loadScript('pages/career/details');

?>
<h1 class="mb0">Career Details</h1>
<?php
breadcrumbs(
    [
        'Careers',
        '?page=career'
    ],
    [
        $crData->title
    ]
)
?>
<div class="career-wrap">
    <div class="wrap-top">
        <div class="wrap-content">
            <div class="cnt-hed">
                <?php
                echo $crData->title;
                ?>
            </div>
            <div class="cnt-type">
                <?php
                if ($crData->type) {
                    $crType = $evg->getCareerType($crData->type, 'name');
                    if ($crType) {
                        echo $crType->name;
                    }
                }
                ?>
            </div>
            <div class="cnt-desc">
                <?php
                echo nl2br($crData->description);
                ?>
            </div>
            <div class="btm-left">
                <span class="material-symbols-outlined loc">
                    location_on
                </span>
                <span class="addr">
                    <?php
                    echo $crData->address;
                    ?>
                </span>
            </div>
            <div class="btm-right">
                <a href="<?php echo $pix->adminURL . "?page=career&sec=mod&id=$crData->id"; ?>" class="btn edt">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                    <span>
                        Edit
                    </span>
                </a>
                <a href="<?php echo $pix->adminURL . "actions/anyadmin/?method=career-delete&id=$crData->id"; ?>" class="btn dlt confirm">
                    <span class="material-symbols-outlined icn">
                        delete
                    </span>
                    <span>
                        Delete
                    </span>
                </a>
            </div>
        </div>
        <div class="wrap-img" id="imgBox">
            <div class="img-prev" id="imgPrev">
                <img src="<?php echo $pix->uploadPath, 'career-image/', $pix->thumb($crData->image, '450x450'); ?>" class="cr-img" id="imgPreview" style="<?php echo $crData->image ? '' : 'display:none'; ?>">
                <div class="no-img" id="noImg" style="<?php echo $crData->image ? 'display:none' : ''; ?>">
                    <span class="material-symbols-outlined img-icn">
                        wallpaper
                    </span>
                </div>
                <span class="add-cr-img">
                    <span class="material-symbols-outlined cam-icn">
                        photo_camera
                    </span>
                    <input type="file" name="careerImg" id="crimg" class="cr-img-inp" />
                </span>
            </div>
            <div class="upload-img">
                <div class="pg-bar" id="pgBar"></div>
            </div>
        </div>
    </div>
</div>
<?php
$cData = [
    'cid' => $crData->id
];
echo '<script> const cData=', json_encode($cData), ';</script>';
?>