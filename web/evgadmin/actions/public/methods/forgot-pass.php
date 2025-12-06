<?php
$_ = $_POST;

if (
    isset(
        $_['email']
    )
) {
    $email = esc($_['email']);

    if (
        $email
    ) {
        $admins = $pixdb->getRow(
            'admins',
            [
                is_mail($email) ? 'email' : 'username' => $email
            ],
            'id,email'
        );
        if (
            $admins &&
            $admins->email
        ) {
            $recv = loadModule('admin-account-verification');
            $staus = $recv->sendVerification(
                (object)array(
                    'id' => $admins->id,
                    'email' => $admins->email
                ),
                null,
                false
            );
            if ($staus->sent) {
                $pix->remsg();
                $pix->redirect(
                    $pix->adminURL . '?page=forgot-password&sec=request-status' .
                        (
                            !$staus->sent ? '?t=' .
                            (
                                $status->nSendTm ?: 0
                            ) : ''
                        )
                );
            }
        } else {
            $pix->addmsg('No account found');
        }
    }
}
// exit;
