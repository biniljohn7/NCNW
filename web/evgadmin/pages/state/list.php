<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$stConds = [
    '#SRT' => 'name asc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];
$adnlqry = [];

//search by region
$shRegion = esc($_GET['sh_region'] ?? '');
if ($shRegion) {
    $stConds['region'] = $shRegion;
}

//search by country
$shNation = esc($_GET['sh_nation'] ?? '');
if ($shNation) {
    $adnlqry[] = 'region IN
    (
        SELECT id FROM regions WHERE nation IN
        (
            SELECT id FROM nations WHERE id = "' . $shNation . '"
        )
    )';
}

//search by state
$shState = esc($_GET['sh_search'] ?? '');
if ($shState) {
    $qSearch = q("%$shState%");
    $adnlqry[] = '(name like ' . $qSearch . ')';
}
if (!empty($adnlqry)) {
    $stConds['__QUERY__'][] = implode(' and ', $adnlqry);
}
$states = $pixdb->get(
    'states',
    $stConds,
    'id,
    name,
    region,
    createdAt,
    enabled,
    members'
);
$regionIds = [];
$stateIds = [];
foreach ($states->data as $row) {
    if ($row->region) {
        $regionIds[] = $row->region;
    }
    $stateIds[] = $row->id;
}
$regionData = $evg->getRegions($regionIds, 'id,name,nation');
$nationIds = [];
foreach ($regionData as $row) {
    if ($row->nation) {
        $nationIds[] = $row->nation;
    }
}
$nationData = $evg->getNations($nationIds, 'id,name');
$nations = $pixdb->get(
    'nations',
    [
        '#SRT' => 'id asc'
    ],
    'id, name'
)->data;
$regions = $pixdb->get(
    'regions',
    [
        '#SRT' => 'id asc'
    ],
    'id, name'
)->data;
loadStyle('pages/state/list');

$mbrCnt = [];
if (!empty($stateIds)) {
    $countData = $db->query('SELECT state, COUNT(state) AS cnt FROM `members_info` WHERE state IN (' . implode(', ', $stateIds) . ') GROUP BY state')->fetchAll(PDO::FETCH_OBJ);

    foreach ($countData as $cnt) {
        $mbrCnt[$cnt->state] = $cnt->cnt;
    }
}

?>
<h1>State List</h1>
<?php
breadcrumbs(
    [
        'States'
    ]
);
?>
<div class="benefits-hed rg">
    <div class="new-nation">
        <a href="<?php echo $pix->adminURL . '?page=state&sec=mod&id=new' ?>">
            Add State
        </a>
    </div>
    <div class="hed-right">
        <form action="" method="get">
            <input type="hidden" name="page" value="state" />
            <div class="bnft-select scope mr10">
                <select name="sh_nation">
                    <option value="">
                        Any Country
                    </option>
                    <?php
                    foreach ($nations as $nt) {
                        echo '<option ', $shNation == $nt->id ? 'selected' : '', ' value="', $nt->id, '">', $nt->name, '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="bnft-select ctgry">
                <select name="sh_region">
                    <option value="">
                        Any Region
                    </option>
                    <?php
                    foreach ($regions as $rg) {
                        echo '<option ', $shRegion == $rg->id ? 'selected' : '', ' value="', $rg->id, '">', $rg->name, '</option>';
                    }
                    ?>
                </select>
            </div>
            <?php
            KeySearch(
                'sh_search',
                $shState,
                'Search by State'
            );
            ?>
        </form>
    </div>
</div>
<?php
$pix->pagination(
    $states->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="nation-list rg">
    <?php
    foreach ($states->data as $row) {
        $edtLink = $pix->adminURL . "?page=state&sec=mod&id=$row->id";
        $detailsLink = $pix->adminURL . "?page=state&sec=details&id=$row->id";
        $rDatas = $regionData[$row->region] ?? null;
        $nDatas = $rDatas ? $nationData[$rDatas->nation] ?? null : null;
    ?>
        <div class="nation-card">
            <div class="card-top">
                <div class="card-left">
                    <div class="nation-info">
                        <div class="nt-fld">
                            <div class="nation-name">
                                <?php
                                echo $row->name ?? '--'
                                ?>
                            </div>
                            <div class="nt-sub">
                                <?php
                                if ($rgData = $regionData[$row->region] ?? false) {
                                    echo $rgData->name;
                                }
                                ?>
                            </div>
                            <div class="nt-sub">
                                <?php
                                echo $nDatas->name ?? '';
                                ?>
                            </div>
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
                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=state-delete&id=$row->id"; ?>" class="confirm">
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
                            echo $mbrCnt[$row->id] ?? '0';
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
    $states->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$states->pages) {
    NoResult(
        'No states found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>