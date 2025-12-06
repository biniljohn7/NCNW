<?php
exit;
include '../../../lib/lib.php';

$remain = $pixdb->get(
    'test',
    [
        '#QRY' => 'progress=0',
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
                '#QRY' => 'progress=0',
                '__limit' => 100
            ]
        );

        if ($mData->data) {;
            $checkStmt = $pdo->prepare("
                SELECT * 
                FROM members 
                WHERE 
                    (email <> '' AND email = :email)
                    OR (CONCAT(firstName, ' ', lastName) = :fullName)
                    OR (memberId = :donorPerfectID)
                LIMIT 1
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
                    regOn = :regOn,
                    verified = :verified
                WHERE id = :id
            ");

            $ackStmt = $pdo->prepare("
                UPDATE test
                SET 
                    memberId = :memberId,
                    progress = :progress
                WHERE id = :id
            ");


            foreach ($mData->data as $mbr) {
                // Prepare Data
                $verified = 'Y';
                $email = esc($mbr->email);
                if (!is_mail($email)) {
                    $verified = 'N';
                    $email = 'noreply-' . substr(str2url($mbr->firstName . $mbr->lastName), 0, 15) . '-' . $pix->makestring(10, 'ln') . '@ncnw.org';
                }
                $fName = esc($mbr->firstName);
                $lName = esc($mbr->lastName);
                $memberId = esc($mbr->donorPerfectID);
                $checkStmt->execute([
                    ':email'         => $email ?? '',
                    ':fullName'     => "$fName $lName",
                    ':donorPerfectID' => $memberId ?? ''
                ]);

                $isExist = $checkStmt->fetch(PDO::FETCH_OBJ);

                $regOn = null;
                // Try createdOn first
                if (!empty($mbr->createdOn)) {
                    if (is_numeric($mbr->createdOn)) {
                        // Treat as Excel serial
                        $excelStart = new DateTime('1899-12-30');
                        $excelStart->modify("+{$mbr->createdOn} days");
                        $regOn = $excelStart->format('Y-m-d');
                    } else {
                        // Try parse string date (d-m-Y H.i)
                        $date = DateTime::createFromFormat('d-m-Y H.i', $mbr->createdOn);
                        if ($date !== false) {
                            $regOn = $date->format('Y-m-d');
                        }
                    }
                }
                // If still empty, try updatedOn
                if (empty($regOn) && !empty($mbr->updatedOn)) {
                    if (is_numeric($mbr->updatedOn)) {
                        $excelStart = new DateTime('1899-12-30');
                        $excelStart->modify("+{$mbr->updatedOn} days");
                        $regOn = $excelStart->format('Y-m-d');
                    } else {
                        $date = DateTime::createFromFormat('d-m-Y H.i', $mbr->updatedOn);
                        if ($date !== false) {
                            $regOn = $date->format('Y-m-d');
                        }
                    }
                }
                // Fallback
                if (!($regOn)) {
                    $regOn = date('Y-m-d');
                }


                $mid = null;

                $memberData = [
                    ':firstName' => substr($fName, 0, 60),
                    ':lastName' => substr($lName, 0, 60),
                    ':email' => $email,
                    ':memberId' => $memberId ?? null,
                    ':regOn' => $regOn,
                    ':verified' => $verified
                ];
                if ($isExist) {
                    $mid = $isExist->id;
                    $memberData[':id'] = $mid;
                    $updateStmt->execute($memberData);
                } else {

                    $success = $insertStmt->execute($memberData);

                    if ($success) {
                        $mid = $pdo->lastInsertId();
                    }
                }
                if ($mid) {
                    $ackStmt->execute([
                        ':memberId' => $mid,
                        ':progress' => 1,
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