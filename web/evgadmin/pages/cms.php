<?php
if ($pix->canAccess('cms-pages')) {
    $subPage = str2url($_GET['sec'] ?? '');
    if (!$subPage) {
        $subPage = 'list';
    }
    $subPageCode = dirname(__FILE__) . "/cms/$subPage.php";
    if (is_file($subPageCode)) {
        include $subPageCode;
    }
} else {
    AccessDenied();
}
