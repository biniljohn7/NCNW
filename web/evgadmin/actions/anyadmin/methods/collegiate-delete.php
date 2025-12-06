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
            'collegiate_sections',
            [
                'id' => $id,
                'single' => 1
            ]
        );
        if ($data) {
            $pixdb->delete(
                'collegiate_sections',
                ['id' => $id]
            );

            $pix->addmsg('Collegiate section deleted successfully.', 1);
            $pix->redirect('?page=collegiate-sections');
        }
    }
}
