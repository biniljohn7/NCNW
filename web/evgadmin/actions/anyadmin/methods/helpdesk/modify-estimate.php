<?php
// devMode();

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
            $_['esttype'],
            $_['estdays'],
            $_['esthrs']
        )
    ) {
        $id = esc($_['task']);
        $esttype = esc($_['esttype']);
        $estdays = intval($_['estdays']);
        $esthrs = intval($_['esthrs']);

        if (
            $id &&
            ($task = $pixdb->getRow('help_desk', ['id' => $id])) &&
            $task->status == 'estimated' &&
            (
                $esttype == 'bug' ||
                $esttype == 'new-feature'
            ) &&
            ($estdays || $esthrs)
        ) {

            //     if ($status == 'estimated') {
            //         $modData[
            //     }

            // wrote on db
            $pixdb->update(
                'help_desk',
                ['id' => $id],
                [
                    'lastActivity' => $datetime,
                    'estType' => $esttype,
                    'estTime' => $estdays . ':' . $esthrs
                ]
            );

            // posting log
            $pixdb->insert(
                'helpdesk_comments',
                [
                    'request' => $id,
                    'user' => $lgUser->id,
                    'date' => $datetime,
                    'comment' => 'Developer has modified the estimated time',
                    'type' => 'system'
                ]
            );

            echo 'Estimate has been modified!';

            $pix->addmsg('Estimate has been modified!', 1);
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

// exit;
