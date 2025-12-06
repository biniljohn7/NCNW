<?php
(function (
    &$r,
    $pix,
    $pixdb,
    $datetime,
    $evg,
    $lgUser
) {
    $_ = $_POST;

    if (
        isset(
            $_['reqType'],
            $_['priority'],
            $_['summary'],
            $_['desc'],
            $_['impact'],
            $_['reqCompletion']
        )
    ) {
        $rid = esc($_['rid'] ?? '');
        $reqType = esc($_['reqType']);
        $priority = esc($_['priority']);
        $summary = esc($_['summary']);
        $desc = esc($_['desc']);
        $impact = esc($_['impact']);
        $reqCompletion = esc($_['reqCompletion']);
        $comments = esc($_['comments']);
        $refUrl = esc($_['referance'] ?? '');
        $new = !$rid;

        if (
            $reqType &&
            $priority &&
            $summary &&
            $desc &&
            $impact
        ) {
            $rData = false;
            if ($rid) {
                $rData = $pixdb->getRow(
                    'help_desk',
                    ['id' => $rid],
                    'id'
                );
            }
            if (
                $new ||
                (
                    !$new &&
                    $rData
                )
            ) {
                $dbData = [
                    'raisedBy' => $lgUser->id,
                    'reqType' => $reqType,
                    'priority' => $priority,
                    'summary' => $summary,
                    'desc' => $desc,
                    'impact' => $impact,
                    'reqCompletion' => $reqCompletion ? date('Y-m-d', strtotime($reqCompletion)) : null,
                    'comments' => $comments,
                    'refUrl' => $refUrl
                ];

                if ($new) {
                    $dbData['createdAt'] = $datetime;
                    $iid = $pixdb->insert(
                        'help_desk',
                        $dbData
                    );

                    $developers = $pixdb->getcol(
                        'admins',
                        [
                            '#QRY' => 'id != ' . $lgUser->id . ' 
                                        AND FIND_IN_SET("helpdesk/developer", perms) 
                                        AND email != "" AND email IS NOT NULL'
                        ],
                        'email'
                    );

                    if (
                        !empty($developers)
                    ) {
                        $mailData = [
                            'reqType' => $reqType,
                            'priority' => $priority,
                            'summary' => $summary,
                            'id' => $iid,
                            'link' => DOMAIN . 'evgadmin/?page=requests&sec=details&id=' . $iid
                        ];

                        $HelpDesk = loadModule('helpdesk');
                        $HelpDesk->sendUpdate($developers, 'A new help request has been posted', (object)$mailData);
                    }
                } else {
                    $iid = $rid;
                    $dbData['lastActivity'] = $datetime;
                    $pixdb->update(
                        'help_desk',
                        ['id' => $iid],
                        $dbData
                    );
                }
                if ($iid) {
                    $r->status = 'ok';
                    $r->id = $iid;
                }
            }
        }
    }
    //exit;
})(
    $r,
    $pix,
    $pixdb,
    $datetime,
    $evg,
    $lgUser
);
