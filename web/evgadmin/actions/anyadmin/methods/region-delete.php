<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_REQUEST;

if (
    isset($_['id'])
) {
    $id = esc($_['id']);

    if ($id) {
        $data = $pixdb->get(
            'regions',
            [
                'id' => $id,
                'single' => 1
            ]
        );
        if ($data) {
            $evg->removeRegion($id, 'regional');
            $pix->addmsg('Region deleted successfully.', 1);
            $pix->redirect('?page=region');
        }
    }
}
