<?php
$evData = false;
if (isset($_GET['id'])) {
    $id = esc($_GET['id']);
    if ($id) {
        $evData = $pixdb->getRow('events', ['id' => $id]);
    }
}
if (!$evData) {
    $pix->addmsg('Unknown event');
    $pix->redirect('?page=events');
}

loadStyle('pages/events/details');
loadScript('pages/events/details');
?>
<h1 class="mb0">Event Details</h1>
<?php
breadcrumbs(
    [
        'Events',
        '?page=events'
    ],
    [$evData->name]
)
?>
<div class="event-details">
    <div class="evt-box">
        <div class="ev-info">
            <div class="ev-name">
                <?php echo $evData->name; ?>
            </div>
            <div class="ev-cat">
                <span class="material-symbols-outlined">
                    bookmark
                </span>
                <?php
                $catName = null;
                if ($evData->category) {
                    $catData = $pixdb->getRow(
                        'categories',
                        ['id' => $evData->category],
                        'ctryName'
                    );
                    if ($catData) {
                        $catName = $catData->ctryName;
                    }
                }
                if ($catName) {
                    echo $catName;
                    // 
                } else {
                ?>
                    <span class="no-cat">
                        ( No category selected )
                    </span>
                <?php
                }
                ?>
            </div>
            <div class="ev-date">
                <?php
                echo date('d F Y', strtotime($evData->date));

                if ($evData->enddate && $evData->enddate != $evData->date) {
                    echo ' - ', date('j F Y', strtotime($evData->enddate));
                }
                ?>
            </div>
            <div class="ev-desc">
                <?php echo nl2br($evData->descrptn); ?>
            </div>
            <div class="ev-address">
                <span class="material-symbols-outlined loc">
                    location_on
                </span>
                <span class="addr">
                    <?php
                    echo nl2br($evData->address);
                    ?>
                </span>
            </div>
            <div class="ev-actions">
                <a href="<?php echo $pix->adminURL, "?page=events&sec=mod&id=$evData->id"; ?>" class="btn edt">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                    <span>
                        Edit
                    </span>
                </a>
                <a href="<?php echo $pix->adminURL . "actions/anyadmin/?method=event-delete&id=$evData->id"; ?>" class="btn dlt confirm">
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
                <?php
                if ($evData->image) {
                ?>
                    <img src="<?php echo $pix->uploadPath, 'events/', $pix->thumb($evData->image, 'w750'); ?>" class="cr-img">
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
                <form class="add-cr-img" method="post" enctype="multipart/form-data" action="<?php echo $pix->adminURL, 'actions/anyadmin/'; ?>" id="eventImgForm">
                    <input type="hidden" name="method" value="event-photo-upload" />
                    <input type="hidden" name="id" value="<?php echo $id; ?>" />
                    <span class="material-symbols-outlined cam-icn">
                        photo_camera
                    </span>
                    <input type="file" name="photo" id="eventImg" class="cr-img-inp" />
                </form>
            </div>
        </div>
    </div>
</div>