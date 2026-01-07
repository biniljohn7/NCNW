<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$ntConds = [
    '#SRT' => 'name asc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];

//Search by countries
$shSearch = esc($_GET['sh_search'] ?? '');
if ($shSearch) {
    $qSearch = q("%$shSearch%");
    $ntConds['__QUERY__'][] = '(name like ' . $qSearch . ')';
}
$nations = $pixdb->get(
    'nations',
    $ntConds,
    'id,
    name,
    createdAt,
    enabled,
    members'
);
loadStyle('pages/nation/list');
loadScript('pages/nation');


?>
<h1>Country List</h1>
<?php
breadcrumbs(
    [
        'Countries'
    ]
)
?>
<div class="nation-top">
    <div class="new-nation">
        <a href="<?php echo $pix->adminURL . '?page=nation&sec=mod&id=new' ?>">
            Add Country
        </a>
    </div>
    <div class="hed-right">
        <form action="" method="get">
            <input type="hidden" name="page" value="nation" />
            <?php
            KeySearch(
                'sh_search',
                $shSearch,
                'Search by countries'
            );
            ?>
        </form>
    </div>
</div>
<?php
$pix->pagination(
    $nations->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="nation-list">
    <?php
    foreach ($nations->data as $row) {
        $edtLink = $pix->adminURL . "?page=nation&sec=mod&id=$row->id";
    ?>
        <div class="nation-card">
            <div class="card-top">
                <div class="card-left">
                    <div class="nation-info">
                        <div class="nation-name">
                            <?php
                            echo $row->name ?? '--'
                            ?>
                        </div>
                        <div class="nation-sts">
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
                    <a href="<?php echo $edtLink; ?>" class="mr5">
                        <span class="material-symbols-outlined icn">
                            edit
                        </span>
                    </a>
                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=nation-delete&id=$row->id"; ?>" class="confirm">
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
                <div class="items">
                    <div class="itm-icn">
                        <span class="material-symbols-outlined">
                            groups
                        </span>
                    </div>
                    <div class="itm-info">
                        <div class="itm-label">
                            Members
                        </div>
                        <div class="itm-val">
                            <?php
                            echo $row->members ?? '--';
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
    $nations->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$nations->pages) {
    NoResult(
        'No countries found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>