<?php
(function (
    $pix,
    $pixdb,
    $datetime,
    $lgUser
) {
    $_ = $_REQUEST;

    if (
        isset(
            $_['task'],
            $_['status']
        )
    ) {
        $id = esc($_['task']);
        $status = esc($_['status']);
        $statusRanks = [
            'estimated' => 1,
            'est-verified' => 2,
            'approved' => 3,
            'started' => 4,
            'deployed' => 5,
            'revise-update' => 6,
            'resolved' => 7
        ];
        $statusPermissions = [
            'estimated' => 'developer',
            'est-verified' => 'project-coordinators',
            'approved' => 'ncnw-team',
            'started' => 'developer',
            'deployed' => 'developer',
            'revise-update' => 'ncnw-team',
            'resolved' => 'ncnw-team'
        ];

        $othErrors = false;

        $havePermission = $pix->canAccess('helpdesk/' . $statusPermissions[$status]);

        // check estimated
        if ($status == 'estimated') {
            $othErrors = true;
            if (
                isset(
                    $_['esttype'],
                    $_['estdays'],
                    $_['esthrs']
                )
            ) {
                $esttype = esc($_['esttype']);
                $estdays = intval($_['estdays']);
                $esthrs = intval($_['esthrs']);

                if (
                    (
                        $esttype == 'bug' ||
                        $esttype == 'new-feature'
                    ) &&
                    ($estdays || $esthrs)
                ) {
                    $othErrors = false;
                }
            }
        }

        // post revice-update
        if ($status == 'revise-update') {
            $othErrors = true;
            if (isset($_['message'])) {
                $revise = esc($_['message']);
                if ($revise) {
                    $othErrors = false;
                }
            }
        }

        if (
            $id &&
            $status &&
            ($nStatRank = $statusRanks[$status] ?? 0) &&
            ($task = $pixdb->getRow('help_desk', ['id' => $id])) &&
            (
                $nStatRank > ($statusRanks[$task->status] ?? 0) ||
                (
                    $status == 'started' &&
                    $task->status == 'revise-update'
                )
            ) &&
            !$othErrors &&
            $havePermission
        ) {

            $modData = [
                'status' => $status,
                'lastActivity' => $datetime
            ];

            if ($status == 'estimated') {
                $modData['estType'] = $esttype;
                $modData['estTime'] = $estdays . ':' . $esthrs;
            }

            // wrote on db
            $pixdb->update('help_desk', ['id' => $id], $modData);

            // 
            $addComment = function (
                $comment,
                $type = 'regular'
            ) use (
                $pixdb,
                $id,
                $lgUser,
                $datetime
            ) {
                $pixdb->insert(
                    'helpdesk_comments',
                    [
                        'request' => $id,
                        'user' => $lgUser->id,
                        'date' => $datetime,
                        'comment' => $comment,
                        'type' => $type
                    ]
                );
            };

            // posting comments
            $statusMsgs = [
                'estimated' => [
                    'An estimate has been submitted by the development team.',
                    'An estimate has been submitted for your review. Kindly verify the details to ensure accuracy and alignment with your requirements.'
                ],
                'est-verified' => [
                    'The project coordinator(s) have reviewed and approved the submitted estimate.',
                    'The project coordinator(s) have reviewed and approved the submitted estimate. Please review it on your end and provide your approval to initiate the work.'
                ],
                'approved' => [
                    'The NCNW Team has reviewed and approved this task.',
                    'The NCNW Team has reviewed and approved this task. You may proceed with starting the work.'
                ],
                'started' => [
                    'Development has started on this task.',
                    'The development phase for this task has started. The team is actively working on implementing the necessary features and ensuring that all requirements are met.'
                ],
                'deployed' => [
                    'The development team has deployed the requested changes.',
                    'The development team has deployed the requested changes. Kindly review them and either close the task or inform the team if any amendments are needed in the latest deployment.'
                ],
                'resolved' => [
                    'The task has been resolved.',
                    'The task has been completed, tested, and successfully implemented. It is fully reviewed and ready for use or deployment.'
                ]
            ];

            $to = [];
            $toFilter = [];
            $title = '';
            if (isset($statusMsgs[$status])) {
                $addComment($statusMsgs[$status][0], 'system');
                // 
                switch ($status) {
                    case 'estimated':
                        $toFilter['#QRY'] = 'FIND_IN_SET("helpdesk/project-coordinators", perms) 
                                            AND email != "" AND email IS NOT NULL';
                        break;
                        ////

                    case 'est-verified':
                    case 'started':
                    case 'deployed':
                        $toFilter['id'] = $task->raisedBy;
                        $toFilter['#QRY'] = 'email != "" AND email IS NOT NULL';
                        break;
                        ////

                    case 'approved':
                    case 'resolved':
                        $toFilter['#QRY'] = 'FIND_IN_SET("helpdesk/developer", perms) 
                                            AND email != "" AND email IS NOT NULL';
                        break;
                }
                $title = $statusMsgs[$status][0] ?? '';
                $task->msg = $statusMsgs[$status][1] ?? '';
                ////
            } else {
                if ($status == 'revise-update') {
                    $addComment($revise);

                    $toFilter['#QRY'] = 'FIND_IN_SET("helpdesk/developer", perms) 
                                        AND email != "" AND email IS NOT NULL';

                    $title = 'The NCNW team has requested amendments to your last deployment';
                    $task->msg = 'The NCNW team has requested amendments to your last deployment. Please make the necessary changes or share your comments with them if needed.';
                }
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
                    $HelpDesk->sendUpdate($to, $title, $task);
                }
            }

            echo 'Status updated!';

            $pix->addmsg('Status updated!', 1);
            // 
        } else {
            echo 'validation error';
        }
    } else {
        echo 'missing data';
    }

    //
})(
    $pix,
    $pixdb,
    $datetime,
    $lgUser
);

if (isset($_REQUEST['dev'])) {
    devMode();
    exit;
}
