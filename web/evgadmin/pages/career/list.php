<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$conds = [
    '#SRT' => 'id desc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];

// searching
$shKey = esc($_GET['shkey'] ?? '');
if ($shKey) {
    $qSearch = q("%$shKey%");
    $conds['__QUERY__'][] = "(
        title like $qSearch  OR 
        type IN (
            SELECT id FROM career_types WHERE name like $qSearch
        ) OR
        address like $qSearch OR
        description like $qSearch
    )";
}

$careers = $pixdb->get(
    'careers',
    $conds,
    'id,
    title,
    type,
    image,
    enabled'
);
$typeIds = [];
foreach ($careers->data as $row) {
    if ($row->type) {
        $typeIds[] = $row->type;
    }
}
$typeDatas = $evg->getCareerTypes($typeIds, 'id, name');

$careerStatus = [
    'active' => 'Active',
    'inactive' => 'Inactive'
];
loadStyle('pages/career/list');
?>
<div class="page-head">
    <div class="head-col">
        <h1>Career List</h1>
        <?php
        breadcrumbs(
            [
                'Careers'
            ]
        );
        ?>
    </div>
    <div class="sh-col">
        <form method="get">
            <input type="hidden" name="page" value="career" />
            <?php
            KeySearch(
                'shkey',
                $shKey,
                'Search by Keywords'
            );
            ?>
        </form>
    </div>
</div>
<div class="career-count">
    Total
    <strong>
        <?php echo $careers->totalRows; ?>
    </strong>
    <?php echo ' career', $careers->totalRows > 1 ? 's' : ''; ?>
</div>

<?php
$pix->pagination(
    $careers->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="career-list">
    <?php
    foreach ($careers->data as $crs) {
    ?>
        <a href="<?php echo $pix->adminURL, '?page=career&sec=details&id=', $crs->id; ?>" class="career-card">
            <span class="career-thumb">
                <?php
                if ($crs->image) {
                ?>
                    <img src="<?php echo $pix->uploadPath, 'career-image/', $pix->thumb($crs->image, '150x150'); ?>" class="cr-img">
                <?php
                } else {
                ?>
                    <span class="no-logo">
                        <span class="material-symbols-outlined icn">
                            hide_image
                        </span>
                    </span>
                <?php
                }
                ?>
            </span>
            <span class="career-info">
                <span class="career-title">
                    <?php echo $crs->title ?? ''; ?>
                </span>
                <span class="career-type">
                    <?php
                    if ($tpData = $typeDatas[$crs->type] ?? false) {
                        echo $tpData->name;
                    }
                    ?>
                </span>
                <?php
                if ($crs->enabled) {
                    $sts = $crs->enabled == 'Y' ? 'active' : 'inactive';
                ?>
                    <span class="cr-sts">
                        <span class="sts <?php echo $sts; ?>">
                            <?php
                            echo $careerStatus[$sts];
                            ?>
                        </span>
                    </span>
                <?php
                }
                ?>
            </span>
        </a>
    <?php
    }
    ?>
</div>
<?php
$pix->pagination(
    $careers->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);

if (!$careers->pages) {
    NoResult(
        'No careers found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>
<?php
StickyButton($pix->adminURL . '?page=career&sec=mod&id=new', 'add');
?>