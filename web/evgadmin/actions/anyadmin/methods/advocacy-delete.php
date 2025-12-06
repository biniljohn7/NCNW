<?php
if (!$pix->canAccess('advocacy')) {
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
        $advMemberData = $pixdb->get(
            'advocacies',
            [
                'id' => $id,
                'single' => 1
            ],
            'id,image'
        );
        $memberSignPdf = false;
        if ($advMemberData->id) {
            $memberSignPdf = $pixdb->get(
                'member_advocacy',
                [
                    'advocacy' => $advMemberData->id,
                    'single' => 1
                ],
                'signature,pdf'
            );
            if ($memberSignPdf) {
                if ($memberSignPdf->signature) {
                    $pix->cleanThumb(
                        'signature',
                        $pix->uploads . 'signatures/' . $memberSignPdf->signature
                    );
                }
                if ($memberSignPdf->pdf) {
                    $pdfFile = $pix->uploads . 'advocacy/' . $memberSignPdf->pdf;
                    if (is_file($pdfFile)) {
                        unlink($pdfFile);
                    }
                }
                $pixdb->delete(
                    'member_advocacy',
                    [
                        'advocacy' => $advMemberData->id
                    ]
                );
            }
        }
        if ($advMemberData->image) {
            $pix->cleanThumb(
                'advocacy-icon',
                $pix->uploads . 'advocacy-image/' . $advMemberData->image
            );
        }
        $pixdb->delete(
            'advocacies',
            [
                'id' => $id
            ]
        );
        $pix->addmsg('Advocacy deleted successfully.', 1);
        $pix->redirect('?page=advocacy');
    }
}
// exit;
