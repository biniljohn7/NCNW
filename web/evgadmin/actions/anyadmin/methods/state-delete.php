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
            'states',
            [
                'id' => $id,
                'single' => 1
            ]
        );
        if ($data) {
            $evg->removeState($id, 'state');
            $pix->addmsg('State deleted successfully.', 1);
            $pix->redirect('?page=state');
        }
    }
}
// exit;
