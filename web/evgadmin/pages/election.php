<?php
if ($pix->canAccess('elect')) {
    $subPage = str2url($_GET['sec'] ?? '');
    if (!$subPage) {
        $subPage = 'list';
    }
    $subPageCode = dirname(__FILE__) . "/election/$subPage.php";
    if (is_file($subPageCode)) {
        include $subPageCode;
    }
} else {
    AccessDenied();
}
