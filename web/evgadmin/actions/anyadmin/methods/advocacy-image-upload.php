<?php
if (!$pix->canAccess('advocacy')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb) {
    $_ = $_POST;

    if (
        isset(
            $_FILES['photo'],
            $_['id']
        )
    ) {
        $photo = $_FILES['photo'];
        $id = esc($_['id']);
        if (
            isValidImage($photo) &&
            $id
        ) {
            $advocacy = $pixdb->getRow('advocacies', ['id' => $id], 'image');
            if ($advocacy) {
                $dateDir = $pix->setDateDir('advocacy-image');
                $uplphoto = $pix->addIMG($photo, $dateDir->absdir, 'random', 3500);

                if ($uplphoto) {
                    $absFile = $dateDir->absdir . $uplphoto;
                    $imgRoot = $dateDir->uplroot . $uplphoto;

                    $pix->make_thumb('advocacy-icon', $absFile);

                    $pixdb->update(
                        'advocacies',
                        ['id' => $id],
                        ['image' => $imgRoot]
                    );

                    // clean old image
                    if ($advocacy->image) {
                        $pix->cleanThumb(
                            'advocacy-icon',
                            $dateDir->upldir . $advocacy->image,
                            true
                        );
                    }

                    $pix->addmsg('Image uploaded!', 1);
                }
            }
        }
    }
})($pix, $pixdb);
