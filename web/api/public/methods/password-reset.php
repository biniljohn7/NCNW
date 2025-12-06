<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if (
    isset(
        $pl->email,
        $pl->otp,
        $pl->password
    )
) {
    $email = esc($pl->email);
    $otp = esc($pl->otp);
    $password = esc($pl->password);

    if (
        is_mail($email) &&
        $otp &&
        $pix->isValidPassword($password)
    ) {
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
            $res = loadModule('account-verification')->checkEmailVerifn($otp, $accData);
            if ($res->verified) {
                $pixdb->update(
                    'members',
                    [
                        'id' => $accData->id
                    ],
                    [
                        'password' => $pix->encrypt($password)
                    ]
                );
            }
            $r->status = 'ok';
            $r->success = 1;
            $r->message = 'Password reset successfully!';
        }
    }
}
