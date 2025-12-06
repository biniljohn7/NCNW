<?php
loadStyle('pages/pay-categories/list');
?>
<h1>Payment Categories</h1>
<?php
breadcrumbs(
    ['Payment Categories']
);
?>
<div class="pay-types">
    <?php
    $payCatgs = $pixdb->get(
        'products',
        []
    );
    foreach ($payCatgs->data as $row) {
    ?>
        <div class="pay-item">
            <div class="card-right">
                <a href="<?php echo ADMINURL, '?page=payment-categories&sec=mod&id=', $row->id; ?>" class="mr5">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                </a>
                <a href="<?php echo ADMINURL . "actions/anyadmin/?method=product-delete&id=$row->id"; ?>" class="confirm">
                    <span class="material-symbols-outlined icn">
                        delete
                    </span>
                </a>
            </div>
            <div class="pay-info">
                <div class="pay-name">
                    <?php echo $row->name; ?>
                </div>
                <?php
                if ($row->type == 'fee') {
                ?>
                    <div class="pay-amt">
                        <?php echo dollar($row->amount, 1, 1); ?>
                    </div>
                <?php
                }
                ?>
                <div class="pay-status pt10">
                    <?php
                    if ($row->enabled == 'Y') {
                    ?>
                        <div class="sts active">
                            Enabled
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="sts inactive">
                            Disabled
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>
<?php
StickyButton($pix->adminURL . '?page=payment-categories&sec=mod&id=new', 'add');
?>