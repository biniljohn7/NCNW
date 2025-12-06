<?php
$_ = $_POST;

if (
    isset(
        $_['request'],
        $_['fileId']
    )
) {
    $request = esc($_['request']);
    $fileId = esc($_['fileId']);

    if ($fileId) {

        $fileInfo = $pixdb->getRow(
            'help_desk_files',
            ['id' => $fileId],
            'request, file'
        );

        if (
            $fileInfo &&
            $fileInfo->request == $request
        ) {
            $pix->cleanThumb(
                'request-files',
                $pix->basedir . 'upload/request-images/' . $fileInfo->file
            );

            $pixdb->delete(
                'help_desk_files',
                ['id' => $fileId]
            );

            $pixdb->run(
                "UPDATE help_desk
                    SET `attachments`= (
                        SELECT COUNT(1)
                            FROM help_desk_files
                            WHERE request=$fileInfo->request
                    )
                WHERE id=$fileInfo->request"
            );

            $fileCount = $pixdb->getRow(
                'help_desk',
                ['id' => $request],
                'attachments'
            );

            $r->count = $fileCount->attachments;
        }
        $r->status = 'ok';
    }
}
// exit;
