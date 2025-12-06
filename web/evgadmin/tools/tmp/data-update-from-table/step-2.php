<?php
exit;
include '../../../lib/lib.php';

$remain = $pixdb->get(
    'test',
    [
        '#QRY' => 'progress=1',
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
                '#QRY' => 'progress=1',
                '__limit' => 100
            ]
        );

        if ($mData->data) {
            $countrySrhStmt = $pdo->prepare("
                SELECT id 
                FROM nations 
                WHERE 
                    (name like :name)
                    OR (code like :code)
                LIMIT 1
            ");
            $stateSrhStmt = $pdo->prepare("
                SELECT id 
                FROM states 
                WHERE
                    name like :name
                LIMIT 1
            ");

            $insertStmt = $pdo->prepare("
                INSERT INTO members_info
                    (member, prefix, country, state, city, address, address2, zipcode, phone) 
                VALUES 
                    (:member, :prefix, :country, :state, :city, :address, :address2, :zipcode, :phone)
                ON DUPLICATE KEY UPDATE
                    prefix   = VALUES(prefix),
                    country  = VALUES(country),
                    state    = VALUES(state),
                    city     = VALUES(city),
                    address  = VALUES(address),
                    address2 = VALUES(address2),
                    zipcode  = VALUES(zipcode),
                    phone    = VALUES(phone)
            ");

            $ackStmt = $pdo->prepare("
                UPDATE test
                SET 
                    progress = :progress
                WHERE id = :id
            ");


            foreach ($mData->data as $mbr) {
                if ($mbr->memberId) {
                    $zip = preg_match('/^\d{5}(-\d{4})?$/', $mbr->zip ?? '') ? $mbr->zip : null;

                    $phone = null;
                    $digits = preg_replace('/\D/', '', $mbr->phone);
                    if (strlen($digits) === 11 && $digits[0] === '1') {
                        $digits = substr($digits, 1);
                    }
                    if (preg_match('/^[2-9][0-9]{9}$/', $digits)) {
                        $phone = $digits;
                    }
                    $countryId = null;
                    if ($mbr->country) {
                        $countrySrhStmt->execute([
                            ':name' => $mbr->country,
                            ':code' => $mbr->country
                        ]);
                        $getCountry = $countrySrhStmt->fetch(PDO::FETCH_OBJ);
                        if ($getCountry) {
                            $countryId = $getCountry->id;
                        }
                    }
                    $stateId = null;
                    if ($mbr->state) {
                        $stateSrhStmt->execute([
                            ':name' => $mbr->state
                        ]);
                        $getState = $stateSrhStmt->fetch(PDO::FETCH_OBJ);
                        if ($getState) {
                            $stateId = $getState->id;
                        }
                    }
                    $prefix = null;
                    if ($mbr->billingTitle) {
                        $OPTIONS = $pix->PROFILE_OPTIONS;
                        foreach ($OPTIONS['prefix'] as $idx => $pfx) {
                            if ($pfx == ucwords($mbr->billingTitle)) {
                                $prefix = $idx;
                            }
                        }
                    }

                    $info = [
                        ':member'        => $mbr->memberId,
                        ':prefix'        => $prefix ?? null,
                        ':country'       => $countryId ?? null,
                        ':state'         => $stateId ?? null,
                        ':city'          => $mbr->city ? trim($mbr->city) : null,
                        ':address'       => $mbr->address ? trim($mbr->address) : null,
                        ':address2'      => $mbr->address2 ? trim($mbr->address2) : null,
                        ':zipcode'       => $zip,
                        ':phone'         => $phone
                    ];

                    $success = $insertStmt->execute($info);

                    if ($success) {
                        $ackStmt->execute([
                            ':progress' => 2,
                            ':id' => $mbr->id
                        ]);
                    } else {
                        var_dump($info);
                    }
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