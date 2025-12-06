<?php
loadStyle('pages/forgot-password/request-status');
?>
<div class="req-status">
    <div class="req-wrap">
        <div class="req-card">
            <div class="req-img">
                <img src="<?php echo ADMINURL; ?>assets/images/req-img.svg" alt="Request Image">
            </div>
            <div class="req-hed text-center bold-800 mb15 text-14">
                CHECK YOUR INBOX
            </div>
            <div class="req-msg text-center bold-500 text-11">
                <?php
                if (isset($_GET['t'])) {
                    $diff = intval($_GET['t']) - time();

                    $r = '<a href="' . $pix->adminURL . '?page=forgot-password">try</a> ';
                    if ($diff > 86400) {
                        $r .= 'after ' . floor($diff / 3600) . ' hr(s)';
                    } else if ($diff > 3600) {
                        $r .= 'after ' . floor($diff / 60) . ' min';
                    } else {
                        $r .= 'again';
                    }
                    echo 'Failed to send account recovery mail.<br/> Please, check your e-mail or ', $r;
                } else {
                    echo 'Verification link sent to your e-mail. Please check your email.';
                }
                ?>
            </div>
        </div>
    </div>
</div>