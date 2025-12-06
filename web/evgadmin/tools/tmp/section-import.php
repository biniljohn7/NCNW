<?php
include '../../lib/lib.php';

$data = $pixdb->get(
    'chapters',
    []
)->data;

$checkStmt = $pdo->prepare("
    SELECT * 
    FROM zz_import_chapters 
    WHERE 
        name like :name
    LIMIT 1
");
$i = 0;
foreach ($data as $rw) {
    $name = escape($rw->name);
    $checkStmt->execute([
        ':name'  => $name
    ]);

    $isExist = $checkStmt->fetch(PDO::FETCH_OBJ);
    if (!$isExist) {
        var_dump($rw->id, $rw->name);
        $i++;
    }
}
var_dump($i);

// $_ = $_REQUEST;
// if (isset($_['action'], $_['refer'])) {
//     $action = esc($_['action']);
//     $refer = esc($_['refer']);
//     $evgId = NULL;

//     if ($refer) {
//         $data = $pixdb->getRow(
//             'zz_import_chapters',
//             ['id' => $refer]
//         );

//         if ($data) {
//             $dbData = [
//                 'nation' => $data->nation ?? NULL,
//                 'region' => $data->region ?? NULL,
//                 'state' => $data->state ?? NULL,
//                 'name' => $data->name ?? NULL,
//                 'createdAt' => $data->createdAt ?? NULL,
//                 'enabled' => 'Y',
//                 'ein' => $data->ein ?? NULL,
//                 'secId' => $data->secId ?? NULL,
//                 'sectionTypes' => $data->sectionTypes ?? NULL,
//                 'addressLineOne' => $data->addressLineOne ?? NULL,
//                 'addressLineTwo' => $data->addressLineTwo ?? NULL,
//                 'city' => $data->city ?? NULL,
//                 'zipCode' => $data->zipCode ?? NULL
//             ];

//             if ($action == 'update') {
//                 if (isset($_['chapter'])) {
//                     $chapter = esc($_['chapter']);
//                     $exist = $pixdb->getRow(
//                         'chapters',
//                         ['id' => $chapter],
//                         'id'
//                     );
//                     if ($exist) {
//                         $pixdb->update(
//                             'chapters',
//                             ['id' => $chapter],
//                             $dbData
//                         );
//                         $evgId = $chapter;
//                     }
//                 }
//             } else {
//                 $evgId = $pixdb->insert(
//                     'chapters',
//                     $dbData
//                 );
//             }
//         }
//     }
//     if ($evgId) {
//         $pixdb->update(
//             'zz_import_chapters',
//             [
//                 'evgId' => $evgId
//             ],
//             ['id' => $refer]
//         );
//     }
// }

// // 👇 Normal execution (initial comparison logic)
// $data = $pixdb->get('zz_import_chapters', ['#QRY' => 'evgId IS NULL'])->data;

// $checkStmt = $pdo->prepare("
//     SELECT * 
//     FROM chapters
//     WHERE name LIKE :name
//     LIMIT 1
// ");

// $i = 0;
// foreach ($data as $rw) {
//     $name = $rw->name;
//     $checkStmt->execute([':name' => $name]);
//     $isExist = $checkStmt->fetch(PDO::FETCH_OBJ);

//     if (!$isExist) {
//         $words = explode(' ', $name);
//         $conditions = [];

//         $conditions[] = "IF(name LIKE '$name%', 1, 0)";
//         foreach ($words as $word) {
//             $conditions[] = "IF(name LIKE '$word%', 1, 0)";
//         }

//         $sql = "SELECT *, (" . implode(' + ', $conditions) . ") AS score, id
//                 FROM chapters
//                 HAVING score > 1
//                 ORDER BY score DESC
//                 LIMIT 5";

//         $stmt = $pdo->query($sql);
//         $matches = $stmt ? $stmt->fetchAll(PDO::FETCH_OBJ) : [];

//         if (!empty($matches)) {
//             echo "<form method='POST'>";
//             echo "<p>Possible match for: <strong>{$name}</strong></p>";

//             // Dropdown for chapter matches
//             echo "<label>Match found: </label>";
//             echo "<select name='chapter'>";
//             foreach ($matches as $m) {
//                 echo "<option value='{$m->id}'>{$m->name} (score: {$m->score})</option>";
//             }
//             echo "</select><br><br>";

//             echo "<label>Action: </label>";
//             echo "<select name='action' required>";
//             echo "<option value='insert'>Insert as new chapter</option>";
//             echo "<option value='update'>Update existing chapter</option>";
//             echo "</select><br><br>";

//             echo "<input type='hidden' name='refer' value='{$rw->id}'>";
//             echo "<button type='submit'> Confirm Action</button>";
//             echo "</form><hr>";
//         } else {
//             $pixdb->insert(
//                 'chapters',
//                 [
//                     'nation' => $rw->nation ?? NULL,
//                     'region' => $rw->region ?? NULL,
//                     'state' => $rw->state ?? NULL,
//                     'name' => $rw->name ?? NULL,
//                     'createdAt' => $rw->createdAt ?? NULL,
//                     'enabled' => 'Y',
//                     'ein' => $rw->ein ?? NULL,
//                     'secId' => $rw->secId ?? NULL,
//                     'sectionTypes' => $rw->sectionTypes ?? NULL,
//                     'addressLineOne' => $rw->addressLineOne ?? NULL,
//                     'addressLineTwo' => $rw->addressLineTwo ?? NULL,
//                     'city' => $rw->city ?? NULL,
//                     'zipCode' => $rw->zipCode ?? NULL
//                 ]
//             );
//         }
//         $i++;
//     }
// }

// echo "<br>Total processed: $i";
