<?php
$pgn = max(0, intval($_GET['pgn'] ?? 0));
$pvConds = [
    '#SRT' => 'id desc',
    '__page' => $pgn,
    '__limit' => 20,
    '__QUERY__' => array()
];

//Search by name & email
$shSearch = esc($_GET['sh_search'] ?? '');
if ($shSearch) {
    $qSearch = q("%$shSearch%");
    $pvConds['__QUERY__'][] = '(
        name like ' . $qSearch . ' OR email like ' . $qSearch . '
    )';
}
$provider = $pixdb->get(
    'benefit_providers',
    $pvConds,
    'id,
    name,
    email,
    phone,
    logo,
    status'
);
loadStyle('pages/benefits/list');
?>
<h1>Provider List</h1>
<?php
breadcrumbs(
    [
        'Providers'
    ]
);
?>
<div class="benefits-hed">
    <div class="hed-left">
        <div class="list-count">
            <?php
            echo $provider->totalRows;
            ?>
            <?php
            echo ' Provider', $provider->totalRows > 1 ? 's' : '';
            ?>
        </div>
    </div>
    <div class="hed-right">
        <form action="" method="get">
            <input type="hidden" name="page" value="provider" />
            <?php
            KeySearch(
                'sh_search',
                $shSearch,
                'Search by Provider'
            );
            ?>
        </form>
    </div>
</div>
<div class="bnft-wrap">
    <div class="bnft-card hed">
        <div class="list-item left">
            <div class="txt-left img">
                Image
            </div>
            <div class="txt-right">
                Name
            </div>
        </div>
        <div class="list-item mid">
            <div class="txt-left">
                Email
            </div>
            <div class="txt-right">
                Phone Number
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
    if ($provider->pages > 0) {
        $pix->pagination(
            $provider->pages,
            $pgn,
            5,
            null,
            'pt30 mb50 text-left'
        );
        foreach ($provider->data as $row) {
            $edtLink = $pix->adminURL . "?page=provider&sec=mod&id=$row->id";
            $dtlLink = $pix->adminURL . "?page=provider&sec=details&id=$row->id";
    ?>
            <div class="bnft-card list">
                <div class="list-item left">
                    <div class="txt-left labels img">
                        <?php
                        if ($row->logo) {
                        ?>
                            <img src="<?php echo $pix->uploadPath, 'provider-logo/', $pix->thumb($row->logo, '150x150'); ?>">
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
                    </div>
                    <div class="txt-right labels nam">
                        <?php
                        echo $row->name;
                        ?>
                    </div>
                </div>
                <div class="list-item mid pv">
                    <div class="txt-left labels em">
                        <?php
                        echo $row->email;
                        ?>
                    </div>
                    <div class="txt-right labels ph">
                        <?php
                        echo $row->phone;
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
            $provider->pages,
            $pgn,
            5,
            null,
            'pt30 mb50 text-left'
        );
    } else {
        NoResult(
            'No benefits provider found',
            'We couldn\'t find any results. Maybe try a new search.'
        );
    }
    ?>
</div>
<?php
StickyButton($pix->adminURL . '?page=provider&sec=mod&id=new', 'add');
?>