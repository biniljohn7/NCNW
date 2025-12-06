<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$grpConds = [
    '#SRT' => 'id desc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => array()
];
$adnlqry = [];

if (!empty($adnlqry)) {
    $grpConds['__QUERY__'][] = implode(' and ', $adnlqry);
}
$grpInfo = $pixdb->get(
    'message_groups',
    $grpConds,
    'id,
    title,
    createdOn'
);

$grpIds = [];
foreach ($grpInfo->data as $grp) {
    $grpIds[] = $grp->id;
}

$mbrCnt = [];
if (!empty($grpIds)) {
    $countData = $db->query('SELECT `groupId`, COUNT(`groupId`) AS cnt FROM `message_group_members` WHERE `groupId` IN (' . implode(', ', $grpIds) . ') GROUP BY `groupId`')->fetchAll(PDO::FETCH_OBJ);

    foreach ($countData as $cnt) {
        $mbrCnt[$cnt->groupId] = $cnt->cnt;
    }
}

loadStyle('pages/groups/list');
loadScript('pages/groups/list');
?>
<div class="page-head">
    <div class="head-col">
        <h1>Message Groups</h1>
        <?php
        breadcrumbs(
            ['Message Groups']
        );
        ?>
    </div>
    <div class="sh-col">
        <span
            class="pix-btn site rounded mr5" id="newGroupAddBtn">
            <span class="material-symbols-outlined fltr">
                group_add
            </span>
            New Group
        </span>
    </div>
</div>

<?php
$pix->pagination(
    $grpInfo->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
?>

<div class="group-container">
    <?php
    foreach ($grpInfo->data as $grp) {
    ?>
        <div class="group-card">
            <div class="card-top">
                <div class="card-left">
                    <div class="group-info">
                        <div class="nt-fld">
                            <div class="group-name">
                                <?php echo $grp->title; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-right">
                    <a href="<?php echo ADMINURL, '?page=groups&sec=group-view&id=', $grp->id; ?>" class="mr5">
                        <span class="material-symbols-outlined icn">
                            visibility
                        </span>
                    </a>
                    <?php /*
                    <a href="#" class="mr5">
                        <span class="material-symbols-outlined icn">
                            edit
                        </span>
                    </a>
                    */ ?>
                    <a href="<?php echo ADMINURL, 'actions/anyadmin/?method=message-group-delete&id=', $grp->id; ?>" class="confirm">
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
                            <?php echo $grp->createdOn ? date('M d, Y', strtotime($grp->createdOn)) : ''; ?>
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
                            <?php echo $mbrCnt[$grp->id] ?? '0'; ?>
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
    $grpInfo->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);
if (!$grpInfo->pages) {
    NoResult(
        'No Message Groups found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
?>