<?php
if (!$pix->canAccess('benefit')) {
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
        $pixdb->delete(
            'benefits',
            [
                'id' => $id
            ]
        );
        $pix->addmsg('Benefit deleted successfully.', 1);
        $pix->redirect('?page=benefits');
    }
}
