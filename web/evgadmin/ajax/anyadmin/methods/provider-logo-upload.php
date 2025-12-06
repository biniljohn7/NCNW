<?php
if (!$pix->canAccess('benefit')) {
    $r->errorMsg = 'Permission denied';
    $pix->json($r);
}

if (
    isset(
        $_FILES['logo'],
        $_['pid']
    )
) {
    $logo = $_FILES['logo'];
    $pid = esc($_['pid']);

    if ($pid) {
        $pData = $pixdb->get(
            'benefit_providers',
            [
                'id' => $pid,
                'single' => 1
            ],
            'logo'
        );
        if ($pData) {
            if (preg_match('/\.(jpe*g|png|gif)$/i', $logo['name'])) {
                $dateDir = $pix->setDateDir('provider-logo');
                $uplphoto = $pix->addIMG($logo, $dateDir->absdir, 'random', 3500);

                if ($uplphoto) {
                    $absFile = $dateDir->absdir . $uplphoto;
                    $imgRoot = $dateDir->uplroot . $uplphoto;

                    $pix->make_thumb('provider-logo-pic', $absFile);

                    $pixdb->update(
                        'benefit_providers',
                        array('id' => $pid),
                        array('logo' => $imgRoot)
                    );

                    if ($pData->logo) {
                        $pix->cleanThumb(
                            'provider-logo-pic',
                            $dateDir->upldir . $pData->logo,
                            true
                        );
                    }
                    $r->status = 'ok';
                    $r->logo = $dateDir->abspath . $pix->thumb($uplphoto, '450x450');
                } else {
                    $r->errormsg = 'Invalid file';
                }
            } else {
                $r->errormsg = 'Some format are invalid';
            }
        } else {
            $r->errormsg = 'Missing required params';
        }
    }
}
