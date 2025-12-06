<?php
include '../../lib/lib.php';

$remain = $pixdb->get(
    'zz_import_members',
    [
        'infoSync' => 'N',
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
            'zz_import_members',
            [
                'infoSync' => 'N',
                '__limit' => 100
            ]
        );

        if ($mData->data) {
            $updateStmt = $pdo->prepare("
                UPDATE members_info
                SET 
                    state = :state
                WHERE id = :id
            ");

            $ackStmt = $pdo->prepare("
                UPDATE zz_import_members
                SET 
                    infoSync = 'Y'
                WHERE evgId = :id
            ");


            foreach ($mData->data as $mbr) {
                if ($mbr->evgId) {
                    $state = NULL;
                    $country = NULL;
                    if ($mbr->state) {
                        $state = $evg->getStateId($mbr->state);
                    }
                    var_dump($state);
                    // $updateStmt->execute([
                    //     ':state' => $state,
                    //     ':id' => $mbr->memberId
                    // ]);
                    // $ackStmt->execute([
                    //     ':id' => $mbr->evgId
                    // ]);
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