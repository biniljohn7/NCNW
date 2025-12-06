<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

$usr = null;

if (isset($pl->email)) {
    $email = esc($pl->email);
    if (is_mail($email)) {
        $accData = $pixdb->get(
            'members',
            [
                'single' => 1,
                'email' =>  $email
            ],
            'id'
        );

        if ($accData) {
            $usr = new stdClass();
            $usr->id = $accData->id;
            $usr->email = $email;
        } else {
            $r->message = 'The email address you entered does not exist.';
        }
    }
}


if ($usr) {
    $status = loadModule('account-verification')->sendVerification(
        $usr,
        null,
        false,
        'otp-send',
        true
    );

    $r->status = 'ok';
    if ($status->sent) {
        $r->success = 1;
        $r->message = 'OTP is sent to this email address';
    } else {
        $r->success = 0;
        $r->message = 'Failed to send OTP';
    }
}
