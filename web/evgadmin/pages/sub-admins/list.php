<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$conds = [
    '#SRT' => 'name asc, id desc',
    '__page' => $pgn,
    '__limit' => 24,
    '__QUERY__' => ['type!="admin"']
];

// searching
$shKey =  esc($_GET['shkey'] ?? '');
if ($shKey) {
    $qSearch = q("%$shKey%");
    $conds['__QUERY__'][] = "(
        `name` like $qSearch OR 
        email like $qSearch OR
        username like $qSearch
    )";
}

$admins = $pixdb->get(
    'admins',
    $conds,
    'id,
    name,
    enabled,
    perms'
);

loadStyle('pages/sub-admins/list');
?>
<div class="page-head">
    <div class="head-col">
        <h1>Sub Admins</h1>
        <?php
        breadcrumbs(
            ['Sub Admins']
        );
        ?>
    </div>
    <div class="sh-col">
        <form method="get">
            <input type="hidden" name="page" value="sub-admins" />
            <?php
            KeySearch(
                'shkey',
                $shKey,
                'Search admins'
            );
            ?>
        </form>
    </div>
</div>

<div class="admin-list">
    <?php
    $permNames = $perms = [
        'benefit' => 'Benefit Management',
        'events' => 'Event Coordination',
        'location' => 'Location Oversight',
        'members' => 'Member Administration',
        'transactions' => 'Transaction Management',
        'career' => 'Career Services',
        'advocacy' => 'Advocacy Initiatives',
        'paid-plans' => 'Paid Plans',
        'point-rules' => 'Point System Rules',
        'messages' => 'Message Center',
        'cms-pages' => 'Content Management System (CMS)',
        'contact-enquiries' => 'Contact Inquiries',
        'statistics' => 'Analytics and Statistics',
        'email-templates' => 'Email Templates',
    ];

    foreach ($admins->data as $adm) {
    ?>
        <a class="team-list-item" href="<?php echo ADMINURL . "?page=sub-admins&sec=details&id=$adm->id";
                                        ?>">
            <span class="team-img">
                <span class="no-img">
                    <span class="material-symbols-outlined usr-icon">
                        person
                    </span>
                </span>
                <?php
                ?>
            </span>
            <span class="team-info">
                <span class="team-mbr-name">
                    <?php echo $adm->name; ?>
                </span>
                <span class="adm-status <?php echo $adm->enabled == 'Y' ? 'active' : ''; ?>">
                    <?php echo $adm->enabled == 'Y' ? 'Enabled' : 'Disabled'; ?>
                </span>
                <span class="adm-perms">
                    <?php
                    $applyPermLabel = function ($key) use ($permNames) {
                        return $permNames[$key] ?? $key;
                    };
                    $perms = array_map(
                        $applyPermLabel,
                        explode(',', $adm->perms ?: '')
                    );
                    $permLen = count($perms);
                    $maxLen = 5;
                    if ($permLen > $maxLen) {
                        $perms = array_slice($perms, 0, $maxLen);
                    }
                    foreach ($perms as $perm) {
                    ?>
                        <span class="perm-txt">
                            <?php echo $perm; ?>
                        </span>
                    <?php
                    }
                    if ($permLen > $maxLen) {
                    ?>
                        <span class="perm-txt more">
                            +<?php echo $permLen - $maxLen; ?> more..
                        </span>
                    <?php
                    }
                    ?>
                </span>
            </span>
        </a>
    <?php
    }
    ?>
</div>
<?php
$pix->pagination(
    $admins->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);

if (!$admins->pages) {
    NoResult(
        'No admins found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}
//
StickMenu(
    [
        'add',
        'New Sub-Admin',
        0,
        0,
        '?page=sub-admins&sec=mod'
    ],
    [
        'vertical_align_bottom',
        'Import',
        0,
        0,
        '?page=sub-admins&sec=import-file'
    ]
    //[$pix->adminURL . '?page=sub-admins&sec=mod', 'add'],
);
?>