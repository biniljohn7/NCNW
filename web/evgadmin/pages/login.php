<?php
$rd = '';
if (isset($_GET['rd'])) {
    $rd = esc($_GET['rd']);
}
if ($lgUser) {
    $pix->redirect(
        $pix->adminURL . $rd
    );
}
loadStyle('pages/login');
loadScript('pages/login');
?>
<div class="eg-logo">
    <img src="<?php echo $pix->adminURL; ?>assets/images/evergreen-logo.png">
</div>
<div class="login-page">
    <div class="login-form">
        <div class="form-box">
            <div class="logo">
                <img src="<?php echo $pix->adminURL; ?>assets/images/ncnw-logo-new.png">
            </div>
            <form action="<?php echo ADMINURL, 'actions/public/'; ?>" method="post" id="loginForm">
                <input type="hidden" name="method" value="login" />
                <input type="hidden" name="rd" value="<?php echo $rd; ?>" />
                <div class="field">
                    <input type="text" placeholder="Username / E-mail" name="uname" data-type="string">
                </div>
                <div class="field">
                    <input type="password" name="pass" placeholder="Password" data-type="string" />
                </div>
                <div class="field">
                    <button class="pix-btn site lg" name="login" type="submit">
                        <em class="fa fa-lock"></em>
                        LOGIN
                    </button>
                </div>
            </form>
            <div class="lg-fgpass pt30">
                <a href="<?php echo $pix->adminURL; ?>?page=forgot-password">
                    Forgot Password
                </a>
            </div>
        </div>
    </div>
</div>