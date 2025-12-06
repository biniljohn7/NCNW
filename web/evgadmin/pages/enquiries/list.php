<?php
(function ($pix, $pixdb) {
    $pgn = max(0, intval($_GET['pgn'] ?? 0));
    $enqCnds = [
        '#SRT' => 'CASE WHEN `read`="N" THEN 1 ELSE 0 END desc, id desc',
        '__page' => $pgn,
        '__limit' => 20,
        '__QUERY__' => array()
    ];
    $adnlqry = [];

    //Search by name or subject
    $shSearch =  esc($_GET['sh_search'] ?? '');
    if ($shSearch) {
        $qshSearch = q("%$shSearch%");
        $adnlqry[] = '(
        name like ' . $qshSearch . ' OR 
        subject like ' . $qshSearch . '
    )';
    }

    if (!empty($adnlqry)) {
        $enqCnds['__QUERY__'][] = implode(' and ', $adnlqry);
    }
    $enquiries = $pixdb->get(
        'enquiries',
        $enqCnds
    );

    loadScript('pages/enquiries');
    loadStyle('pages/enquiries/list');
?>
    <h1>Inquiries</h1>
    <?php
    breadcrumbs(
        [
            'Inquiries'
        ]
    );
    ?>
    <div class="enquiries-hed">
        <div class="hed-left">
            <div class="list-count">
                <?php echo $enquiries->totalRows; ?>
                <?php
                echo ' Enquir', $enquiries->totalRows > 1 ? 'ies' : 'y';
                ?>
            </div>
        </div>
        <div class="hed-right">
            <form action="" method="get">
                <input type="hidden" name="page" value="enquiries">
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
    <div class="enq-wrap">
        <div class="enq-card hed">
            <div class="list-item left">
                <div class="txt-left">
                    Name
                </div>
                <div class="txt-right">
                    subject
                </div>
            </div>
            <div class="list-item mid">
                message
            </div>
            <div class="list-item right">
                <div class="txt-left">
                    Date
                </div>
                <div class="txt-right">
                    Actions
                </div>
            </div>
        </div>
        <?php
        if ($enquiries->pages > 0) {
            $pix->pagination(
                $enquiries->pages,
                $pgn,
                5,
                null,
                'pt30 mb50 text-left'
            );
            foreach ($enquiries->data as $row) {
        ?>
                <div class="enq-card list <?php echo $row->read == 'N' ? 'unread' : ''; ?>">
                    <div class="list-item left">
                        <div class="txt-left labels nm">
                            <?php
                            echo $row->name ?? '--';
                            ?>
                        </div>
                        <div class="txt-right labels ct">
                            <?php
                            echo $row->subject ?? '--';
                            ?>
                        </div>
                    </div>
                    <div class="list-item mid">
                        <?php
                        echo substr($row->description, 0, 50), strlen($row->description) > 50 ? '...' : '';
                        ?>
                    </div>
                    <div class="list-item right">
                        <div class="txt-left">
                            <?php
                            echo date('d F Y h:ia', strtotime($row->date));
                            ?>
                        </div>
                        <div class="txt-right">
                            <a href="<?php echo $pix->adminURL . "?page=enquiries&sec=details&id=$row->id"; ?>" class="mr5">
                                <span class="material-symbols-outlined icn">
                                    visibility
                                </span>
                            </a>
                            <a href="<?php echo $pix->adminURL . "actions/anyadmin/?method=enquiry-delete&id=$row->id"; ?>" class="enq-dlt">
                                <span class="material-symbols-outlined icn">
                                    delete
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
        <?php
            }
            $pix->pagination(
                $enquiries->pages,
                $pgn,
                5,
                null,
                'pt30 mb50 text-left'
            );
        } else {
            NoResult(
                'No inquiries found',
                'We couldn\'t find any results. Maybe try a new search.'
            );
        }
        ?>
    </div>
<?php

})($pix, $pixdb);
