<?php
$reason = null;

if (isset(
    $_FILES['file']
)) {
    $image = $_FILES['file'];

    if (
        preg_match('/\.(jpe*g|png|gif)$/i', $image['name'])
    ) {

        $dateDir = $pix->setDateDir('avatars');
        $uplphoto = $pix->addIMG($image, $dateDir->absdir, 'random', 1200);

        if ($uplphoto) {
            $absFile = $dateDir->absdir . $uplphoto;
            $imgRoot = $dateDir->uplroot . $uplphoto;

            $pix->make_thumb('avatars', $absFile);

            if ($lgUser->avatar) {
                $pix->cleanThumb('avatars', $pix->uploads . 'avatars/' . $lgUser->avatar);
            }
            $pixdb->update(
                'members',
                [
                    'id' => $lgUser->id
                ],
                [
                    'avatar' => $imgRoot
                ]
            );

            $r->success = 1;
            $r->data = [
                'memberId' => $lgUser->id,
                'profileImage' => $dateDir->abspath . $pix->thumb($uplphoto, '150x150')
            ];
            $r->message = 'Image uploaded successfully!';
            unset($r->status);
        }
    }
} else {
    $reason = 'file-empty';
}

if ($reason) {
    $r->reason = $reason;
}
