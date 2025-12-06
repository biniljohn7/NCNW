<?php
(function ($pix, $pixdb, $r, $evg) {
    $pl = file_get_contents('php://input');
    $pl = $pl ? json_decode($pl) : false;
    if (!is_object($pl)) {
        $pl = false;
    }

    $r->lgWithOtp = false;
    $r->status = 'ok';

    if (
        isset(
            $pl->email,
            $pl->password
        )
    ) {
        $email = esc($pl->email);
        $password = esc($pl->password);

        if (
            $email &&
            $password
        ) {
            $member = $pixdb->getRow(
                'members',
                ['email' => $email],
                'id, 
                email,
                password, 
                verified,
                enabled'
            );
            if ($member) {
                if ($member->password == $pix->encrypt($password)) {
                    $canLogin = true;
                    if ($member->verified == 'N') {
                        $canLogin = false;
                        $r->message = 'Email is not verified yet. Please verify your email first!';
                    }
                    if ($member->enabled == 'N') {
                        $canLogin = false;
                        $r->message = 'Your profile has been inactivated for some reason.';
                    }

                    if ($canLogin) {
                        $r->success = 1;
                        $r->data = $evg->setLoginData($member->id);
                        $r->message = 'Login successfully!';
                    }
                } else {
                    $res = loadModule('account-verification')->checkEmailVerifn($password, $member, true);
                    if ($res->verified) {
                        $r->success = 1;
                        $r->lgWithOtp = true;
                    } else {
                        $r->message = 'Login failed. Invalid email or password.';
                    }
                }
            } else {
                $r->message = 'No active profile found. Please create an account.';
            }
        }
    }
})($pix, $pixdb, $r, $evg);
