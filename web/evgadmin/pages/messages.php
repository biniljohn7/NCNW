<?php
if ($pix->canAccess('messages')) {
    $subPage = str2url($_GET['sec'] ?? '');
    if (!$subPage) {
        $subPage = 'messages';
    }
    $subPageCode = dirname(__FILE__) . "/messages/$subPage.php";
    if (is_file($subPageCode)) {
        include $subPageCode;
    }
} else {
    AccessDenied();
}
