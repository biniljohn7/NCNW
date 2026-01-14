<?php
if (isset($_GET['id'])) {
    $advId = esc($_GET['id']);

    if ($advId) {

        $advocacy = $pixdb->get(
            'advocacies',
            [
                'id' => $advId,
                'enabled' => 'Y',
                'single' => 1
            ],
            'id as advocacyId,
            title,
            enabled,
            senator,
            legislator,
            contact,
            recipient as recipientName,
            recipAddr as recipientAddress,
            recipEmail as recipientEmail,
            createdAt,
            descrptn as description,
            pdf,
            pdfContent,
            image,
            pdfUpload'
        );

        if ($advocacy) {
            if ($advocacy->image) {
                $advocacy->image = $pix->uploadPath . 'advocacy-image/' . $advocacy->image;
            }
            if ($advocacy->pdfUpload) {
                $advocacy->pdfUpload = $pix->uploadPath . 'advocacy-pdf/' . $advocacy->pdfUpload  ;
            }
            $r->status = 'ok';
            $r->success = 1;
            $r->message = 'Viewed Successfully!';
            $r->data = $advocacy;
        }
    }
}
