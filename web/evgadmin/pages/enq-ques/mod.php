<?php
(function ($pix, $pixdb) {
    $nid = esc($_GET['id'] ?? 'new');
    $new = $nid == 'new';

    if (!$new) {
        $validQues = false;
        if ($nid) {
            $enqQues = $pixdb->get(
                'enquiry_questions',
                [
                    'id' => $nid,
                    'single' => 1
                ]
            );
            $validQues = !!$enqQues;
        }
        if (!$validQues) {
            $pix->addmsg('Unknown question');
            $pix->redirect('?page=enq-ques');
        }
    }
    loadScript('pages/enq-ques');
    loadStyle('pages/enq-ques/mod');

?>
    <h1 class="mb0">
        <?php echo $new ? 'Create' : 'Modify'; ?> question
    </h1>
    <?php
    breadcrumbs(
        [
            'Enquiry Questions',
            '?page=enq-ques'
        ],
        [
            $new ? 'Create' : 'Modify'
        ]
    );
    ?>
    <form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="quesSave">
        <input type="hidden" name="method" value="enq-questn-save" />
        <?php
        if (!$new) {
        ?>
            <input type="hidden" name="nid" value="<?php echo $nid; ?>" />
        <?php
        }
        ?>
        <div class="fm-field">
            <div class="fld-label">
                Question
            </div>
            <div class="fld-inp">
                <input type="text" size="35" name="ques" value="<?php echo $new ? '' : $enqQues->question; ?>" data-type="string">
            </div>
        </div>
        <div class="fm-field">
            <input type="submit" class="pix-btn md site bold-500" value="Submit">
        </div>
    </form>
<?php
})($pix, $pixdb);
