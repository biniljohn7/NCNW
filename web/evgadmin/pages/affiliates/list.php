<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$afConds = [
    '#SRT' => 'id desc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];
$adnlqry = [];

//search by affiliate
$shAffiliate = escape($_GET['sh_search'] ?? '');
if ($shAffiliate) {
    $qSearch = q("%$shAffiliate%");
    $adnlqry[] = '(name like ' . $qSearch . ')';
}

if (!empty($adnlqry)) {
    $afConds['__QUERY__'][] = implode(' and ', $adnlqry);
}

$affiliates = $pixdb->get(
    'affiliates',
    $afConds,
    'id,
    name,
    createdAt,
    enabled'
);
loadStyle('pages/affiliate/list');

?>
<h1>Affiliate List</h1>
<?php
breadcrumbs(
    ['Affiliates']
);
?>
<div class="affiliate-top">
    <div class="new-affiliate">
        <a href="<?php echo $pix->adminURL . '?page=affiliates&sec=mod&id=new' ?>">
            Add Affiliate
        </a>
    </div>
    <div class="hed-right">
        <form action="" method="get">
            <input type="hidden" name="page" value="affiliates" />
            <?php
            KeySearch(
                'sh_search',
                $shAffiliate,
                'Search by affiliates'
            );
            ?>
        </form>
    </div>
</div>
<?php
$pix->pagination(
    $affiliates->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="affiliate-list">
    <?php
    foreach ($affiliates->data as $row) {
        $edtLink = $pix->adminURL . "?page=affiliates&sec=mod&id=$row->id";
        $detailsLink = $pix->adminURL . "?page=affiliates&sec=details&id=$row->id";
    ?>
        <div class="affiliate-card">
            <div class="card-top">
                <div class="card-left">
                    <div class="affiliate-info">
                        <div class="affiliate-name">
                            <?php
                            echo $row->name ?? '--'
                            ?>
                        </div>
                        <div class="affiliate-sts">
                            <?php
                            if ($row->enabled == 'Y') {
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
                        </div>
                    </div>
                </div>
                <div class="card-right">
                    <a href="<?php echo $detailsLink; ?>" class="mr5">
                        <span class="material-symbols-outlined icn">
                            visibility
                        </span>
                    </a>
                    <a href="<?php echo $edtLink; ?>" class="mr5">
                        <span class="material-symbols-outlined icn">
                            edit
                        </span>
                    </a>
                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=affiliate-delete&id=$row->id"; ?>" class="confirm">
                        <span class="material-symbols-outlined icn">
                            delete
                        </span>
                    </a>
                </div>
            </div>
            <div class="card-btm">
                <div class="items">
                    <div class="itm-icn">
                        <span class="material-symbols-outlined">
                            fact_check
                        </span>
                    </div>
                    <div class="itm-info">
                        <div class="itm-label">
                            Created Date
                        </div>
                        <div class="itm-val">
                            <?php
                            echo date('F d, Y', strtotime($row->createdAt)) ?? '--';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    ?>
</div>
<?php
$pix->pagination(
    $affiliates->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$affiliates->pages) {
    NoResult(
        'No affiliates found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>