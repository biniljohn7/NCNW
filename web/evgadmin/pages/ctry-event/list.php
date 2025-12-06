<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$conds = [
    '#SRT' => 'ctryName asc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];
$adnlqry = [];

// searching
$shKey = esc($_GET['shKey'] ?? '');
if ($shKey) {
    $qSearch = q("%$shKey%");
    $adnlqry[] = '(ctryName like ' . $qSearch . ')';
}

$conds['__QUERY__'][] = "type = 'Event'";
if (!empty($adnlqry)) {
    $conds['__QUERY__'][] = implode(' and ', $adnlqry);
}

$crtyEvent = $pixdb->get(
    'categories',
    $conds,
    'id,
    ctryName,
    itryIcon'
);

loadStyle('pages/category/list');
loadScript('pages/category/mod');

?>
<div class="page-head">
    <div class="head-col">
        <h1>
            Event Categories
        </h1>
        <?php
        breadcrumbs(
            [
                'Events'
            ]
        )
        ?>
    </div>
    <div class="sh-col">
        <form method="get">
            <input type="hidden" name="page" value="ctry-event" />
            <?php
            KeySearch(
                'shKey',
                $shKey,
                'Search by event'
            )
            ?>
        </form>
    </div>
</div>
<?php
$pix->pagination(
    $crtyEvent->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
)
?>
<div class="ctry-list">
    <?php
    foreach ($crtyEvent->data as $row) {
        $edtLink = $pix->adminURL . "?page=ctry-event&sec=mod&id=$row->id";
    ?>
        <div class="list-card">
            <div class="card-hed">
                <?php
                echo $row->ctryName ?? '';
                ?>
            </div>
            <div class="card-img">
                <?php
                if ($row->itryIcon) {
                    $row->itryIcon = $pix->uploadPath . 'category-image/' . $pix->thumb(
                        $row->itryIcon,
                        '450x450'
                    );
                ?>
                    <img src="<?php echo $row->itryIcon; ?>">
                <?php
                } else {
                ?>
                    <div class="no-img"></div>
                <?php
                }
                ?>
            </div>
            <div class="card-btns">
                <a href="<?php echo $edtLink; ?>" class="edt">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                </a>
                <a href="<?php echo $pix->adminURL . "actions/anyadmin/?method=category-delete&type=event&id=$row->id"; ?>" class="dlt confirm">
                    <span class="material-symbols-outlined icn">
                        delete
                    </span>
                </a>
            </div>
        </div>
    <?php
    }
    ?>
</div>
<?php
$pix->pagination(
    $crtyEvent->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$crtyEvent->pages) {
    NoResult(
        'No event category found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>
<?php
StickyButton($pix->adminURL . '?page=ctry-event&sec=mod&id=new', 'add');
?>