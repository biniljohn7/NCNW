<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if (isset($pl->token)) {
    $token = esc($pl->token);
    if ($token) {
        preg_match("/([0-9]+)/", $token, $matches);

        if (isset($matches[1])) {
            $mmbrId = $matches[1];
            $accData = $pixdb->get(
                'members',
                array(
                    'single' => 1,
                    'id' => $mmbrId
                ),
                'id,
                email'
            );

            if ($accData) {
                $res = loadModule('account-verification')->checkEmailVerifn($token, $accData, false, null);

                $r->status = 'ok';

                if ($res->verified) {
                    $r->success = 1;
                    $r->message = 'E-mail verified successfully';
                } else {
                    $r->success = 0;
                    $r->message = $res->errorMsg;
                }
            }
        }
    }
}
