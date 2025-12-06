<?php
$tpData = false;
if (isset($_GET['id'])) {
    $id = esc($_GET['id']);
    if ($id) {
        $tpData = $pixdb->getRow('membership_types', ['id' => $id]);
    }
}
if (!$tpData) {
    $pix->addmsg('Unknown package');
    $pix->redirect('?page=member-packages');
}

loadStyle('pages/member-packages/details');
?>
<h1>
    <?php echo $tpData->name; ?>
    <?php
    if ($tpData->active == 'Y') {
    ?>
        <span class="tp-sts active">
            Active
        </span>
    <?php
    } else {
    ?>
        <span class="tp-sts inactive">
            Inactive
        </span>
    <?php
    }
    ?>
</h1>
<?php
breadcrumbs(
    [
        'Membership Plans',
        '?page=member-packages'
    ],
    [$tpData->name]
)
?>
<div class="mb10">
    <a href="<?php echo ADMINURL, '?page=member-packages&sec=mod-type&id=', $id; ?>" class="pix-btn rounded mr10">
        <span class="material-symbols-outlined">
            stylus
        </span>
        edit type
    </a>
    <a href="<?php echo ADMINURL, 'actions/anyadmin/?method=membership-package-type-delete&id=', $id; ?>" class="pix-btn danger rounded confirm">
        <span class="material-symbols-outlined">
            delete_forever
        </span>
        delete type
    </a>
</div>

<h3 class="pt50">
    Membership Plans
</h3>

<?php
$plans = $pixdb->get(
    'membership_plans',
    [
        'type' => $id,
        '#SRT' => 'title asc, id desc'
    ]
);
if (!empty($plans->data)) {
?>
    <div class="plans">
        <?php
        foreach ($plans->data as $plan) {
        ?>
            <div class="plan-item">
                <div class="plan-name">
                    <?php echo $plan->title; ?>
                    <?php
                    if ($plan->active == 'Y') {
                    ?>
                        <span class="tp-sts active">
                            Active
                        </span>
                    <?php
                    } else {
                    ?>
                        <span class="tp-sts inactive">
                            Inactive
                        </span>
                    <?php
                    }
                    ?>
                </div>
                <div class="i-row">
                    <div class="i-label">
                        National Dues
                    </div>
                    <div class="i-amount bold-600">
                        <?php echo dollar($plan->nationalDue); ?>
                    </div>
                </div>
                <div class="i-row">
                    <div class="i-label">
                        National Late Fee
                    </div>
                    <div class="i-amount">
                        <?php echo dollar($plan->natLateFee); ?>
                    </div>
                </div>
                <div class="i-row">
                    <div class="i-label">
                        National Per Capital Fee
                    </div>
                    <div class="i-amount">
                        <?php echo dollar($plan->natCapFee); ?>
                    </div>
                </div>
                <div class="i-row">
                    <div class="i-label">
                        National Reinstatement Fee
                    </div>
                    <div class="i-amount">
                        <?php echo dollar($plan->natReinFee); ?>
                    </div>
                </div>
                <div class="i-row">
                    <div class="i-label">
                        Local Dues
                    </div>
                    <div class="i-amount">
                        <?php echo dollar($plan->localDue); ?>
                    </div>
                </div>
                <hr />
                <div class="i-row bold-600">
                    <div class="i-label">
                        Total Local Section Charges
                    </div>
                    <div class="i-amount">
                        <?php echo dollar($plan->ttlLocChaptChrg); ?>
                    </div>
                </div>
                <div class="i-row bold-600">
                    <div class="i-label">
                        Total Country Section Charges
                    </div>
                    <div class="i-amount">
                        <?php echo dollar($plan->ttlNatChaptChrg); ?>
                    </div>
                </div>
                <div class="i-row bold-600 text-12">
                    <div class="i-label">
                        Total Charges
                    </div>
                    <div class="i-amount">
                        <?php echo dollar($plan->ttlCharge); ?>
                    </div>
                </div>
                <?php
                $addons = json_decode($plan->addons);
                if (is_array($addons)) {
                    $getAddons = $pixdb->get('products', ['id' => $addons])->data;
                ?>
                    <div class="i-row pt20">
                        <div class="i-label bold-600 text-12">
                            Additional charges after membership full payment
                        </div>
                    </div>
                    <?php
                    foreach ($getAddons as $row) {
                    ?>
                        <div class="i-row">
                            <div class="i-label">
                                <?php echo $row->name; ?>
                            </div>
                            <div class="i-amount">
                                <div><?php echo dollar($row->amount); ?></div>
                            </div>
                        </div>

                        <?php
                        if ($row->validity) {
                        ?>
                            <div class="i-row">
                                <div class="i-label">
                                    -- Validity
                                </div>
                                <div class="i-amount">
                                    <div><?php echo ucfirst($row->validity); ?></div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>

                <div class="pl-actions">
                    <a href="<?php echo ADMINURL, "?page=member-packages&sec=mod-plan&type=$id&id=$plan->id"; ?>" class="pix-btn rounded">
                        <span class="material-symbols-outlined">
                            stylus
                        </span>
                    </a>
                    <a href="<?php echo ADMINURL, 'actions/anyadmin/?method=membership-package-plan-delete&id=', $plan->id; ?>" class="pix-btn danger rounded confirm">
                        <span class="material-symbols-outlined">
                            delete_forever
                        </span>
                    </a>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
<?php
    StickyButton(ADMINURL . '?page=member-packages&sec=mod-plan&type=' . $id . '&id=new', 'add');
    // 
} else {
    NoResult(
        'No Plans Found',
        'No plans added yet.',
        [
            'Add Plan',
            ADMINURL . '?page=member-packages&sec=mod-plan&type=' . $id . '&id=new'
        ]
    );
}
?>