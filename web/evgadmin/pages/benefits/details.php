<?php
$bfData = false;
if (isset($_GET['id'])) {
    $bid = esc($_GET['id']);
    if ($bid) {
        $bfData = $pixdb->get(
            'benefits',
            [
                'id' => $bid,
                'single' => 1
            ]
        );
    }
}
if (!$bfData) {
    $pix->addmsg('Unknown benefit');
    $pix->redirect('?page=benefits');
}
loadStyle('pages/benefits/details');
loadScript('pages/benefits/mod');

?>
<h1 class="mb0">Benefit Details</h1>
<?php
breadcrumbs(
    [
        'Benefits',
        '?page=benefits'
    ],
    [
        $bfData->name
    ]
)
?>
<div class="bnft-detail">
    <div class="bnft-hed">
        <div class="hed-left">
            <div class="bf-name">
                <?php
                echo $bfData->name ?? '--';
                ?>
            </div>
            <?php
            if ($bfData->status == 'inactive') {
            ?>
                <div class="bf-sts">
                    <span class="sts">
                        Inactive
                    </span>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="hed-right">
            <?php
            echo $bfData->discount . ' %' ?? '--';
            ?>
        </div>
    </div>
    <div class="shrt-desc">
        <div class="desc-label">
            Short Description
        </div>
        <div class="decs-val">
            <?php
            echo $bfData->shortDescr ?? '--';
            ?>
        </div>
    </div>
    <div class="box-items">
        <div class="item left">
            <div class="itm lf">
                <div class="itm-icn">
                    <span class="icn material-symbols-outlined">
                        category
                    </span>
                </div>
                <div class="itm-info">
                    <div class="itm-label">
                        Category
                    </div>
                    <?php
                    if ($bfData->ctryId) {
                        $catData = $evg->getCategory($bfData->ctryId, 'ctryName');
                        if ($catData) {
                    ?>
                            <div class="itm-val">
                                <?php echo $catData->ctryName; ?>
                            </div>
                    <?php
                        }
                    } else {
                        echo '--';
                    }
                    ?>
                </div>
            </div>
            <div class="itm rt brd">
                <div class="itm-icn">
                    <span class="icn material-symbols-outlined">
                        content_copy
                    </span>
                </div>
                <div class="itm-info">
                    <div class="itm-label">
                        Provider
                    </div>
                    <?php
                    if ($bfData->provider) {
                        $pvData = $evg->getProvider($bfData->provider, 'name');
                        if ($pvData) {
                    ?>
                            <div class="itm-val">
                                <?php echo $pvData->name; ?>
                            </div>
                    <?php
                        }
                    } else {
                        echo '--';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="item right">
            <div class="itm lf">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        app_registration
                    </span>
                </div>
                <div class="itm-info">
                    <div class="itm-label">
                        Code
                    </div>
                    <div class="itm-val">
                        <?php
                        echo $bfData->code ?? '--';
                        ?>
                    </div>
                </div>
            </div>
            <div class="itm rt">
                <div class="itm-icn">
                    <span class="material-symbols-outlined">
                        grid_view
                    </span>
                </div>
                <div class="itm-info">
                    <div class="itm-label">
                        Scope
                    </div>
                    <div class="itm-val">
                        <?php
                        echo $evg->getBenefitScopeName($bfData->scope) ?? '--';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="lg-desc">
        <div class="desc-label">
            Long Description
        </div>
        <div class="desc-val">
            <?php
            echo nl2br($bfData->descr ?? '--');
            ?>
        </div>
    </div>
    <div class="dlt-btn">
        <a href="<?php echo ADMINURL . "?page=benefits&sec=mod&id=$bfData->id"; ?>" class="bf-edt mr5">
            Edit
        </a>
        <a href="<?php echo ADMINURL . "actions/anyadmin/?method=benefit-delete&id=$bfData->id"; ?>" class="confirm">
            Delete
        </a>
    </div>
</div>