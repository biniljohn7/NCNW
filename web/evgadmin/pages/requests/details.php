<?php
(function (
    $pix,
    $pixdb,
    $evg,
    $lgUser,
    $HelpDesk
) {
    $rData = false;
    $dtlContDir = dirname(__FILE__) . '/details';

    if (isset($_GET['id'])) {
        $rId = esc($_GET['id']);
        if ($rId) {
            $rData = $pixdb->getRow(
                'help_desk',
                ['id' => $rId]
            );
        }
    }

    if (!$rData) {
        $pix->addmsg('Unknown request');
        $pix->redirect('?page=requests');
    }

    $pgData = (object)[
        'estimate' => (object)[
            'type' => $rData->estType,
            'time' => $rData->estTime,
        ]
    ];
    $reqFiles = $pixdb->get(
        'help_desk_files',
        [
            'request' => $rId,
            '#SRT' => 'name asc'
        ]
    );
    $raisedBy = $rData->raisedBy ? $evg->getAnyAdmin($rData->raisedBy, 'id, name, type') : false;
    $isAdmin = ($raisedBy->type ?? '') == 'admin';
    //
    $developer = $pix->canAccess('helpdesk/developer');
    $cordinator = $pix->canAccess('helpdesk/project-coordinators');
    $ncnwTeam = $pix->canAccess('helpdesk/ncnw-team');
    //
    $iconsReq = [
        'pending' => 'work_alert',
        'estimated' => 'price_check',
        'est-verified' => 'preliminary',
        'approved' => 'priority',
        'started' => 'play_arrow',
        'deployed' => 'deployed_code',
        'revise-update' => 'cycle',
        'resolved' => 'add_task',
    ];
    //
    loadStyle('pages/request/details');
    loadScript('pages/request/details');
?>
    <h1>
        Request Details
    </h1>
    <?php
    breadcrumbs(
        [
            'Requests',
            '?page=requests'
        ],
        [
            'Request Name'
        ]
    );
    ?>
    <div class="req-details">
        <div class="lt-col">
            <?php
            include $dtlContDir . '/action-banner.php';
            include $dtlContDir . '/details-body.php';
            include $dtlContDir . '/comments.php';
            ?>
        </div>
        <div class="rt-col">
            <?php
            include $dtlContDir . '/details-card-right.php';
            include $dtlContDir . '/action-cards.php';
            ?>
        </div>
    </div>
<?php
    echo '<script>
            var 
                reqId=', json_encode($rData->id), ',
                pgData=', json_encode($pgData), ';
        </script>';
})(
    $pix,
    $pixdb,
    $evg,
    $lgUser,
    $HelpDesk
);
?>