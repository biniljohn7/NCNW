<?php
if ($pix->canAccess('email-templates')) {
    (function ($pix, $pixdb, $evg) {
        loadStyle('pages/email-templates');
?>
        <h1>
            Email Templates
        </h1>
        <?php
        breadcrumbs(['Email Templates']);
        ?>
        <div class="stt-ui">
            <?php
            $sec = str2url($_GET['sec'] ?? '');
            if (!$sec) {
                $sec = 'advocacy';
            }
            $tabLink = ADMINURL . "?page=email-templates";
            ?>
        </div>

        <div class="mb30">
            <?php
            TabGroup(
                array_filter(
                    [
                        [
                            'campaign',
                            'Advocacy',
                            $tabLink,
                            $sec == 'advocacy'
                        ],
                        [
                            'person',
                            'Account Verification',
                            $tabLink . '&sec=account-verification',
                            $sec == 'account-verification'
                        ],
                        [
                            'social_leaderboard',
                            'Leadership Access Invitation',
                            $tabLink . '&sec=leaders-invite-access',
                            $sec == 'leaders-invite-access'
                        ],
                    ]
                )
            );
            ?>
        </div>

        <?php
        $secPageCode = dirname(__FILE__) . "/email-templates/$sec.php";
        if (is_file($secPageCode)) {
            include $secPageCode;
        }
        ?>

<?php
    })($pix, $pixdb, $evg);
} else {
    AccessDenied();
}
