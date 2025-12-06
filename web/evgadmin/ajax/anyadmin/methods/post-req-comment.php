<?php
$_ = $_POST;

if (
    isset(
        $_['req'],
        $_['comment']
    )
) {
    $req = esc($_['req']);
    $comment = esc($_['comment']);
    $replyto = esc($_['replyto'] ?? '');
    $isAdmin = $lgUser->type == 'admin';

    $task = $pixdb->getRow(
        'help_desk',
        ['id' => $req]
    );

    if (
        $req &&
        $comment &&
        $task &&
        (
            !$replyto ||
            (
                $replyto &&
                (
                    $reqComnt = $pixdb->getRow(
                        'helpdesk_comments',
                        ['id' => $replyto],
                        'id, request, replyto'
                    )
                ) &&
                $reqComnt->request == $req &&
                $reqComnt->replyto == null
            )
        )
    ) {
        $iid = $pixdb->insert(
            'helpdesk_comments',
            [
                'request' => $req,
                'user' => $lgUser->id,
                'date' => $datetime,
                'comment' => $comment,
                'replyto' => $replyto ?: null
            ]
        );

        if ($iid) {
            $pixdb->run(
                "UPDATE help_desk
                    SET `ttlComments`= (
                        SELECT COUNT(1)
                            FROM helpdesk_comments
                            WHERE request=$req
                    )
                WHERE id=$req"
            );

            if ($replyto) {
                $pixdb->run(
                    "SET @COUNT = (
                        SELECT COUNT(1)
                            FROM helpdesk_comments
                            WHERE replyto=$replyto
                    );
                    
                    UPDATE helpdesk_comments
                        SET `replies`= @COUNT
                        WHERE id=$replyto;"
                );
            }

            $r->status = 'ok';
            $r->user = $isAdmin ?
                (object)[
                    'avatar' => null,
                    'name' => 'NCNW Admin'
                ] :
                (object)[
                    'avatar' => null,
                    'name' => $lgUser->name
                ];
            $r->id = $iid;
            $r->date = date(
                'M j, Y, h:i A',
                strtotime($datetime)
            );
            $r->comment = $comment;
            $r->replies = 0;
            $r->own = true;
            $r->count = $pixdb->getRow(
                'help_desk',
                ['id' => $req],
                'ttlComments'
            )->ttlComments;

            ////  email notification
            $to = [];
            $toFilter = [];

            if ($lgUser->id == $task->raisedBy) {
                $toFilter['#QRY'] = 'FIND_IN_SET("helpdesk/developer", perms) 
                                    AND email != "" AND email IS NOT NULL';

                $task->msg = 'A new comment has been posted by the NCNW team, providing their insights or feedback. Please review it to stay updated and take any necessary actions based on their input.';
            } else {
                $toFilter['id'] = $task->raisedBy;
                $toFilter['#QRY'] = 'email != "" AND email IS NOT NULL';

                $task->msg = 'The developers have posted a new comment with updates or feedback. Please review it and take any necessary action.';
            }

            if (
                !empty($toFilter)
            ) {
                $to = $pixdb->getcol(
                    'admins',
                    $toFilter,
                    'email'
                );

                if (
                    !empty($to)
                ) {
                    $task->link = DOMAIN . 'evgadmin/?page=requests&sec=details&id=' . $task->id;

                    $HelpDesk = loadModule('helpdesk');
                    $HelpDesk->sendUpdate($to, 'A new comment has been posted', $task);
                }
            }

            // var_dump($r);
        }
    }
}
// exit;
