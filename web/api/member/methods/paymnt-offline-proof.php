<?php
if (
    isset(
        $_['txnId'],
        $_['payNum'],
        $_FILES['paymentProof']
    )
) {
    $txnId = esc($_['txnId']);
    $payNum = esc($_['payNum']);
    $proof = $_FILES['paymentProof'];

    if ($txnId) {
        $txn = $pixdb->getRow(
            'transactions',
            [
                'txnid' => $txnId,
                'member' => $lgUser->id
            ],
            'id,
            member,
            method,
            offlineProof'
        );

        if ($txn && in_array($txn->method, ['check', 'moneyorder'])) {
            if (preg_match('/^[0-9]{4,10}$/', $payNum)) {
                $allowTypes = [
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (
                    preg_match('/\.(pdf|jpg|jpeg|png|webp)$/i', $proof['name']) &&
                    in_array($proof['type'], $allowTypes)
                ) {
                    $dateDir = $pix->setDateDir('offline-payments');
                    $baseName = pathinfo($proof['name'], PATHINFO_FILENAME);
                    $fileExt = strtolower(pathinfo($proof['name'], PATHINFO_EXTENSION));
                    $fileBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
                    $fileName = $fileBase . '.' . $fileExt;

                    $counter = 1;
                    while (file_exists($dateDir->absdir . $fileName)) {
                        $fileName = sprintf('%s (%d).%s', $fileBase, $counter, $fileExt);
                        $counter++;
                    }

                    if (move_uploaded_file($proof['tmp_name'], $dateDir->absdir . $fileName)) {
                        $docRoot = $dateDir->uplroot . $fileName;
                        $pixdb->update(
                            'transactions',
                            [
                                'txnid' => $txnId
                            ],
                            [
                                'offlineProof' => $docRoot,
                                'offlinePayNum' => $payNum
                            ]
                        );

                        // remove old proof file
                        if ($txn->offlineProof) {
                            $pix->removeFile(
                                $pix->uploads . 'offline-payments/' . $txn->offlineProof
                            );
                        }

                        // notify admin
                        $evg->postNotification(
                            'admin',
                            $txn->member,
                            'new-payment',
                            'New Payment',
                            'Member has uploaded a payment proof for review.',
                            ['id' => $txn->id]
                        );

                        $r->success = 1;
                        $r->status = 'ok';
                        $r->data = [
                            'fileName' => basename($docRoot),
                            'fileUrl' => $pix->uploadPath . '/offline-payments/' . $docRoot
                        ];
                    }
                } else {
                    $r->message = 'Invalid file type. Allowed types: pdf, jpeg, pgn, webp.';
                }
            } else {
                $r->message = 'Invalid payment number format.';
            }
        } else {
            $r->message = 'Transaction not found or invalid payment method.';
        }
    }
}
