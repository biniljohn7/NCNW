<?php
$actionBanner = function (
    $text,
    $btnText,
    $link = false,
    $id = false,
    $classes = ''
) {
    $tag = $link ? 'a' : 'span';
?>
    <div class="action-banner">
        <div class="br-icon">
            <span class="material-symbols-outlined">
                apps_outage
            </span>
        </div>
        <div class="br-text">
            <?php echo $text; ?>
        </div>
        <div class="br-action">
            <<?php echo $tag,
                $link ? " href=\"$link\"" : '',
                $id ? " id=\"$id\"" : ''; ?> class="br-btn <?php echo $classes; ?>">
                <?php echo $btnText; ?>
            </<?php echo $tag; ?>>
        </div>
    </div>
<?php
};

if ($rData->status == 'pending') {
    if ($developer) {
        $actionBanner(
            'This task requires your attention. Please review the details and submit your estimate in response.',
            'Submit',
            false,
            false,
            'estimate-btn'
        );
    }
    // 
} elseif ($rData->status == 'estimated') {
    if ($cordinator) {
        $actionBanner(
            'An estimate has been submitted for your review. Kindly verify the details to ensure accuracy and alignment with your requirements.',
            'Verify',
            $pix->adminURL . 'actions/anyadmin/?method=helpdesk/set-status&status=est-verified&task=' . $rId,
            false,
            'confirm'
        );
    }
    // 
} elseif ($rData->status == 'est-verified') {
    if ($ncnwTeam) {
        $actionBanner(
            'The project coordinator(s) have reviewed and approved the submitted estimate. Please review it on your end and provide your approval to initiate the work.',
            'Approve',
            $pix->adminURL . 'actions/anyadmin/?method=helpdesk/set-status&status=approved&task=' . $rId,
            false,
            'confirm'
        );
    }
    // 
} elseif ($rData->status == 'approved') {
    if ($developer) {
        $actionBanner(
            'The NCNW Team has reviewed and approved this task. You may proceed with starting the work.',
            'Mark as Started',
            $pix->adminURL . 'actions/anyadmin/?method=helpdesk/set-status&status=started&task=' . $rId,
            false,
            'confirm'
        );
    }
    // 
} elseif ($rData->status == 'started') {
    if ($developer) {
        $actionBanner(
            'After deploying the changes, please mark the task as done upon completion to notify other team members.',
            'Mark as Deployed',
            $pix->adminURL . 'actions/anyadmin/?method=helpdesk/set-status&status=deployed&task=' . $rId,
            false,
            'confirm'
        );
    }
    // 
} elseif ($rData->status == 'deployed') {
    if ($ncnwTeam) {
        $actionBanner(
            'The development team has deployed the requested changes. Kindly review them and either close the task or inform the team if any amendments are needed in the latest deployment.',
            'Take Action',
            false,
            false,
            'feedback-deployment-btn'
        );
    }
    // 
} elseif ($rData->status == 'revise-update') {
    if ($developer) {
        $actionBanner(
            'The NCNW team has requested amendments to your last deployment. Please make the necessary changes or share your comments with them if needed.',
            'Take Action',
            false,
            false,
            'revise-req-resp-btn'
        );
    }
}
?>