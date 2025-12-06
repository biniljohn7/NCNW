<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$rgConds = [
    '#SRT' => 'id desc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];

$adnlqry = [];

//search by country
$shNation = esc($_GET['sh_nation'] ?? '');
if ($shNation) {
    $rgConds['nation'] = $shNation;
}

//search by region
$shRegion = esc($_GET['sh_search'] ?? '');
if ($shRegion) {
    $qSearch = q("%$shRegion%");
    $adnlqry[] = '(name like ' . $qSearch . ')';
}
if (!empty($adnlqry)) {
    $rgConds['__QUERY__'][] = implode(' and ', $adnlqry);
}
$regions = $pixdb->get(
    'regions',
    $rgConds,
    'id,
    name,
    nation,
    createdAt,
    enabled,
    members'
);
$nationIds = [];
foreach ($regions->data as $row) {
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
loadStyle('pages/nation/list');
loadScript('pages/region');


?>
<h1>Region List</h1>
<?php
breadcrumbs(
    [
        'Regions'
    ]
);
?>
<div class="nation-top rg">
    <div class="new-nation">
        <a href="<?php echo $pix->adminURL . '?page=region&sec=mod&id=new' ?>">
            Add Region
        </a>
    </div>
    <div class="hed-right">
        <form action="" method="get">
            <input type="hidden" name="page" value="region" />
            <div class="bnft-select scope">
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
            <?php
            KeySearch(
                'sh_search',
                $shRegion,
                'Search by Region'
            );
            ?>
        </form>
    </div>
</div>
<?php
$pix->pagination(
    $regions->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>
<div class="nation-list rg">
    <?php
    foreach ($regions->data as $row) {
        $edtLink = $pix->adminURL . "?page=region&sec=mod&id=$row->id";
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
                                if ($ntData = $nationData[$row->nation] ?? false) {
                                    echo $ntData->name;
                                }
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
                    <a href="<?php echo $edtLink; ?>" class="mr5">
                        <span class="material-symbols-outlined icn">
                            edit
                        </span>
                    </a>
                    <a href="<?php echo ADMINURL . "actions/anyadmin/?method=region-delete&id=$row->id"; ?>" class="confirm">
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
    $regions->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$regions->pages) {
    NoResult(
        'No Regions found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>