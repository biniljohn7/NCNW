<?php
if (!$pix->canAccess('location')) {
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
            'chapters',
            [
                'id' => $id,
                'single' => 1
            ]
        );
        if ($data) {
            $evg->removeChapter($id, 'chapter');
            $pix->addmsg('Section deleted successfully.', 1);
            $pix->redirect('?page=chapter');
        }
    }
}
