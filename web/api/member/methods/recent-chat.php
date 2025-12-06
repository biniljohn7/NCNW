<?php
$r->status = 'ok';
$r->success = 1;

// Recent chats: members
$sqlMembers = "
    SELECT 
        l.lastMsg AS lastMessage,
        m.firstName,
        m.lastName,
        m.id AS memberId,
        m.avatar,
        NULL AS groupId,
        NULL AS owner,
        'member' AS type,
        l.lastMsgOn
    FROM message_logs l
    JOIN members m ON l.sender = m.id
    WHERE l.lastMsg IS NOT NULL
      AND l.user = " . (int)$lgUser->id . "
";

// Recent chats: groups
$sqlGroups = "
    SELECT 
        g.lastMsg AS lastMessage,
        NULL AS firstName,
        g.title AS lastName,
        NULL AS memberId,
        NULL AS avatar,
        g.id AS groupId,
        g.createdBy As owner,
        'group' AS type,
        g.lastMsgOn
    FROM message_groups g
    JOIN message_group_members gm ON gm.groupId = g.id
    WHERE g.lastMsg IS NOT NULL
      AND gm.member = " . (int)$lgUser->id . "
";

// Combine both
$sql = "
    ($sqlMembers)
    UNION ALL
    ($sqlGroups)
    ORDER BY lastMsgOn DESC
    LIMIT 10
";

$rcntChat = $pixdb->fetchAll($sql);

$admins = collectObjData($rcntChat, 'owner');
$getMemberProfile = [];
if ($admins) {
    $getMemberProfile = $pixdb->fetchAssoc(
        [
            ['admins', 'a', 'memberid'],
            ['members', 'm', 'id']
        ],
        [
            'a.id' => $admins
        ],
        'a.id,
        m.firstName,
        m.lastName,
        a.memberid,
        m.avatar',
        'id'
    );
}

// Format results
foreach ($rcntChat as $itm) {
    if ($itm->type === 'member') {
        $itm->fullName = $itm->firstName . ' ' . $itm->lastName;
        $itm->profileImage = $itm->avatar
            ? $pix->avatar($itm->avatar, '150x150', 'avatars')
            : null;
    } else {
        $itm->fullName = $itm->lastName;
        $itm->profileImage = null;
        $itm->memberId = "gp_$itm->groupId";
        if (isset($getMemberProfile[$itm->owner])) {
            $mmbr = $getMemberProfile[$itm->owner];
            $itm->gpAdmin = (object)[
                'fullName' => $mmbr->firstName . ' ' . $mmbr->lastName,
                'profileImage' => $mmbr->avatar
                    ? $pix->avatar($mmbr->avatar, '150x150', 'avatars')
                    : null,
                'memberId' => $mmbr->memberid
            ];
        }
    }

    unset($itm->firstName, $itm->lastName, $itm->avatar);

    // Hide image-type message previews
    if (preg_match('/^\{msgImg\:/i', $itm->lastMessage)) {
        $itm->lastMessage = '';
    }
}

$r->data = $rcntChat;

$r->message = 'Data Retrieved Successfully!';
