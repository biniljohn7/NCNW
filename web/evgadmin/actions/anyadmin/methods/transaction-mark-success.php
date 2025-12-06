<?php
if (!$pix->canAccess('transactions')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['trnId']
    )
) {
    $trnId = esc($_['trnId']);
    $refnumber = isset($_['refnumber']) ? esc($_['refnumber']) : '';

    if (
        $trnId
    ) {
        $txn = $pixdb->getRow(
            'transactions',
            [
                'id' => $trnId
            ]
        );

        if (
            $txn &&
            $txn->status != 'success'
        ) {
            $mark = $evg->markPaymentDone(
                $txn,
                $datetime,
                $refnumber
            );

            if ($mark->marked) {
                $pix->addmsg('Transaction successfully marked as a success', 1);
            }
        } else {
            $pix->addmsg('Invalid or unknown transaction', 0);
        }
    }
}
// exit;
