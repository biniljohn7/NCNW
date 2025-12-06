<?php
(function () use ($pix, $pixdb, $evg, &$r) {
    $pl = file_get_contents('php://input');
    $pl = $pl ? json_decode($pl, true) : false;
    $token = esc($pl['token'] ?? '');

    if ($token) {
        $uid = $pixdb->getVar(
            'members_auth',
            ['token' => $token],
            'id'
        );
        if (
            $uid &&
            (
                $user = $pixdb->getRow(
                    'members',
                    ['id' => $uid],
                    'id'
                )
            )
        ) {
            $r->status = 'ok';
            $r->success = 1;
            $r->data = $evg->setLoginData($uid);
            $r->message = 'Login successfully!';
        }
    }
    //
})();
