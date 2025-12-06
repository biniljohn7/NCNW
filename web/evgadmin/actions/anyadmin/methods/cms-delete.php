<?php
if (!$pix->canAccess('cms-pages')) {
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
            'cms',
            [
                'id' => $id
            ]
        );
        $pix->addmsg('CMS deleted successfully', 1);
        $pix->redirect('?page=cms');
    }
}
// exit;
