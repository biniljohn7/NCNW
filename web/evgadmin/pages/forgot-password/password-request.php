<?php
loadStyle('pages/forgot-password/password-request');
?>

<div class="pass-req">
    <div class="req-form">
        <div class="form-box">
            <div class="logo">
                <img src="<?php echo $pix->adminURL; ?>assets/images/evergreen-logo.png">
            </div>
            <div class="form-desc">
                Forgot Your Password?
            </div>
            <div class="form-sub-desc">
                Enter your email address to start the process of resetting your password.
            </div>
            <form action="<?php echo ADMINURL, 'actions/public/' ?>" method="post" id="passReq">
                <input type="hidden" name="method" value="forgot-pass" />
                <div class="field">
                    <input type="text" name="email" autocomplete="off" placeholder="Email address">
                </div>
                <div class="field btn">
                    <input type="submit" value="Continue" class="pix-btn rounded pink bold-500">
                </div>
            </form>
        </div>
    </div>
</div>