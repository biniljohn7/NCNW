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
            'affiliates',
            [
                'id' => $id,
                'single' => 1
            ]
        );
        if ($data) {
            $pixdb->delete(
                'affiliates',
                ['id' => $id]
            );

            $pix->addmsg('Affiliate deleted successfully.', 1);
            $pix->redirect('?page=affiliates');
        }
    }
}
