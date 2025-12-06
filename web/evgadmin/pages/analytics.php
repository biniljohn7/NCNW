<?php
if ($pix->canAccess('statistics')) {
    loadStyle('pages/analytics');
    loadScript('pages/analytics');
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
                (SELECT COUNT(1) FROM members) AS members,
                (SELECT COUNT(1) FROM members WHERE `verified` = "N") AS unVerified'
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
    <div class="analytics-wrap">
        <div class="analytics-top">
            <div class="analytics-hed">
                Analytics
            </div>
            <?php
            breadcrumbs(
                [
                    'Analytics',
                    null
                ]
            );
            ?>
        </div>
        <div class="analytics-counts">
            <?php
            $showCnt('Members', $adminData->members ?? 0, 'groups');
            $showCnt('Activated Plans', $adminData->counts ?? 0, 'card_membership');
            $showCnt('Total Referrals', $adminData->referrals ?? 0, 'temp_preferences_eco');
            $showCnt('Points Used', $adminData->ponits ?? 0, 'timeline');
            $showCnt('Revenue', $adminData->revenue ?? 0, 'currency_exchange', true);
            $showCnt('Un-Verified Members', $adminData->unVerified ?? 0, 'unpublished');
            ?>
        </div>
        <div class="analytics-cards">
            <?php
            // include $pix->basedir . 'pages/analytics/top-referrals.php';
            include $pix->basedir . 'pages/analytics/quick-links.php';
            include $pix->basedir . 'pages/analytics/top-earned-plan.php';
            include $pix->basedir . 'pages/analytics/top-signed-advocacy.php';
            include $pix->basedir . 'pages/analytics/upcoming-events.php';
            include $pix->basedir . 'pages/analytics/nation-revenue.php';
            include $pix->basedir . 'pages/analytics/recent-members.php';
            include $pix->basedir . 'pages/analytics/nations.php';
            include $pix->basedir . 'pages/analytics/regions.php';
            include $pix->basedir . 'pages/analytics/states.php';
            include $pix->basedir . 'pages/analytics/chapters.php';

            ?>
        </div>
    </div>
<?php
}
?>