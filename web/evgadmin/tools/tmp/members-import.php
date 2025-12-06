<?php
include '../../lib/lib.php';
$remain = $pixdb->get(
    'zz_import_members',
    [
        '#QRY' => 'evgId IS NULL',
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
                '#QRY' => 'evgId IS NULL',
                '__limit' => 100
            ]
        );
        if ($mData->data) {;
            $checkStmt = $pdo->prepare("
                SELECT 
                    *
                FROM members
                WHERE 
                    concat(firstName, ' ', lastName) like :name OR (email like :email AND email NOT LIKE 'noreply-%')
                LIMIT 1;
            ");

            $insertStmt = $pdo->prepare("
                INSERT INTO members 
                    (firstName, lastName, email, memberId, regOn, verified) 
                VALUES 
                    (:firstName, :lastName, :email, :memberId, :regOn, :verified)
            ");

            $updateStmt = $pdo->prepare("
                UPDATE members
                SET 
                    firstName = :firstName,
                    lastName = :lastName,
                    email = :email,
                    memberId = :memberId,
                    verified = :verified,
                    enabled='Y'
                WHERE id = :id
            ");

            $ackStmt = $pdo->prepare("
                UPDATE zz_import_members
                SET 
                    evgId = :evgId
                WHERE id = :id
            ");


            foreach ($mData->data as $mbr) {
                // Prepare Data
                $verified = 'Y';
                $email = esc($mbr->email);
                if (!is_mail($email)) {
                    $verified = 'N';
                    $email = NULL;
                }
                $fName = esc($mbr->firstName);
                $lName = esc($mbr->lastName);
                $query = $checkStmt->execute([
                    ':email'         => $email ?? '',
                    ':name'     => "%$fName $lName%"
                ]);

                $isExist = $checkStmt->fetch(PDO::FETCH_OBJ);

                $mid = null;
                if ($isExist) {
                    $memberId = $isExist->memberId;
                    if (!preg_match('/^\d+$/', $isExist->memberId)) {
                        $memberId = NULL;
                        $maxMemberId = $pdo->query("SELECT MAX(memberId) as maxId FROM members WHERE memberId REGEXP '^[0-9]+$'")->fetch(PDO::FETCH_OBJ);
                        if ($maxMemberId && isset($maxMemberId->maxId)) {
                            $memberId = $maxMemberId->maxId + 1;
                        }
                    }
                    $memberData = [
                        ':firstName' => substr($fName, 0, 60),
                        ':lastName' => substr($lName, 0, 60),
                        ':email' => $email,
                        ':memberId' => $memberId ?? null,
                        ':verified' => $verified
                    ];
                    $mid = $isExist->id;
                    $memberData[':id'] = $mid;
                    $updateStmt->execute($memberData);
                } else {
                    $memberId = NULL;
                    $maxMemberId = $pdo->query("SELECT MAX(CAST(memberId AS UNSIGNED)) + 1 AS nextId
                        FROM members
                        WHERE memberId REGEXP '^[0-9]+$'")->fetch(PDO::FETCH_OBJ);
                    if ($maxMemberId && isset($maxMemberId->nextId)) {
                        $memberId = $maxMemberId->nextId;
                    }
                    $memberData = [
                        ':firstName' => substr($fName, 0, 60),
                        ':lastName' => substr($lName, 0, 60),
                        ':email' => $email,
                        ':memberId' => $memberId ?? null,
                        ':regOn' => date('Y-m-d'),
                        ':verified' => $verified
                    ];
                    $success = $insertStmt->execute($memberData);

                    if ($success) {
                        $mid = $pdo->lastInsertId();
                    }
                }
                if ($mid) {
                    $ackStmt->execute([
                        ':evgId' => $mid,
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