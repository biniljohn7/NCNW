<?php
(function ($pixdb, &$r, $pix, $evg) {
    $_ = $_POST;

    if (
        $pix->canAccess('members') &&
        isset(
            $_['membId'],
            $_['newSts']
        )
    ) {
        $id = intval($_['membId']);
        $newSts = esc($_['newSts']);

        if (
            $id &&
            $newSts
        ) {

            if($newSts == 'N') {
                $evg->changeAccessToken($id);
            }
            
            $pixdb->update(
                'members',
                ['id' => $id],
                ['enabled' => $newSts]
            );
            $r->status = 'ok';
        }
    }
})($pixdb, $r, $pix, $evg);
