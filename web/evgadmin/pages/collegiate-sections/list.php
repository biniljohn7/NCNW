<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$afConds = [
    '#SRT' => 'name asc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];
$adnlqry = [];

//search by collegiate
$shCollegiate = escape($_GET['sh_search'] ?? '');
if ($shCollegiate) {
    $qSearch = q("%$shCollegiate%");
    $adnlqry[] = '(name like ' . $qSearch . ')';
}

if (!empty($adnlqry)) {
    $afConds['__QUERY__'][] = implode(' and ', $adnlqry);
}

$collegiates = $pixdb->get(
    'collegiate_sections',
    $afConds,
    'id,
    name,
    createdAt,
    enabled'
);
loadStyle('pages/collegiate/list');

?>
<h1>Collegiate Sections</h1>
<?php
breadcrumbs(
    ['Collegiate Sections']
);
?>
<div class="affiliate-top">
    <div class="new-affiliate">
        <a href="<?php echo $pix->adminURL . '?page=collegiate-sections&sec=mod&id=new' ?>">
            Add Collegiate Section
        </a>
    </div>
    <div class="hed-right">
        <form action="" method="get">
            <input type="hidden" name="page" value="collegiate-sections" />
            <?php
            KeySearch(
                'sh_search',
                $shCollegiate,
                'Search by collegiate'
            );
            ?>
        </form>
    </div>
</div>
<?php
$pix->pagination(
    $collegiates->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="affiliate-list">
    <?php
    foreach ($collegiates->data as $row) {
        $edtLink = $pix->adminURL . "?page=collegiate-sections&sec=mod&id=$row->id";
        $detailsLink = $pix->adminURL . "?page=collegiate-sections&sec=details&id=$row->id";
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
                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=collegiate-delete&id=$row->id"; ?>" class="confirm">
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
    $collegiates->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$collegiates->pages) {
    NoResult(
        'No affiliates found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>