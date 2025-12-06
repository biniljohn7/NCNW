<?php
if (!$pix->canAccess('contact-enquiries')) {
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
            'enquiries',
            [
                'id' => $id
            ]
        );
        $pix->addmsg('Enquiry deleted successfully.', 1);
        $pix->redirect('?page=enquiries');
    }
}
