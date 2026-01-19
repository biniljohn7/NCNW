<?php
if (
    $lgUser->verified == 'Y' &&
    isset(
        $_['txnId'],
        $_FILES['paymentProof']
    )
) {
    $txnId = esc($_['txnId']);
    $proof = $_FILES['paymentProof'];

    if ($txnId) {
        $txn = $pixdb->getRow(
            'transactions',
            [
                'txnid' => $txnId,
                'member' => $lgUser->id
            ],
            'method,
            offlineProof'
        );

        if ($txn && in_array($txn->method, ['check', 'moneyorder'])) {
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
                $fileBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseName);
                $fileName = $fileBase . '.' . $ext;

                $counter = 1;
                while (file_exists($dateDir->absdir . $fileName)) {
                    $fileName = sprintf('%s (%d).%s', $fileBase, $counter, $ext);
                    $counter++;
                }

                if (move_uploaded_file($proof['tmp_name'], $dateDir->absdir . $fileName)) {
                    $docRoot = $dateDir->uplroot . $fileName;
                    $attachments[] = [
                        'type' => 'doc',
                        'path' => $docRoot
                    ];
                }
            } else {
                $r->message = 'Invalid file type. Allowed types: pdf, jpeg, pgn, webp.';
            }
        } else {
            $r->message = 'Transaction not found or invalid payment method.';
        }
    }
}
