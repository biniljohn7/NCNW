<?php
(function ($pix, $pixdb, $lgUser) {
    $_ = $_POST;

    if (
        isset(
            $_['curpass'],
            $_['npass1'],
            $_['npass2']
        )
    ) {
        $curpass = esc($_['curpass']);
        $npass1 = esc($_['npass1']);
        $npass2 = esc($_['npass2']);

        if (
            $curpass &&
            $npass1 &&
            $npass2 == $npass1
        ) {
            if ($lgUser->password == $pix->encrypt($curpass)) {
                $npwEnc = $pix->encrypt($npass1);
                // modifying db
                $pixdb->update(
                    'admins',
                    ['id' => $lgUser->id],
                    ['password' => $npwEnc]
                );

                // changing session
                loadModule('admins')->storeSession(
                    $lgUser->username ?: $lgUser->email,
                    $npwEnc
                );

                $pix->addmsg('Password changed successfully', 1);

                // 
            } else {
                $pix->addmsg('Current password is incorrect');
            }
        }
    }
})($pix, $pixdb, $lgUser);
