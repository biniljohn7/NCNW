<?php
if ($pix->canAccess('transactions')) {
    $subPage = str2url($_GET['sec'] ?? '');
    if (!$subPage) {
        $subPage = 'list';
    }
    $subPageCode = dirname(__FILE__) . "/transactions/$subPage.php";
    if (is_file($subPageCode)) {
        include $subPageCode;
    }
} else {
    AccessDenied();
}
