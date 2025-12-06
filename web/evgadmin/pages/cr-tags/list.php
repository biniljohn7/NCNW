<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$conds = [
    '#SRT' => 'id desc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];

// search by tag
$shKey = esc($_GET['shkey'] ?? '');
if ($shKey) {
    $qSearch = q("%$shKey%");
    $conds['__QUERY__'][] = "(
        name like $qSearch
    )";
}

$crTags = $pixdb->get(
    'career_types',
    $conds,
    'id,
    name,
    createdAt,
    enabled'
);

loadStyle('pages/career-tag/list');
loadScript('pages/cr-tags/career-tag');

?>
<div class="page-head">
    <div class="head-col">
        <h1>Career Tag List</h1>
        <?php
        breadcrumbs(
            [
                'Career Tags'
            ]
        );
        ?>
    </div>
    <div class="sh-col">
        <form method="get">
            <input type="hidden" name="page" value="cr-tags" />
            <?php
            KeySearch(
                'shkey',
                $shKey,
                'Search by Tag'
            );
            ?>
        </form>
    </div>
</div>
<?php
$pix->pagination(
    $crTags->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="crtags-list">
    <?php
    foreach ($crTags->data as $row) {
        $edtLink = $pix->adminURL . "?page=cr-tags&sec=mod&id=$row->id";
    ?>
        <div class="tag-card">
            <div class="card-info">
                <div class="tag-name">
                    <?php
                    echo $row->name ?? '--'
                    ?>
                </div>
                <div class="tag-sts">
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
                <div class="card-btm">
                    <div class="items dt mr10px">
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
                    <div class="items btns">
                        <a href="<?php echo $edtLink; ?>" class="mr5">
                            <span class="material-symbols-outlined icn">
                                edit
                            </span>
                        </a>
                        <a href="<?php echo ADMINURL . "actions/anyadmin/?method=cr-tag-delete&id=$row->id"; ?>" class="confirm">
                            <span class="material-symbols-outlined icn">
                                delete
                            </span>
                        </a>
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
    $crTags->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$crTags->pages) {
    NoResult(
        'No career tags found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>
<?php
StickyButton($pix->adminURL . '?page=cr-tags&sec=mod&id=new', 'add');
?>