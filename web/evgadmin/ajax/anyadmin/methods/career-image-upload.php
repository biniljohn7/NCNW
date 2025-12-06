<?php
if (!$pix->canAccess('career')) {
    $r->errorMsg = 'Permission denied';
    $pix->json($r);
}

if (
    isset(
        $_FILES['image'],
        $_['cid']
    )
) {
    $image = $_FILES['image'];
    $cid = esc($_['cid']);


    if ($cid) {
        $cData = $pixdb->get(
            'careers',
            [
                'id' => $cid,
                'single' => 1
            ],
            'image'
        );
        if ($cData) {
            if (preg_match('/\.(jpe*g|png|gif)$/i', $image['name'])) {
                $dateDir = $pix->setDateDir('career-image');
                $uplphoto = $pix->addIMG($image, $dateDir->absdir, 'random', 1500);

                if ($uplphoto) {
                    $absFile = $dateDir->absdir . $uplphoto;
                    $imgRoot = $dateDir->uplroot . $uplphoto;

                    $pix->make_thumb('career-pic', $absFile);

                    $pixdb->update(
                        'careers',
                        array('id' => $cid),
                        array('image' => $imgRoot)
                    );

                    if (
                        $cData->image
                    ) {
                        $pix->cleanThumb(
                            'career-pic',
                            $dateDir->upldir . $cData->image,
                            true
                        );
                    }
                    $r->status = 'ok';
                    $r->image = $dateDir->abspath . $pix->thumb($uplphoto, '450x450');
                } else {
                    $r->errorMsg = 'Image upload failed';
                }
            } else {
                $r->errorMsg = 'Invalid image format';
            }
        } else {
            $r->errormsg = 'Missing required params';
        }
    }
}
