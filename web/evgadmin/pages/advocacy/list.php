<?php
(function ($pix, $pixdb) {
    $pgn = max(0, intval($_GET['pgn'] ?? 0));
    $adConds = [
        '#SRT' => 'id desc',
        '__page' => $pgn,
        '__limit' => 24,
        '__QUERY__' => array()
    ];

    $adnlqry = [];

    //search by scope
    $shScope = esc($_GET['sh_scope'] ?? '');
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

    //search by title
    $shSearch = esc($_GET['sh_search'] ?? '');
    if ($shSearch) {
        $qSearch = q("%$shSearch%");
        $adnlqry[] = '(
            title like ' . $qSearch . ' OR 
            senator like ' . $qSearch . ' OR 
            recipient like ' . $qSearch . ' OR 
            recipAddr like ' . $qSearch . ' OR 
            descrptn like ' . $qSearch . '
        )';
    }

    if (!empty($adnlqry)) {
        $adConds['__QUERY__'][] = implode(' and ', $adnlqry);
    }
    $advocacies = $pixdb->get(
        'advocacies',
        $adConds,
        'id,
        title,
        scope,
        recipient,
        recipEmail,
        enabled,
        image'
    );

    $advScope = [
        'national' => 'National',
        'regional' => 'Regional',
        'state' => 'State',
        'chapter' => 'Section'
    ];
    $advStatus = [
        'active' => 'Active',
        'inactive' => 'Inactive'
    ];

    loadStyle('pages/advocacy/list');

?>
    <h1>Advocacy List</h1>
    <?php
    breadcrumbs(
        [
            'Advocacies'
        ]
    )
    ?>
    <div class="advocacy-hed">
        <div class="hed-left">
            <?php
            echo $advocacies->totalRows;
            ?>
            <?php
            echo ' Advocac', $advocacies->totalRows > 1 ? 'ies' : 'y';
            ?>
        </div>
        <div class="hed-right">
            <form method="get">
                <input type="hidden" name="page" value="advocacy" />
                <div class="scp-select">
                    <select name="sh_scope">
                        <option value="">
                            Any Scope
                        </option>
                        <?php
                        foreach ($advScope as $key => $val) {
                            echo '<option ', $shScope == $key ? 'selected' : '', ' value="', $key, '">', $val, '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
                KeySearch(
                    'sh_search',
                    $shSearch,
                    'Search by keywords'
                );
                ?>
            </form>
        </div>
    </div>
    <?php
    $pix->pagination(
        $advocacies->pages,
        $pgn,
        5,
        null,
        'pt30 mb50 text-left'
    );
    ?>
    <div class="advocacy-list">
        <?php
        foreach ($advocacies->data as $row) {
        ?>
            <a href="<?php echo $pix->adminURL, '?page=advocacy&sec=details&id=', $row->id; ?>" class="adv-card">
                <span class="adv-thumb">
                    <?php
                    if ($row->image) {
                        $row->image = $pix->uploadPath . 'advocacy-image/' . $pix->thumb(
                            $row->image,
                            '450x450'
                        );
                    ?>
                        <img src="<?php echo $row->image; ?>" alt="Advocacy-image" class="cr-img">
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
                <span class="adv-info">
                    <span class="adv-title">
                        <?php
                        echo $row->title;
                        ?>
                    </span>
                    <span class="adv-scope">
                        <?php
                        echo $row->scope;
                        ?>
                    </span>
                    <span class="adv-itm">
                        <?php
                        echo $row->recipient;
                        ?>
                    </span>
                    <span class="adv-itm">
                        <?php
                        echo $row->recipEmail
                        ?>
                    </span>
                    <?php
                    if ($row->enabled) {
                        $sts = $row->enabled == 'Y' ? 'active' : 'inactive';
                    ?>
                        <span class="adv-sts">
                            <span class="sts <?php echo $sts; ?>">
                                <?php
                                echo $advStatus[$sts];
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
        $advocacies->pages,
        $pgn,
        5,
        null,
        'pt30 mb50 text-left'
    );
    if (!$advocacies->pages) {
        NoResult(
            'No advocacy found',
            'We couldn\'t find any results. Maybe try a new search.'
        );
    }
    ?>
<?php
    StickyButton($pix->adminURL . '?page=advocacy&sec=mod&id=new', 'add');
})($pix, $pixdb);
?>