<?php
if (!$pix->canAccess('benefit')) {
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
            'benefit_providers',
            [
                'id' => $id,
                'single' => 1
            ],
            'id, logo'
        );
        if ($data) {
            // cleaning logo
            if ($data->logo) {
                $pix->cleanThumb(
                    'provider-logo-pic',
                    $pix->uploads . 'provider-logo/' . $data->logo
                );
            }

            $pixdb->update(
                'benefits',
                ['provider' => $id],
                ['provider' => null]
            );
            $pixdb->delete(
                'benefit_providers',
                ['id' => $id]
            );
            $pix->addmsg('Benefit provider deleted successfully.', 1);
            $pix->redirect('?page=provider');
        }
    }
}
