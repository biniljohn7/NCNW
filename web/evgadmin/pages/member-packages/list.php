<?php
loadStyle('pages/member-packages/list');
?>
<h1>Membership Plans</h1>
<?php
breadcrumbs(
    ['Membership Plans']
);
?>
<div class="plan-types">
    <?php
    $types = $pixdb->get(
        'membership_types',
        ['#SRT' => 'name asc']
    );
    foreach ($types->data as $type) {
    ?>
        <a href="<?php echo ADMINURL, '?page=member-packages&sec=details&id=', $type->id; ?>" class="plan-item">
            <span class="plan-info">
                <span class="plan-name">
                    <?php echo $type->name; ?>
                </span>
                <span class="plan-status">
                    <?php
                    if ($type->active == 'Y') {
                    ?>
                        <span class="sts active">
                            Active
                        </span>
                    <?php
                    } else {
                    ?>
                        <span class="sts inactive">
                            Inactive
                        </span>
                    <?php
                    }
                    ?>
                </span>
            </span>
        </a>
    <?php
    }
    ?>
</div>
<?php
StickyButton($pix->adminURL . '?page=member-packages&sec=mod-type&id=new', 'add');
?>