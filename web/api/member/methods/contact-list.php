<?php
$r->status = 'ok';
$r->success = 1;
$list = [];

$srh = have($_['search'], null);
$pgn = have($_['pageId'], 1);
$pageLimit = 10;

// Get groups where user is a member
$mmbrGps = $pixdb->getCol(
    'message_group_members',
    ['member' => $lgUser->id],
    'groupId'
);

$whereMembers = ["verified='Y'", 'id!=' . $lgUser->id];
$whereGroups  = [];

// Search condition
if ($srh) {
    $qSearch = q("%$srh%");
    $whereMembers[] = "(
        concat(firstName, ' ', lastName) LIKE $qSearch OR 
        email LIKE $qSearch OR
        memberId LIKE $qSearch
    )";

    $whereGroups[] = "(
        title LIKE $qSearch
    )";
}

// Query members
$sqlMembers = "
    SELECT 
        id,
        concat(firstName, ' ', lastName) AS name,
        email,
        memberId,
        avatar,
        'member' AS type
    FROM members
    WHERE firstName!='' AND " . implode(' AND ', $whereMembers);

// Query groups only if user has any
$sqlGroups = null;
if (!empty($mmbrGps)) {
    $sqlGroups = "
        SELECT 
            id,
            title AS name,
            NULL AS email,
            NULL AS memberId,
            NULL AS avatar,
            'mmbrgroup' AS type
        FROM message_groups
        WHERE id IN (" . implode(',', $mmbrGps) . ")" .
        ($whereGroups ? 'AND ' . implode(' AND ', $whereGroups) : '');
}

// Combine both
if ($sqlGroups) {
    $sql = "
        ($sqlGroups)
        UNION ALL
        ($sqlMembers)
        ORDER BY type DESC, name ASC
        LIMIT " . (($pgn - 1) * $pageLimit) . ", $pageLimit
    ";
} else {
    $sql = "
        $sqlMembers
        ORDER BY type DESC, name ASC
        LIMIT " . (($pgn - 1) * $pageLimit) . ", $pageLimit
    ";
}

$contacts = $pixdb->fetchAll($sql);

// Format results
$data = [];
foreach ($contacts as $mm) {
    $mm->fullName = html_entity_decode($mm->name, ENT_QUOTES, 'UTF-8');
    $mm->phoneCode = null;
    $mm->phoneNumber = null;
    if ($mm->type === 'member') {
        $mm->profileImage = $mm->avatar
            ? $pix->avatar($mm->avatar, '150x150', 'avatars')
            : null;
    } else {
        $mm->memberId = "gp_$mm->id";
        $mm->profileImage = null;
    }
    unset($mm->avatar);

    $data[] = $mm;
}

// Count total rows for pagination
$countSqlParts = [];

if (!empty($mmbrGps)) {
    $countSqlParts[] = "
        SELECT COUNT(*) AS cnt
        FROM message_groups
        WHERE id IN (" . implode(',', $mmbrGps) . ")" .
        ($whereGroups ? 'AND ' . implode(' AND ', $whereGroups) : '');
}

$countSqlParts[] = "
    SELECT COUNT(*) AS cnt
    FROM members
    WHERE " . implode(" AND ", $whereMembers);

$countSql = implode(" UNION ALL ", $countSqlParts);

$totalCount = 0;
$rows = $pixdb->fetchAll($countSql);
foreach ($rows as $row) {
    $totalCount += $row->cnt;
}

$totalPages = ceil($totalCount / $pageLimit);

$r->data = (object)[
    'list' => $data,
    'currentPageNo' => (int)$pgn,
    'totalPages' => $totalPages
];

$r->message = 'Data Retrieved Successfully!';
