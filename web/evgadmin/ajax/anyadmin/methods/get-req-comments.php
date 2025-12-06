<?php
$_ = $_REQUEST;

if (
    isset($_['request'])
) {
    $request = esc($_['request']);
    $pgn = $pix->getPageNum();
    $isAdmin = $lgUser->type == 'admin';

    if (
        $request &&
        (
            $pixdb->getRow(
                'help_desk',
                ['id' => $request]
            )
        )
    ) {
        $conds = [
            '__QUERY__' => [],
            'request' => $request,
            '__page' => $pgn,
            '__limit' => 10,
            '#SRT' => 'id desc'
        ];

        if ($commentId = esc($_['comment'] ?? '')) {
            $conds['replyto'] = $commentId;
        } else {
            $conds['__QUERY__'][] = 'replyto is null';
        }

        $list = $pixdb->get(
            'helpdesk_comments',
            $conds,
            'id,
            user,
            date,
            type,
            comment,
            replies'
        );

        $raisedIds = [];
        foreach ($list->data as $row) {
            if ($row->user) {
                $raisedIds[] = $row->user;
            }
        }
        $raisedData = $evg->getAnyAdmins($raisedIds, 'id, name, type');

        foreach ($list->data as $cmt) {
            $cmt->own = $isAdmin || ($lgUser && $lgUser->id == $cmt->user);
            if ($cmt->user) {
                $cmt->user = (object)[
                    'avatar' => null,
                    'name' => $raisedData[$cmt->user]->name ?? 'Anonymous User'
                ];
            } else {
                $cmt->user = (object)[
                    'avatar' => $pix->adminURL . 'assets/images/evergreen-logo.png',
                    'name' => 'NCNW Admin'
                ];
            }
            $cmt->date = date(
                'M j, Y, h:i A',
                strtotime($cmt->date)
            );
            $cmt->replies = intval($cmt->replies);
        }

        $r->status = 'ok';
        $r->list = $list->data;
        $r->totalPages = $list->pages;

        // var_dump($r);
    }
}
// exit;
