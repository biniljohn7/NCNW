<?php
(function ($pix, $pixdb, $r, $evg) {
    $pl = file_get_contents('php://input');
    $pl = $pl ? json_decode($pl) : false;
    if (!is_object($pl)) {
        $pl = false;
    }
    $r->lgWithOtp = false;

    if (isset($pl->email)) {
        $email = esc($pl->email);

        if ($email) {
            $member = $pixdb->get(
                'members',
                [
                    'email' => $email,
                    'single' => 1
                ],
                'id,
                email,
                password,
                verified,
                account'
            );
            if ($member) {
                if ($member->verified == 'Y') {
                    if ($member->account == null && ($member->password == null || $member->password == '')) {
                        $status = loadModule('account-verification')->sendVerification(
                            $member,
                            null,
                            true,
                            'otp-send',
                            true
                        );

                        $r->status = 'ok';
                        if ($status->sent) {
                            $r->success = 1;
                            $r->message = 'OTP is sent to this email address';
                            $r->lgWithOtp = true;
                        } else {
                            $r->success = 0;
                            $r->message = 'Failed to send OTP';
                        }
                    } else {
                        $r->success = 1;
                        $r->message = '';
                    }
                } else {
                    $r->message = 'Email is not verified yet. Please verify your email first!';
                }
            } else {
                $r->message = 'Login failed. Invalid email.';
            }
        }
    }
})($pix, $pixdb, $r, $evg);
