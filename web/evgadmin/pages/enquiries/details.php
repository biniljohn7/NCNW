<?php
(function ($pix, $pixdb) {
    $enqData = false;
    if (isset($_GET['id'])) {
        $enqId = esc($_GET['id']);
        if ($enqId) {
            $enqData = $pixdb->get(
                'enquiries',
                [
                    'id' => $enqId,
                    'single' => 1
                ]
            );
        }
    }
    if (!$enqData) {
        $pix->addmsg('Unknown enquiry');
        $pix->redirect('?page=enquiries');
    }

    loadScript('pages/enquiries');
    loadStyle('pages/enquiries/details');

    if ($enqData->read == 'N') {
        $pixdb->update(
            'enquiries',
            [
                'id' => $enqId
            ],
            [
                'read' => 'Y'
            ]
        );
    }

    if ($enqData->memberId) {
        $member = $pixdb->get(
            'members',
            [
                'id' => $enqData->memberId,
                'single' => 1
            ],
            'email'
        );
        if ($member) {
            $enqData->email = $member->email;
        }
    }
?>
    <h1 class="mb0">Enquiry Details</h1>
    <?php
    breadcrumbs(
        [
            'Enquiries',
            '?page=enquiries'
        ],
        [
            'details'
        ]
    )
    ?>
    <div class="enq-details">
        <div class="row-fr">
            <div class="col-fr">
                <div class="avatar material-symbols-outlined">
                    user_attributes
                </div>
                <div class="usr-attr">
                    <div class="text-600 text-11">
                        <?php echo $enqData->name; ?>
                    </div>
                    <div class="">
                        <?php echo $enqData->email; ?>
                    </div>
                </div>
            </div>
            <div class="col-sc">
                <div class="txt-09">
                    <?php
                    echo date('d F Y, h:ia', strtotime($enqData->date));
                    ?>
                </div>
            </div>
        </div>
        <?php
        if ($enqData->subject) {
        ?>
            <div class="enq-sub">
                <div class="col-fr">
                    Subject
                </div>
                <div class="col-sc">
                    <?php echo $enqData->subject; ?>
                </div>
            </div>
        <?php
        }
        if ($enqData->question) {
        ?>
            <div class="enq-que">
                <div class="col-fr">
                    Question
                </div>
                <div class="col-sc">
                    <?php echo $enqData->question; ?>
                </div>
            </div>
        <?php
        }
        ?>
        <div class="enquiry">
            <?php echo $enqData->description; ?>
        </div>
        <div class="text-right">
            <a href="<?php echo $pix->adminURL . "actions/anyadmin/?method=enquiry-delete&id=$enqData->id"; ?>" class="enq-dlt">
                <span class="btn dlt confirm">
                    <span class="material-symbols-outlined icn">
                        delete
                    </span>
                    <span>
                        Delete
                    </span>
                </span>
            </a>
        </div>
    </div>
<?php
})($pix, $pixdb);
?>