<?php
include '../../lib/lib.php';

$remain = $pixdb->get(
    'memberships',
    [
        '#QRY' => "expiry>NOW()",
        'enabled' => 'N',
        'single' => 1
    ],
    'count(id) cnt'
);

if (
    $remain &&
    isset($remain->cnt)
) {
    if ($remain->cnt > 0) {
        $mData = $pixdb->get(
            'memberships',
            [
                '#QRY' => "expiry>NOW()",
                'enabled' => 'N',
                '__limit' => 100
            ]
        );

        if ($mData->data) {
            $updateStmt = $pdo->prepare("
                UPDATE memberships
                SET 
                    enabled = 'Y'
                WHERE id = :id
            ");

            foreach ($mData->data as $mbrshp) {
                if ($mbrshp->expiry > $date) {
                    $res = $updateStmt->execute([':id' => $mbrshp->id]);
                }
            }

            echo ($remain->cnt - count($mData->data)) . ' items to complete';
        }
        ////
    } else {
        echo 'Complete';
        exit;
    }
}
?>


<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="0">
</head>

<body>
</body>

</html>