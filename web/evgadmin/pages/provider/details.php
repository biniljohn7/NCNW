<?php
$pvData = false;
if (isset($_GET['id'])) {
    $pid = esc($_GET['id']);
    if ($pid) {
        $pvData = $pixdb->get(
            'benefit_providers',
            [
                'id' => $pid,
                'single' => 1
            ]
        );
    }
}
if (!$pvData) {
    $pix->addmsg('Unknown benefit provider');
    $pix->redirect('?page=provider');
}
loadStyle('pages/provider/details');
loadScript('pages/provider');


?>
<h1 class="mb0">Provider Details</h1>
<?php
breadcrumbs(
    [
        'Providers',
        '?page=provider'
    ],
    [
        $pvData->name
    ]
)
?>
<div class="pvd-details">
    <div class="pvd-info">
        <div class="img-sec" id="imgBox">
            <div class="img-prev" id="imgPrev">
                <img id="imgPreview" src="<?php echo $pix->uploadPath, 'provider-logo/', $pix->thumb($pvData->logo, '450x450'); ?>" class="pvd-img" style="<?php echo $pvData->logo ? '' : 'display:none'; ?>">
                <div class="no-img" id="noImg" style="<?php echo $pvData->logo ? 'display:none' : ''; ?>">
                    <span class="material-symbols-outlined img-icn">
                        wallpaper
                    </span>
                </div>
                <span class="add-pvd-img">
                    <span class="material-symbols-outlined cam-icn">
                        photo_camera
                    </span>
                    <input type="file" name="providerImg" id="pvdimg" class="pvd-imp-inp" />
                </span>
            </div>
            <div class="upload-img">
                <div class="pg-bar" id="pgBar"></div>
            </div>
        </div>
        <div class="info-sec">
            <div class="pvd-name">
                <?php
                echo $pvData->name  ?? '--';
                ?>
            </div>
            <div class="pvd-itm mail">
                <?php
                echo $pvData->email ?? '--'
                ?>
            </div>
            <div class="pvd-itm phn">
                <?php
                echo $pvData->phone ?? '--'
                ?>
            </div>
            <div class="pvd-addr-lbl">
                Address
            </div>
            <div class="pvd-addr">
                <?php
                echo $pvData->address ?? '--';
                ?>
            </div>
        </div>
        <div class="dlt-btn">
            <a href="<?php echo ADMINURL . "?page=provider&sec=mod&id=$pvData->id"; ?>" class="bf-edt mr5">
                <span class="material-symbols-outlined icn">
                    edit
                </span>
            </a>
            <a href="<?php echo ADMINURL . "actions/anyadmin/?method=provider-delete&id=$pvData->id"; ?>" class="confirm">
                <span class="material-symbols-outlined icn">
                    delete
                </span>
            </a>
        </div>
    </div>
</div>
<?php
$pData = [
    'pid' => $pvData->id
];
echo '<script> const pData=', json_encode($pData), ';</script>';
?>