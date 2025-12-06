<?php
(function ($pix, $pixdb) {
    $pgn = max(0, intval($_GET['pgn'] ?? 0));
    $cConds = [
        '#SRT' => 'id desc',
        '__limit' => 20,
        '__page' => $pgn,
        '__QUERY__' => array()
    ];

    //Search by name
    $shSearch = esc($_GET['sh_page'] ?? '');
    if ($shSearch) {
        $qSearch = q("%$shSearch%");
        $cConds['__QUERY__'][] = '(cmsName like ' . $qSearch . ')';
    }

    $cData = $pixdb->get(
        'cms',
        $cConds,
        'id,
        cmsName,
        enabled,
        createdAt'
    );
    loadStyle('pages/cms/list');
?>
    <h1>
        CMS Pages
    </h1>
    <?php
    breadcrumbs(
        [
            'CMS Pages'
        ]
    )
    ?>
    <div class="cms-hed">
        <form action="" method="get">
            <input type="hidden" name="page" value="cms" />
            <?php
            KeySearch(
                'sh_page',
                $shSearch,
                'Search by name'
            );
            ?>
        </form>
    </div>
    <?php
    $pix->pagination(
        $cData->pages,
        $pgn,
        5,
        null,
        'pt30 mb50 text-left'
    );
    ?>
    <div class="cms-wrap">
        <div class="cms-card hed">
            <div class="list-item left">
                <div class="txt-left">
                    Name
                </div>
                <div class="txt-right">
                    Status
                </div>
            </div>
            <div class="list-item right">
                <div class="txt-left">
                    Created At
                </div>
                <div class="txt-right">
                    Actions
                </div>
            </div>
        </div>
        <?php
        foreach ($cData->data as $row) {
            $edtLink = $pix->adminURL . "?page=cms&sec=mod&id=$row->id";
            $dtlLink = $pix->adminURL . "?page=cms&sec=details&id=$row->id";
        ?>
            <div class="cms-card list">
                <div class="list-item left">
                    <div class="txt-left labels nm">
                        <?php
                        echo $row->cmsName;
                        ?>
                    </div>
                    <div class="txt-right">
                        <?php
                        if ($row->enabled) {
                            $sts = $row->enabled == 'Y' ? 'published' : 'unpublished';
                        ?>
                            <span class="sts <?php echo $sts; ?>">
                                <?php
                                echo ucwords($sts);
                                ?>
                            </span>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="list-item right">
                    <div class="txt-left labels dt">
                        <?php
                        echo date('F d, Y', strtotime($row->createdAt));
                        ?>
                    </div>
                    <div class="txt-right labels btns">
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
        ?>
    </div>
    <?php
    $pix->pagination(
        $cData->pages,
        $pgn,
        5,
        null,
        'pt30 mb50 text-left'
    );
    if (!$cData->pages) {
        NoResult(
            'No cms found',
            'We couldn\'t find any results. Maybe try a new search.'
        );
    }
    ?>
<?php
    StickyButton($pix->adminURL . '?page=cms&sec=mod&id=new', 'add');
})($pix, $pixdb);
?>