<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if (
    isset(
        $pl->email,
        $pl->otp
    )
) {
    $email = esc($pl->email);
    $otp = esc($pl->otp);

    if ($email && $otp) {
        $accData = $pixdb->get(
            'members',
            array(
                'single' => 1,
                'email' => $email
            ),
            'id,
            email'
        );

        if ($accData) {
            $res = loadModule('account-verification')->checkEmailVerifn($otp, $accData, true);

            $r->status = 'ok';

            if ($res->verified) {
                $r->success = 1;
                $r->otp = $otp;
                $r->message = 'OTP verified successfully';
            } else {
                $r->success = 0;
                $r->message = $res->errorMsg;
            }
        }
    }
}
