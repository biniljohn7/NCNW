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
        `name` like $qSearch OR 
        `address` like $qSearch OR 
        `descrptn` like $qSearch
    )";
}

// search scope
$scopeList = [
    'national' => 'National',
    'state' => 'State',
    'regional' => 'Regional',
    'chapter' => 'Section'
];
$shScope = esc($_GET['scope'] ?? '');
if ($shScope) {
    if (isset($scopeList[$shScope])) {
        $conds['scope'] = $shScope;
    }
}

$events = $pixdb->get(
    'events',
    $conds,
    'id,
    name,
    enabled,
    date,
    enddate,
    scope,
    image'
);

loadStyle('pages/events/list');
loadScript('pages/events/list');
?> <div class="page-head">
    <div class="head-col">
        <h1>Events List</h1>
        <?php
        breadcrumbs(['Events']);
        ?>
    </div>
    <div class="sh-col">
        <form method="get" id="searchForm">
            <input type="hidden" name="page" value="events" />
            <select class="sh-scope" name="scope" id="searchScope">
                <option value="">Any Scope</option>
                <?php
                foreach ($scopeList as $skey => $sname) {
                    echo '<option value="', $skey, '" ', $skey == $shScope ? 'selected' : '', '>', $sname, '</option>';
                }
                ?>
            </select>
            <?php
            KeySearch(
                'shkey',
                $shKey,
                'Search by events'
            );
            ?>
        </form>
    </div>
</div>

<div class="events-count">
    Total
    <strong>
        <?php echo $events->totalRows; ?>
    </strong>
    <?php echo ' event', $events->totalRows > 1 ? 's' : ''; ?>
</div>

<div class="events-list">
    <?php
    foreach ($events->data as $evt) {
    ?>
        <a href="<?php echo $pix->adminURL, '?page=events&sec=details&id=', $evt->id; ?>" class="evt-thumb">
            <span class="evnt-thumb">
                <?php
                if ($evt->image) {
                ?>
                    <img src="<?php echo $pix->uploadPath, 'events/', $pix->thumb($evt->image, '150x150'); ?>" class="cr-img">
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
            <span class="evnt-info">
                <span class="usr-name">
                    <?php echo $evt->name; ?>
                </span>
                <span class="evt-date">
                    <?php
                    echo date('j F Y', strtotime($evt->date));

                    if ($evt->enddate && $evt->enddate != $evt->date) {
                        echo ' - ', date('j F Y', strtotime($evt->enddate));
                    }
                    ?>
                </span>
                <span class="ev-sts">
                    <span class="sts">
                        <?php echo ucfirst($evt->scope); ?>
                    </span>
                    <span class="sts <?php echo $evt->enabled == 'Y' ? 'active' : 'inactive' ?>">
                        <?php
                        echo $evt->enabled == 'Y' ?
                            'Published' :
                            'Un-Published';
                        ?>
                    </span>
                </span>
            </span>
        </a>
    <?php
    }
    ?>
</div>
<?php
$pix->pagination(
    $events->pages,
    $pgn,
    5,
    null,
    'pt30 mb50 text-left'
);

if (!$events->pages) {
    NoResult(
        'No Events Found',
        'We couldn\'t find any results. Maybe try a new search.'
    );
}

StickyButton($pix->adminURL . '?page=events&sec=mod', 'add');
