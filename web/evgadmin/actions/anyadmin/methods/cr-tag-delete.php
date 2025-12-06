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
            'career_types',
            [
                'id' => $id,
                'single' => 1
            ]
        );
        if ($data) {
            $pixdb->update(
                'careers',
                [
                    'type' => $id,
                ],
                [
                    'type' => null
                ]
            );
            $pixdb->delete(
                'career_types',
                [
                    'id' => $id
                ]
            );
            $pix->addmsg('Career tag deleted successfully.', 1);
            $pix->redirect('?page=cr-tags');
        }
    }
}
exit;
