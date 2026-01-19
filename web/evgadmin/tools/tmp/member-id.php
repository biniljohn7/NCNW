<?php
include '../../lib/lib.php';

$remain = $pixdb->get(
    'members',
    [
        '#QRY' => 'memberId IS NULL',
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
            'members',
            [
                '#QRY' => 'memberId IS NULL',
                '__limit' => 50
            ]
        );

        if ($mData->data) {
            $updateStmt = $pdo->prepare("UPDATE members SET memberId = ? WHERE id = ?");

            foreach ($mData->data as $mbr) {
                $memberId = 'NC' . sprintf('%06d', $mbr->id);
                $updateStmt->execute([$memberId, $mbr->id]);
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