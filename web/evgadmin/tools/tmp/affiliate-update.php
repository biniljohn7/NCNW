<?php
include '../../lib/lib.php';

$data = $pixdb->get(
    'affiliates',
    []
)->data;

$checkStmt = $pdo->prepare("
    SELECT * 
    FROM zz_import_affiliates 
    WHERE 
        name like :name
    LIMIT 1
");
foreach ($data as $rw) {
    $name = escape($rw->name);
    $checkStmt->execute([
        ':name'  => $name
    ]);

    $isExist = $checkStmt->fetch(PDO::FETCH_OBJ);
    if (!$isExist) {
        var_dump($rw);
    }
}
