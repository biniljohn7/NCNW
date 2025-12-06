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
            $affSrhStmt = $pdo->prepare("
                SELECT id 
                FROM affiliates 
                WHERE 
                    (name like :name)
                LIMIT 1
            ");
            // $chapterSrhStmt = $pdo->prepare("
            //     SELECT id 
            //     FROM chapters 
            //     WHERE
            //         name like :name
            //     LIMIT 1
            // ");

            // $insertStmt = $pdo->prepare("
            //     INSERT INTO members_info
            //         (member, cruntChptr) 
            //     VALUES 
            //         (:member, :cruntChptr)
            //     ON DUPLICATE KEY UPDATE
            //         cruntChptr   = VALUES(cruntChptr)
            // ");

            $insertAffStmt = $pdo->prepare("
                INSERT INTO members_affiliation
                    (member, affiliation) 
                VALUES 
                    (:member, :affiliation)
            ");

            $ackStmt = $pdo->prepare("
                UPDATE zz_import_members
                SET 
                    infoSync = 'Y'
                WHERE evgId = :id
            ");


            foreach ($mData->data as $mbr) {
                if ($mbr->evgId) {
                    $pixdb->delete(
                        'members_affiliation',
                        ['member' => $mbr->evgId]
                    );
                    $affiliateId = null;
                    if ($mbr->affiliate) {
                        $affSrhStmt->execute([
                            ':name' => "$mbr->affiliate%"
                        ]);
                        $getAff = $affSrhStmt->fetch(PDO::FETCH_OBJ);
                        if ($getAff) {
                            $affiliateId = $getAff->id;
                        }
                    }
                    // $chapterId = null;
                    // var_dump($mbr->section);
                    // if ($mbr->section) {
                    //     $chapterSrhStmt->execute([
                    //         ':name' => $mbr->section
                    //     ]);
                    //     $getChapter = $chapterSrhStmt->fetch(PDO::FETCH_OBJ);
                    //     if ($getChapter) {
                    //         $chapterId = $getChapter->id;
                    //     }
                    // }
                    // var_dump($mbr->evgId, $chapterId);
                    // $res = $insertStmt->execute([
                    //     ':member' => $mbr->evgId,
                    //     ':cruntChptr' => $chapterId
                    // ]);

                    // $insertStmt->execute([
                    //     ':member' => $mbr->evgId,
                    //     ':cruntChptr' => $chapterId
                    // ]);

                    if ($affiliateId) {
                        $insertAffStmt->execute([
                            ':member' => $mbr->evgId,
                            ':affiliation' => $affiliateId
                        ]);
                    }

                    // //membership
                    // $mbr->membership = esc($mbr->membership);
                    // if ($mbr->membership) {
                    //     $data = [
                    //         'member'  => $mbr->evgId,
                    //         'enabled' => 'Y',
                    //         'created' => NULL
                    //     ];

                    //     if (preg_match('/legacy/i', $mbr->membership)) {
                    //         // Legacy Life Membership
                    //         $data['planId']   = 3;
                    //         $data['planName'] = 'Legacy Life Membership';
                    //         $data['amount']   = 1500.00;

                    //         if (!empty($mbr->mmbrshpExp)) {
                    //             $data['expiry'] = date('Y-m-d', strtotime($mbr->mmbrshpExp));
                    //             if (strtotime($data['expiry']) < time()) {
                    //                 $data['enabled'] = 'N';
                    //             }
                    //         } else {
                    //             $data['pinStatus'] = 'pending';
                    //             $data['payStatus'] = 'completed';
                    //         }
                    //     } elseif (preg_match('/life/i', $mbr->membership)) {
                    //         // Life Membership
                    //         $data['planId']   = 2;
                    //         $data['planName'] = 'Life Membership';
                    //         $data['amount']   = 1000.00;

                    //         if (!empty($mbr->mmbrshpExp)) {
                    //             $data['expiry'] = date('Y-m-d', strtotime($mbr->mmbrshpExp));
                    //             if (strtotime($data['expiry']) < time()) {
                    //                 $data['enabled'] = 'N';
                    //             }
                    //         } else {
                    //             $data['pinStatus'] = 'pending';
                    //             $data['payStatus'] = 'completed';
                    //         }
                    //     } elseif (preg_match('/national/i', $mbr->membership)) {
                    //         // National Membership
                    //         $data['planId']   = 1;
                    //         $data['planName'] = 'National Membership';
                    //         $data['amount']   = 75.00;

                    //         if (!empty($mbr->mmbrshpExp)) {
                    //             $data['expiry'] = date('Y-m-d', strtotime($mbr->mmbrshpExp));
                    //             if (strtotime($data['expiry']) < time()) {
                    //                 $data['enabled'] = 'N';
                    //             }
                    //         } else {
                    //             $data['payStatus'] = 'completed';
                    //         }
                    //     } elseif (preg_match('/collegiate/i', $mbr->membership)) {
                    //         // Collegiate Membership
                    //         $data['planId']   = 4;
                    //         $data['planName'] = 'Collegiate Membership';
                    //         $data['amount']   = 25.00;

                    //         if (!empty($mbr->mmbrshpExp)) {
                    //             $data['expiry'] = date('Y-m-d', strtotime($mbr->mmbrshpExp));
                    //             if (strtotime($data['expiry']) < time()) {
                    //                 $data['enabled'] = 'N';
                    //             }
                    //         } else {
                    //             $data['payStatus'] = 'completed';
                    //         }
                    //     } elseif (preg_match('/youth/i', $mbr->membership)) {
                    //         // Youth Membership
                    //         $data['planId']   = 5;
                    //         $data['planName'] = 'Youth Membership';
                    //         $data['amount']   = 10.00;

                    //         if (!empty($mbr->mmbrshpExp)) {
                    //             $data['expiry'] = date('Y-m-d', strtotime($mbr->mmbrshpExp));
                    //             if (strtotime($data['expiry']) < time()) {
                    //                 $data['enabled'] = 'N';
                    //             }
                    //         } else {
                    //             $data['payStatus'] = 'completed';
                    //         }
                    //     } else {
                    //         $data['planName'] = $mbr->membership;
                    //         $data['amount']   = 0;

                    //         if (!empty($mbr->mmbrshpExp)) {
                    //             $data['expiry'] = date('Y-m-d', strtotime($mbr->mmbrshpExp));
                    //             if (strtotime($data['expiry']) < time()) {
                    //                 $data['enabled'] = 'N';
                    //             }
                    //         } else {
                    //             $data['payStatus'] = 'completed';
                    //         }
                    //     }
                    //     $pixdb->delete('memberships', ['member' => $mbr->evgId]);
                    //     $pixdb->insert('memberships', $data);
                    // }
                    $ackStmt->execute([
                        ':id' => $mbr->evgId
                    ]);
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