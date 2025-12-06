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
            image'
        );

        if ($advocacy) {
            if ($advocacy->image) {
                $advocacy->image = $pix->uploadPath . 'advocacy-image/' . $advocacy->image;
            }
            $r->status = 'ok';
            $r->success = 1;
            $r->message = 'Viewed Successfully!';
            $r->data = $advocacy;
        }
    }
}
