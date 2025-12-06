<?php
if (!$pix->canAccess('career')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_REQUEST;

if (
    isset(
        $_['id']
    )
) {
    $id = esc($_['id']);

    if (
        $id
    ) {
        $data = $pixdb->get(
            'careers',
            [
                'id' => $id,
                'single' => 1
            ]
        );
        if ($data) {
            if ($data->image) {
                $pix->cleanThumb(
                    'career-pic',
                    $pix->uploads . 'career-image/' . $data->image
                );
            }
            $pixdb->delete(
                'careers',
                [
                    'id' => $id
                ]
            );
            $pix->addmsg('Career deleted successfully.', 1);
            $pix->redirect('?page=career');
        }
    }
}
// exit;
