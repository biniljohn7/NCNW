<?php
loadStyle('pages/account-verify/pwd-reset');
$token = isset($_GET['t']) ? esc($_GET['t']) : '';
$tokenVerif = false;
$err = 'Invalid verification';
$userId = null;

if ($token) {
    preg_match("/([0-9]+)/", $token, $matches);
    if (isset($matches[1])) {
        $userId = $matches[1];
        $accData = $pixdb->get(
            'admins',
            array(
                'single' => 1,
                'id' => $userId
            ),
            'id,
                email'
        );

        if ($accData) {
            $user = (object) array(
                'id' => $accData->id,
                'email' => $accData->email
            );

            $recvModule = loadModule('admin-account-verification');
            $codeCheck = $recvModule->checkEmailVerifn($token, $user, true);
            if ($codeCheck && $codeCheck->verified) {
                $tokenVerif = true;
            } else {
                $err = $codeCheck && isset($codeCheck->errorMsg) ? $codeCheck->errorMsg : $err;
            }
        }
    }
}

if ($tokenVerif) {
?>
    <div class="newpass-page">
        <div class="recv-form">
            <div class="recv-box">
                <div class="logo">
                    <img src="<?php echo $pix->adminURL; ?>assets/images/evergreen-logo.png">
                </div>
                <form action="<?php echo $pix->adminURL, 'actions/public/'; ?>" method="post" id="pwdResetFm">
                    <input type="hidden" name="method" value="pwd-reset" />
                    <input type="hidden" name="reqId" value="<?php echo $userId; ?>" />
                    <input type="hidden" name="token" value="<?php echo $token; ?>" />
                    <div class="lg-title">
                        Set New Password
                    </div>
                    <div class="field">
                        <input type="password" placeholder="New password" name="pass1" id="pass1" data-type="string" data-label="password" />
                    </div>
                    <div class="field">
                        <input type="password" name="pass2" id="pass2" placeholder="Confirm password" data-type="string" data-label="confirm password" />
                    </div>
                    <div class="recv-submit-btn pt30">
                        <button type="submit" name="new-pass" class="pix-btn rounded pink bold-500">
                            Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
} else {
    $pix->addmsg($err);
    $pix->redirect($pix->adminURL . '?page=login');
}
?>