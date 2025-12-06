<?php
(function ($pix, $pixdb, $evg) {
    $trData = false;
    if (isset($_GET['id'])) {
        $tid = esc($_GET['id']);
        if ($tid) {
            $trData = $pixdb->getRow(
                'transactions',
                [
                    'id' => $tid
                ]
            );
        }
    }
    if (!$trData) {
        $pix->addmsg('Unknown transaction');
        $pix->redirect('?page=transaction');
    }

    $memeberData = $trData->member ?
        $evg->getMember($trData->member, 'id,email,firstName,lastName,memberId') :
        false;

    loadStyle('pages/transactions/details');
    loadScript('pages/transactions/details');
?>
    <h1>Transactions Details</h1>
    <?php
    breadcrumbs(
        [
            'Transactions',
            '?page=transactions'
        ],
        [
            'Details'
        ]
    );
    if ($trData->posDoneBy) {
        include $pix->basedir . 'pages/transactions/details/pos.php';
    } else {
        include $pix->basedir . 'pages/transactions/details/info.php';
    }
    ?>
<?php
})($pix, $pixdb, $evg);
?>