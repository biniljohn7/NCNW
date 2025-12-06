<?php
if ($pix->canAccess('roles')) {
    $subPage = str2url($_GET['sec'] ?? '');
    if (!$subPage) {
        $subPage = 'list';
    }
    $subPageCode = dirname(__FILE__) . "/leaders-state/$subPage.php";
    if (is_file($subPageCode)) {
        include $subPageCode;
    }
} else {
    AccessDenied();
}
