<?php
global
    $evg;
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$bfConds = [
    '#SRT' => 'name asc',
    '__page' => $pgn,
    '__limit' => 20,
    '__QUERY__' => array()
];
$adnlqry = [];

// Search by scope
$shScope = esc($_GET['sh_sp'] ?? '');
if ($shScope) {
    switch ($shScope) {
        case 'national':
            $adnlqry[] = 'scope = "national"';
            break;
        case 'state':
            $adnlqry[] = 'scope = "state"';
            break;
        case 'regional':
            $adnlqry[] = 'scope = "regional"';
            break;
        case 'chapter':
            $adnlqry[] = 'scope = "chapter"';
            break;
    }
}

//Search by  category
$shCategory = esc($_GET['sh_cat'] ?? '');
if ($shCategory) {
    $bfConds['ctryId'] = $shCategory;
}

//Search by name
$shSearch =  esc($_GET['sh_search'] ?? '');
if ($shSearch) {
    $qshSearch = q("%$shSearch%");
    $adnlqry[] = '(
        name like ' . $qshSearch . ' OR 
        provider IN (SELECT id FROM benefit_providers WHERE name LIKE ' . $qshSearch . ') OR
        code like ' . $qshSearch . ' OR
        shortDescr like ' . $qshSearch . ' OR 
        descr like ' . $qshSearch . '
    )';
}

if (!empty($adnlqry)) {
    $bfConds['__QUERY__'][] = implode(' and ', $adnlqry);
}
$benefits = $pixdb->get(
    'benefits',
    $bfConds,
    'id,
    ctryId,
    provider,
    name,
    scope,
    status'
);
$catgoryIds = [];
$providerIds = [];
foreach ($benefits->data as $row) {
    if ($row->ctryId) {
        $catgoryIds[] = $row->ctryId;
    }
    if ($row->provider) {
        $providerIds[] = $row->provider;
    }
}
$catDatas = $evg->getCategories($catgoryIds, 'id,ctryName');
$pvdDatas = $evg->getProviders($providerIds, 'id,name,status');
$category = $pixdb->get(
    'categories',
    [
        '#SRT' => 'ctryName asc'
    ],
    'id, ctryName'
)->data;

$bfScope = [
    'national' => 'National',
    'regional' => 'Regional',
    'chapter' => 'Section',
    'state' => 'State',
];

loadStyle('pages/benefits/list');
?>
<h1>Benefits List</h1>
<?php
breadcrumbs(
    [
        'Benefits'
    ]
);
?>
<div class="benefits-hed">
    <div class="hed-left">
        <div class="list-count">
            <?php echo $benefits->totalRows; ?>
            <?php
            echo ' Benefit', $benefits->totalRows > 1 ? 's' : '';
            ?>
        </div>
    </div>
    <div class="hed-right">
        <form action="" method="get">
            <input type="hidden" name="page" value="benefits">
            <div class="bnft-select scope mr10">
                <select name="sh_sp">
                    <option value="">
                        Any Scope
                    </option>
                    <?php
                    foreach ($bfScope as $key => $val) {
                        echo '<option ', $shScope == $key ? 'selected' : '', ' value="', $key, '">', $val, '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="bnft-select ctgry">
                <select name="sh_cat">
                    <option value="">
                        Any Category
                    </option>
                    <?php
                    foreach ($category as $ct) {
                        echo '<option ', $shCategory == $ct->id ? 'selected' : '', ' value="', $ct->id, '">', $ct->ctryName, '</option>';
                    }
                    ?>
                </select>
            </div>
            <?php
            KeySearch(
                'sh_search',
                $shSearch,
                'Search by Benefits'
            );
            ?>
        </form>
    </div>
</div>
<div class="bnft-wrap">
    <div class="bnft-card hed">
        <div class="list-item left">
            <div class="txt-left">
                Name
            </div>
            <div class="txt-right">
                Category
            </div>
        </div>
        <div class="list-item mid">
            <div class="txt-left">
                Provider
            </div>
            <div class="txt-right">
                Scope
            </div>
        </div>
        <div class="list-item right">
            <div class="txt-left">
                Status
            </div>
            <div class="txt-right">
                Actions
            </div>
        </div>
    </div>
    <?php
    if ($benefits->pages > 0) {
        $pix->pagination(
            $benefits->pages,
            $pgn,
            5,
            null,
            'pt30 mb50 text-left'
        );
        foreach ($benefits->data as $row) {
            $edtLink = $pix->adminURL . "?page=benefits&sec=mod&id=$row->id";
            $dtlLink = $pix->adminURL . "?page=benefits&sec=details&id=$row->id"
    ?>
            <div class="bnft-card list">
                <div class="list-item left">
                    <div class="txt-left labels nm">
                        <?php
                        echo $row->name;
                        ?>
                    </div>
                    <div class="txt-right labels ct">
                        <?php
                        if ($ctData = $catDatas[$row->ctryId] ?? false) {
                            echo $ctData->ctryName;
                        } else {
                            echo '--';
                        }
                        ?>
                    </div>
                </div>
                <div class="list-item mid">
                    <div class="txt-left labels pv">
                        <?php
                        if ($pvData = $pvdDatas[$row->provider] ?? false) {
                            echo $pvData->name;
                        } else {
                            echo '--';
                        }
                        ?>
                    </div>
                    <div class="txt-right labels sp">
                        <?php
                        echo $evg->getBenefitScopeName($row->scope);
                        ?>
                    </div>
                </div>
                <div class="list-item right">
                    <div class="txt-left">
                        <?php
                        if ($row->status) {
                            $sts = $row->status == 'active' ? 'active' : 'inactive';
                        ?>
                            <span class="sts <?php echo $sts; ?>">
                                <?php
                                echo $evg->getBenefitStatusName($sts);
                                ?>
                            </span>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="txt-right">
                        <a href="<?php echo $dtlLink; ?>" class="mr5">
                            <span class="material-symbols-outlined icn">
                                visibility
                            </span>
                        </a>
                        <a href="<?php echo $edtLink; ?>">
                            <span class="material-symbols-outlined icn">
                                edit
                            </span>
                        </a>
                    </div>
                </div>
            </div>
    <?php
        }
        $pix->pagination(
            $benefits->pages,
            $pgn,
            5,
            null,
            'pt30 mb50 text-left'
        );
    } else {
        NoResult(
            'No benefits found',
            'We couldn\'t find any results. Maybe try a new search.'
        );
    }
    ?>
</div>
<?php
StickyButton($pix->adminURL . '?page=benefits&sec=mod&id=new', 'add');
?>