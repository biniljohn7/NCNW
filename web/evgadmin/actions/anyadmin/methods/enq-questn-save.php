<?php
if (!$pix->canAccess('contact-enquiries')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['ques']
    )
) {
    $ques = esc($_['ques']);
    $nid = esc($_['nid'] ?? '');
    $new = !$nid;

    if ($ques) {
        $data = false;
        if ($nid) {
            $data = $pixdb->get(
                'enquiry_questions',
                [
                    'id' => $nid,
                    'single' => 1
                ],
                'id'
            );
        }
        if (
            $new ||
            (
                !$new &&
                $data
            )
        ) {
            $dbData = [
                'question' => $ques
            ];
            if ($new) {
                $iid = $pixdb->insert(
                    'enquiry_questions',
                    $dbData
                );
            } else {
                $iid = $nid;
                $pixdb->update(
                    'enquiry_questions',
                    [
                        'id' => $iid
                    ],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('Question saved', 1);
                $pix->redirect('?page=enq-ques');
            }
        }
    }
}
