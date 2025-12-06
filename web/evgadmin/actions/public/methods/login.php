<?php
$_ = $_POST;

if (
    isset(
        $_['uname'],
        $_['pass'],
        $_['login']
    )
) {
    $uname = esc($_['uname']);
    $pass = esc($_['pass']);
    if (
        $uname &&
        $pass
    ) {
        $pass = $pix->encrypt($pass);
        $uData = $pixdb->get(
            'admins',
            [
                is_mail($uname) ? 'email' : 'username' => $uname,
                'single' => 1
            ]
        );
        if (
            $uData &&
            $uData->enabled == 'Y' &&
            $uData->password == $pass
        ) {

            $rd = isset($_REQUEST['rd']) ? urldecode($_REQUEST['rd']) : '';
            loadModule('admins')->storeSession(
                $uname,
                $pass
            );
            $pix->remsg();
            $pix->redirect($pix->adminURL . $rd);
        }
    }
    $pix->redirect($pix->adminURL);
}
$pix->addmsg('Oops. Login failed.');
