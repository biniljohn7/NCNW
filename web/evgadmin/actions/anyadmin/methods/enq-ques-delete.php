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
            'enquiry_questions',
            [
                'id' => $id
            ]
        );
        $pix->addmsg('Question deleted successfully.', 1);
        $pix->redirect('?page=enq-ques');
    }
}
exit;
