<?php
(function ($pixdb, &$r, $pix, $evg) {
    $_ = $_GET;
    $r->list = false;

    if (
        //$pix->canAccess('members') &&
        isset(
            $_['grpId'],
            $_['search']
        )
    ) {
        $grpId = intval($_['grpId']);
        $search = esc($_['search']);

        $like = "";
        if (
            $search &&
            $search != 'false'
        ) {
            $qSearch = q("%$search%");
            $like = "AND concat(firstName, ' ', lastName) LIKE $qSearch ";
        }
        //query
        $allMembers = $pixdb->custom_query(
            'SELECT 
                id,
                concat(firstName, " ", lastName) as fName,
                avatar
                FROM members
                WHERE id IN(
                    SELECT member 
                    FROM message_group_members
                    WHERE `groupId` = ' . $grpId . '
                )
                ' . $like . '
                ORDER BY `firstName` ASC'
        )->data;
        //changeing avatar
        foreach ($allMembers as $members) {
            if ($members->avatar) {
                $members->avatar = $evg->getAvatar($members->avatar);
            }
        }
        //creating scuccess return
        if (!empty($allMembers)) {
            $r->status = 'ok';
            $r->list = json_encode($allMembers);
        }
    }
})($pixdb, $r, $pix, $evg);
