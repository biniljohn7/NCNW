<?php
include '../../lib/lib.php';

$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 1;
$offset = ($page - 1) * $limit;

$sql = "
    SELECT email
    FROM members
    WHERE email IS NOT NULL
    GROUP BY email
    HAVING COUNT(*) > 1
    LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$duplicate = $stmt->fetch();

if ($duplicate) {
    $email = $duplicate['email'];
    echo "<h3>Processing duplicate group for email: <code>{$email}</code></h3>";

    // --- Step 2: Get all members with this email ---
    $stmtMembers = $pdo->prepare("SELECT * FROM members WHERE email = :email");
    $stmtMembers->execute([':email' => $email]);
    $members = $stmtMembers->fetchAll();

    foreach ($members as $m) {
        $id        = $m['id'];
        $memberId  = $m['memberId'];
        $enabled   = $m['enabled'];

        // --- Step 3: Check delete conditions ---
        if ($enabled === 'N' && !preg_match('/^\d+$/', $memberId)) {
            // --- Step 4: Delete the member ---
            $del = $pdo->prepare("DELETE FROM members WHERE id = :id");
            $del->execute([':id' => $id]);
        }
    }

    // --- Step 5: Auto-refresh to next group ---
    $next = $page + 1;
    echo "<meta http-equiv='refresh' content='1;url=?page={$next}'>";
} else {
    echo "<h1>End of duplicate groups.</h1>";
}
