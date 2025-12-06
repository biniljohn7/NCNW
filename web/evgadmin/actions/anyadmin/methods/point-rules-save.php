<?php
if (!$pix->canAccess('point-rules')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb, $evg) {
    $_ = $_POST;

    if (
        isset(
            $_['share'],
            $_['use'],
            $_['minred'],
            $_['d1val']
        )
    ) {
        $share = intval($_['share']);
        $use = intval($_['use']);
        $minred = intval($_['minred']);
        $d1val = intval($_['d1val']);

        if (
            $share &&
            $use &&
            $minred &&
            $d1val
        ) {
            $pixdb->insert(
                'point_rules',
                [
                    'id' => 1,
                    'sharing' => $share,
                    'using' => $use,
                    'minRedeem' => $minred,
                    'ptsDollar' => $d1val
                ],
                true
            );

            $pix->addmsg('Point rules saved!', 1);
        }
    }
})($pix, $pixdb, $evg);
