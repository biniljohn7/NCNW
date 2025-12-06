<?php
$_ = $_POST;

if (
    isset(
        $_['reqId'],
        $_['token'],
        $_['pass1'],
        $_['pass2']
    )
) {
    $reqId = esc($_['reqId']);
    $token = esc($_['token']);
    $pass1 = esc($_['pass1']);
    $pass2 = esc($_['pass2']);

    if (
        $reqId &&
        $token &&
        $pass1 &&
        $pass1 == $pass2
    ) {
        $accData = $pixdb->getRow(
            'admins',
            [
                'id' => $reqId
            ],
            'email'
        );
        if ($accData) {
            $usr = (object)array(
                'id' => $reqId,
                'email' => $accData->email
            );
            $recvModule = loadModule('admin-account-verification');
            $codeCheck = $recvModule->checkEmailVerifn($token, $usr, false);
            if ($codeCheck->verified) {
                $pixdb->update(
                    'admins',
                    ['id' => $reqId],
                    ['password' => $pix->encrypt($pass1)]
                );
                $pix->addmsg('Please, login your account', 1);
                $pix->redirect('?page=login');
            }
        }
    }
}
// exit;
