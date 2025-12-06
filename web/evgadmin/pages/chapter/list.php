<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$cpConds = [
    '#SRT' => 'id desc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];
$adnlqry = [];

//search by state
$shState = esc($_GET['sh_state'] ?? '');
if ($shState) {
    $cpConds['state'] = $shState;
}

//search by region
$shRegion = esc($_GET['sh_region'] ?? '');
if ($shRegion) {
    $adnlqry[] = 'state IN(
        SELECT id FROM states WHERE region = "' . $shRegion . '"
    )';
}

//search by nation
$shNation = esc($_GET['sh_nation'] ?? '');
if ($shNation) {
    $adnlqry[] = 'state IN(
        SELECT id FROM states WHERE region IN
        (
            SELECT id FROM regions WHERE nation = "' . $shNation . '"
        )
    )';
}

//search by chapter
$shChapter = escape($_GET['sh_search'] ?? '');
if ($shChapter) {
    $qSearch = q("%$shChapter%");
    $adnlqry[] = '(name like ' . $qSearch . ')';
}
if (!empty($adnlqry)) {
    $cpConds['__QUERY__'][] = implode(' and ', $adnlqry);
}
$chapters = $pixdb->get(
    'chapters',
    $cpConds,
    'id,
    name,
    state,
    createdAt,
    enabled,
    members'
);
$stateIds = [];
$chptrIds = [];
foreach ($chapters->data as $row) {
    if ($row->state) {
        $stateIds[] = $row->state;
    }
    $chptrIds[] = $row->id;
}
$stateData = $evg->getStates($stateIds, 'id,name,region');
$regionIds = [];
foreach ($stateData as $row) {
    if ($row->region) {
        $regionIds[] = $row->region;
    }
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
loadStyle('pages/chapter/list');
loadScript('pages/chapter/list');

$mbrCnt = [];
if (!empty($chptrIds)) {
    $countData = $db->query('SELECT cruntChptr, COUNT(cruntChptr) AS cnt FROM `members_info` WHERE cruntChptr IN (' . implode(', ', $chptrIds) . ') GROUP BY cruntChptr')->fetchAll(PDO::FETCH_OBJ);

    foreach ($countData as $cnt) {
        $mbrCnt[$cnt->cruntChptr] = $cnt->cnt;
    }
}
?>
<h1>Section List</h1>
<?php
breadcrumbs(
    [
        'Sections'
    ]
);
?>
<div class="chapter-hed">
    <div class="new-chapter">
        <a href="<?php echo $pix->adminURL . '?page=chapter&sec=mod&id=new' ?>">
            Add Section
        </a>
    </div>
    <div class="hed-right">
        <form action="" method="get">
            <input type="hidden" name="page" value="chapter" />
            <div class="fltr-left">
                <div class="chapter-select nation mr10">
                    <select name="sh_nation" id="shNation">
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
                <div class="chapter-select nation">
                    <select name="sh_region" id="shRegion">
                        <option value="">
                            Any Region
                        </option>
                        <?php
                        if ($shNation) {
                            $regions = $pixdb->get(
                                'regions',
                                [
                                    '#SRT' => 'id asc',
                                    'nation' => $shNation
                                ],
                                'id, name'
                            )->data;
                            foreach ($regions as $rg) {
                                echo '<option ', $shRegion == $rg->id ? 'selected' : '', ' value="', $rg->id, '">', $rg->name, '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="fltr-right">
                <div class="chapter-select state mr10">
                    <select name="sh_state" id="shState">
                        <option value="">
                            Any State
                        </option>
                        <?php
                        if ($shRegion) {
                            $states = $pixdb->get(
                                'states',
                                [
                                    '#SRT' => 'id asc',
                                    'region' => $shRegion
                                ],
                                'id, name'
                            )->data;
                            foreach ($states as $st) {
                                echo '<option ', $shState == $st->id ? 'selected' : '', ' value="', $st->id, '">', $st->name, '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
                KeySearch(
                    'sh_search',
                    $shChapter,
                    'Search by Section'
                )
                ?>
            </div>
        </form>
    </div>
</div>
<?php
$pix->pagination(
    $chapters->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="chapter-list rg">
    <?php
    foreach ($chapters->data as $row) {
        $edtLink = $pix->adminURL . "?page=chapter&sec=mod&id=$row->id";
        $detailsLink = $pix->adminURL . "?page=chapter&sec=details&id=$row->id";
        $stDatas = $stateData[$row->state] ?? null;
        $rgDatas = $stDatas ? $regionData[$stDatas->region] ?? null : null;
        $ntDatas = $rgDatas ? $nationData[$rgDatas->nation] ?? null : null;
    ?>
        <div class="chapter-card">
            <div class="card-top">
                <div class="card-left">
                    <div class="chapter-info">
                        <div class="nt-fld">
                            <div class="chapter-name">
                                <?php
                                echo $row->name;
                                ?>
                            </div>
                            <div class="nt-sub">
                                <?php
                                if ($cpData = $stateData[$row->state] ?? false) {
                                    echo $cpData->name;
                                }
                                ?>
                            </div>

                            <div class="nt-sub">
                                <span>
                                    <?php
                                    echo isset($rgDatas->name) ? $rgDatas->name . ', ' : '';
                                    ?>
                                </span>
                                <span>
                                    <?php
                                    echo $ntDatas->name ?? '';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="chapter-sts">
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
                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=chapter-delete&id=$row->id"; ?>" class="confirm">
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
                            echo date('F d, Y', strtotime($row->createdAt));
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
    $chapters->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$chapters->pages) {
    NoResult(
        'No Section found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>