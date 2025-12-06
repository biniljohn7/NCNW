<?php
(function ($pix, $pixdb) {
    $cData = false;
    if (isset($_GET['id'])) {
        $cid = esc($_GET['id']);
        if ($cid) {
            $cData = $pixdb->getRow(
                'cms',
                [
                    'id' => $cid
                ]
            );
        }
    }
    if (!$cData) {
        $pix->addmsg('Unknown cms');
        $pix->redirect('?page=cms');
    }

    loadScript('pages/cms/details');
    loadStyle('pages/cms/details');
?>
    <h1>
        CMS Page Details
    </h1>
    <?php
    breadcrumbs(
        [
            'CMS Pages',
            '?page=cms'
        ],
        [
            $cData->cmsName
        ]
    );
    ?>
    <div class="cms-wrap">
        <div class="cms-hed">
            <?php
            echo $cData->cmsName;
            ?>
        </div>
        <div class="cms-dtls">
            <div class="cms-date">
                <span class="material-symbols-outlined">
                    calendar_month
                </span>
                <?php
                echo date('F d, Y', strtotime($cData->createdAt));
                ?>
            </div>
            <div class="cms-sts">
                <?php
                if ($cData->enabled) {
                    $sts = $cData->enabled == 'Y' ? 'published' : 'unpublished';
                ?>
                    <span class="sts <?php echo $sts; ?>">
                        <?php
                        echo ucwords($sts);
                        ?>
                    </span>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="cms-content">
            <div class="cont-label">
                CMS Content
            </div>
            <div class="cont-vals">
                <?php
                echo htmlspecialchars_decode($cData->cmsContent, ENT_QUOTES);
                ?>
            </div>
        </div>
        <div class="cms-btn">
            <a href="<?php echo ADMINURL . "?page=cms&sec=mod&id=$cData->id"; ?>" class="edt mr5">
                <span class="material-symbols-outlined icn">
                    edit
                </span>
                <span>
                    Edit
                </span>
            </a>
            <a href="<?php echo $pix->adminURL . "actions/anyadmin?method=cms-delete&id=$cData->id"; ?>" class="dlt confirm">
                <span class="material-symbols-outlined icn">
                    delete
                </span>
                <span>
                    Delete
                </span>
            </a>
        </div>
    </div>
<?php
})($pix, $pixdb);
?>