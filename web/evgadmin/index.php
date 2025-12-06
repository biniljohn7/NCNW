<?php
include 'lib/lib.php';
require_once 'includes/layout-models.php';
require_once 'lib/page-head.php';

$lgUser = $pix->getLoggedUser();

// user roles
$isSuperAdmin = $lgUser && $lgUser->type == 'admin';
$isSubAdmin = $lgUser && $lgUser->type == 'sub-admin';
$isAnyAdmin = $isSuperAdmin || $isSubAdmin;

require_once 'includes/nav-links.php';

$page = '';
if (isset($_GET['page'])) {
    $page = str2url($_GET['page']);
}
if (!$page && $lgUser) {
    $ldPage = 0;
    foreach ($menus as $mgp) {
        if ($mgp) {
            $mgp['items'] = array_filter($mgp['items']);
            foreach ($mgp['items'] as $itm) {
                parse_str($itm[0], $linkArgs);
                $ldPage = $linkArgs['?page'] ?? '';
                if ($ldPage) {
                    break;
                }
            }
        }
        if ($ldPage) {
            break;
        }
    }
    if ($ldPage) {
        $page = $ldPage;
    }
    if (!$page) {
        $page = 'dashboard';
    }
}

$nonAuthPages = [
    'login',
    'forgot-password',
    'account-verify'
];
if (array_search($page, $nonAuthPages) === false && !$lgUser) {
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pgHead->pageTitle; ?></title>
    <link rel="shortcut icon" href="<?php echo ADMINURL; ?>assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,800;0,900;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <script type="text/javascript">
        var domain = '<?php echo $pix->adminURL; ?>';
    </script>
    <?php
    loadStyle('common/default');
    loadStyle('common/jquery-ui');

    loadScript('common/jquery-3.7.1.min');
    loadScript('common/jquery-ui');
    loadScript('common/formchecker');
    loadScript('common/layout-models');
    loadScript('common/site-default');
    ?>
</head>

<body>
    <?php
    $pix->getmsg();
    ?>
    <?php
    if ($lgUser) {
    ?>
        <div class="header">
            <div class="mob-menu-btn" id="mobMenuBtn">
                <span class="material-symbols-outlined">
                    menu
                </span>
            </div>
            <div class="nav-logo">
                <img src="<?php echo ADMINURL; ?>assets/images/logo.png">
            </div>
            <div class="nav-logo-txt">NCNW</div>
            <div class="nav-usr">
                <?php
                if ($pix->canAccess('helpdesk/ncnw-team')) {
                    $sub = str2url($_GET['sec'] ?? '');
                ?>
                    <a href="<?php echo ADMINURL; ?>?page=requests&sec=mod&id=new&refpage=<?php echo $page, $sub ? '&refsec=' . $sub : ''; ?>" class="report-btn">Report an issue</a>
                <?php } ?>
                <a href="<?php echo $pix->appDomain; ?>signin" class="usr-nv-btn" target="_blank">
                    Go to Main Site
                    <?php /* <span class="site-icon"></span> */ ?>
                </a>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="page-body">
        <?php
        include $pix->basedir . 'includes/account-nav.php';
        ?>
        <div class="acc-body">
            <?php
            $pageRoot = $pix->basedir . 'pages/' . $page . '.php';
            if (is_file($pageRoot)) {
                include $pageRoot;
            }
            ?>
        </div>
    </div>
</body>

</html>