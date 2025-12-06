<?php
(function ($pix, $pixdb) {
    $questions = $pixdb->get(
        'enquiry_questions'
    )->data;

    loadStyle('pages/enq-ques/list');
    loadScript('pages/enq-ques');

    StickyButton($pix->adminURL . '?page=enq-ques&sec=mod&id=new', 'add');
?>
    <h1>Enquiry Questions</h1>
    <?php
    breadcrumbs(
        [
            'Enquiry Questions'
        ]
    );

    foreach ($questions as $row) {
    ?>
        <div class="ques">
            <div>
                <?php echo $row->question; ?>
            </div>
            <div class="actions">
                <a href="<?php echo $pix->adminURL . '?page=enq-ques&sec=mod&id=', $row->id; ?>" class="mr5">
                    <span class="material-symbols-outlined icn">
                        edit
                    </span>
                </a>
                <a href="<?php echo $pix->adminURL . "actions/anyadmin/?method=enq-ques-delete&id=$row->id"; ?>" class="dlt-btn">
                    <span class="material-symbols-outlined icn">
                        delete
                    </span>
                </a>
            </div>
        </div>
<?php
    }
})($pix, $pixdb);
