<?php
$_ = $_REQUEST;

if (
    isset($_['id'])
) {
    $id = esc($_['id']);

    if ($id) {
        $data = $pixdb->getRow(
            'help_desk',
            ['id' => $id]
        );

        if ($data->id) {
            // Remove request files under this request

            $reqFiles = $pixdb->get(
                'help_desk_files',
                ['request' => $data->id],
                'file'
            );

            foreach ($reqFiles->data as $files) {
                $pix->cleanThumb(
                    'request-files',
                    $pix->basedir . 'uploads/request-images/' . $files->file
                );
            }

            $pixdb->delete(
                'help_desk_files',
                ['request' => $data->id]
            );
        }

        $pixdb->delete(
            'help_desk',
            ['id' => $id]
        );

        $pix->addmsg('Request deleted successfully', 1);
        $pix->redirect('?page=requests');
    }
}
// exit;
