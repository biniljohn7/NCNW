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
            'nations',
            [
                'id' => $id,
                'single' => 1
            ]
        );
        if ($data) {
            $evg->removeNation($id, 'national');
            $pix->addmsg('Country deleted successfully.', 1);
            $pix->redirect('?page=nation');
        }
    }
}
