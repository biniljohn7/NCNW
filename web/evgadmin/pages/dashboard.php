<?php
if ($pix->canAccess('statistics')) {
    loadStyle('pages/dashboard');
    loadScript('pages/dashboard');
    if (!$lgUser) {
        $pix->redirect(
            '?page=login&rd=' .
                urlencode(
                    str_replace(
                        $pix->adminURL,
                        '',
                        $pix->reqURI
                    )
                )
        );
    }
    $adminData = false;

    if ($isAnyAdmin) {
        $nedLivFlg = 1;
        $adminData = $pix->getData("dashboard/admin");
        if (!is_object($adminData)) {
            $adminData = new stdClass();
        }
        if (isset($adminData->exp)) {
            $ct = strtotime(date('Y-m-d H:i:s'));

            if ($adminData->exp - $ct > 0) {
                $nedLivFlg = 0;
            }
        }

        if ($nedLivFlg) {
            $adminData = $pixdb->fetch(
                'SELECT
                (SELECT SUM(amount) FROM transactions) AS revenue,
                (SELECT COUNT(1) FROM members_referrals) AS referrals,
                (SELECT COUNT(DISTINCT planId) FROM memberships) AS counts,
                (SELECT COUNT(pt4use) FROM members_referrals) AS ponits,
                (SELECT COUNT(1) FROM members WHERE `enabled` = "Y") AS members,
                (SELECT COUNT(1) FROM members WHERE  `enabled` = "Y" AND `verified` = "N") AS unVerified'
            );

            $adminData->exp = strtotime(date('Y-m-d H:i:s') . " +1 hour");
            $pix->setData('dashboard/admin', $adminData);
        }
    }
    $showCnt = function ($title, $val, $icn, $isAmt = false) {
        $toK = function ($amt) {
            $f = 0;
            if ($amt >= 10000) {
                $amt = ($amt / 1000);
                $f = 1;
            }
            return ('$' . money($amt, true, true) . ($f ? 'K' : ''));
        };

        echo '<div class="cunts">
            <div class="cunt-hed">'
            . $title .
            '</div>
            <div class="cunt-btm">
                <div class="cnt-digits">'
            . ($isAmt ? $toK($val) : $val) .
            '</div>
                <div class="cnt-icn">
                    <span class="material-symbols-outlined icn">'
            . $icn .
            '</span>
                </div>
            </div>
        </div>';
    };
?>
    <div class="dashboard-wrap">
        <div class="dashboard-top">
            <div class="dash-hed">
                Dashboard
            </div>
            <?php
            breadcrumbs(
                [
                    'Dashboard',
                    null
                ]
            );
            ?>
        </div>
        <div class="dashboard-counts">
            <?php
            $showCnt('Revenue', $adminData->revenue ?? 0, 'currency_exchange', true);
            $showCnt('Total Referrals', $adminData->referrals ?? 0, 'temp_preferences_eco');
            $showCnt('Activated Plans', $adminData->counts ?? 0, 'card_membership');
            $showCnt('Points Used', $adminData->ponits ?? 0, 'timeline');
            $showCnt('Members', $adminData->members ?? 0, 'groups');
            $showCnt('Un-Verified Members', $adminData->unVerified ?? 0, 'unpublished');
            ?>
        </div>
        <div class="dash-cards">
            <?php
            include $pix->basedir . 'pages/dashboard/revenue.php';
            include $pix->basedir . 'pages/dashboard/members-graph.php';
            include $pix->basedir . 'pages/dashboard/recent-enquiries.php';
            include $pix->basedir . 'pages/dashboard/recent-transactions.php';
            include $pix->basedir . 'pages/dashboard/recent-members.php';
            include $pix->basedir . 'pages/dashboard/membership-plans.php';
            include $pix->basedir . 'pages/dashboard/notifications.php';
            ?>
        </div>
    </div>
<?php
} else {
    AccessDenied();
}
?>