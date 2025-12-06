<?php
exit;
include '../../../lib/lib.php';

$remain = $pixdb->get(
    'test',
    [
        '#QRY' => 'progress=3',
        'memberId' => 59332,
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
            'test',
            [
                '#QRY' => 'progress=3',
                'memberId' => 59332
            ]
        );

        if ($mData->data) {
            $updateInfoStmt = $pdo->prepare("
                UPDATE members_info
                SET 
                    chptrOfInitn = :chptrOfInitn,
                    cruntChptr = :cruntChptr,
                    yearOfInitn = :yearOfInitn
                WHERE member = :member
            ");

            $srhMmbrshpStmt = $pdo->prepare("
                SELECT id, expiry 
                FROM memberships
                WHERE member = :member
                AND planId = :planId
                LIMIT 1
            ");

            $insertMmbrshpStmt = $pdo->prepare("
                INSERT INTO memberships
                    (member, planId, planName, created, expiry) 
                VALUES 
                    (:member, :planId, :planName, :created, :expiry)
            ");
            $updateMmbrshpStmt = $pdo->prepare("
                UPDATE memberships
                SET 
                    planName = :planName,
                    expiry = :expiry
                WHERE id = :id
            ");

            $chkStmt = $pdo->prepare("
                SELECT 'membership' AS type, id 
                FROM membership_plans 
                WHERE title like ?
                UNION
                SELECT 'chapter' AS type, id 
                FROM chapters 
                WHERE name like ?
                LIMIT 1
            ");

            $ackStmt = $pdo->prepare("
                UPDATE test
                SET 
                    progress = :progress
                WHERE id = :id
            ");

            foreach ($mData->data as $mbr) {
                if ($mbr->memberId) {
                    $others = $mbr->others;
                    // Split on "|||"
                    $parts = array_map('trim', explode('|||', $others));
                    $parts = array_values(array_filter($parts));
                    $result = [];
                    for ($i = 0; $i < count($parts); $i++) {
                        // detect title
                        if (!preg_match('/\d{2}-\d{2}-\d{4}/', $parts[$i])) {
                            $title = $parts[$i];
                            $start = null;
                            $end   = null;

                            // find start date
                            if (isset($parts[$i + 1]) && preg_match('/\d{2}-\d{2}-\d{4}/', $parts[$i + 1])) {
                                $start = $parts[$i + 1];
                                $i++;
                            }
                            // find end date
                            if (isset($parts[$i + 1]) && preg_match('/\d{2}-\d{2}-\d{4}/', $parts[$i + 1])) {
                                $end = $parts[$i + 1];
                                $i++;
                            }

                            $result[] = (object)[
                                'title' => $title,
                                'start' => $start,
                                'end'   => $end
                            ];
                        }
                    }
                    var_dump($result);
                    exit;
                    foreach ($result as $item) {
                        $start = null;
                        $end = null;

                        if ($item->start && preg_match('/\d{2}-\d{2}-\d{4}/', $item->start, $matches)) {
                            $date = DateTime::createFromFormat('d-m-Y', $matches[0]);
                            $start = $date ? $date->format('Y-m-d') : null;
                        }

                        if ($item->end && preg_match('/\d{2}-\d{2}-\d{4}/', $item->end, $matches)) {
                            $date = DateTime::createFromFormat('d-m-Y', $matches[0]);
                            $end = $date ? $date->format('Y-m-d') : null;
                        }
                        $search = "%" . $item->title . "%";
                        $chkStmt->execute([$search, $search]);
                        $match = $chkStmt->fetch(PDO::FETCH_OBJ);
                        if ($match) {
                            if ($match->type == 'membership') {
                                $srhMmbrshpStmt->execute([
                                    ':member' => $mbr->memberId,
                                    ':planId' => $match->id
                                ]);
                                $mmbrshpExist = $srhMmbrshpStmt->fetch(PDO::FETCH_OBJ);

                                if ($mmbrshpExist) {
                                    $existingExpiry = $mmbrshpExist->expiry;
                                    if ($existingExpiry && ($end == null || $existingExpiry < $end)) {
                                        $updateMmbrshpStmt->execute([
                                            ':planName' => $item->title,
                                            ':expiry' => $end,
                                            ':id' => $mmbrshpExist->id
                                        ]);
                                    }
                                } else {
                                    $insertMmbrshpStmt->execute([
                                        ':member' => $mbr->memberId,
                                        ':planId' => $match->id,
                                        ':planName' => $item->title,
                                        ':created' => $start,
                                        ':expiry' => $end
                                    ]);
                                }
                            } else {
                                $updateInfoStmt->execute([
                                    ':chptrOfInitn' => $match->id,
                                    ':cruntChptr' => $match->id,
                                    ':yearOfInitn' => $start,
                                    ':member' => $mbr->memberId
                                ]);
                            }
                        }
                    }
                    $ackStmt->execute([
                        ':progress' => 3,
                        ':id' => $mbr->id
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