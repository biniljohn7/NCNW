<?php
if (!$pix->canAccess('events')) {
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
            $event = $pixdb->getRow('events', ['id' => $id], 'image');
            if ($event) {
                $dateDir = $pix->setDateDir('events');
                $uplphoto = $pix->addIMG($photo, $dateDir->absdir, 'random', 1500);

                if ($uplphoto) {
                    $absFile = $dateDir->absdir . $uplphoto;
                    $imgRoot = $dateDir->uplroot . $uplphoto;

                    $pix->make_thumb('events', $absFile);

                    $pixdb->update(
                        'events',
                        ['id' => $id],
                        ['image' => $imgRoot]
                    );

                    // clean old image
                    if ($event->image) {
                        $pix->cleanThumb(
                            'events',
                            $dateDir->upldir . $event->image,
                            true
                        );
                    }

                    $pix->addmsg('Image uploaded!', 1);
                }
            }
        }
    }
})($pix, $pixdb);
