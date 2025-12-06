<?php
if ($pix->canAccess('events')) {
    $subPage = str2url($_GET['sec'] ?? '');
    if (!$subPage) {
        $subPage = 'list';
    }
    $subPageCode = dirname(__FILE__) . "/ctry-event/$subPage.php";
    if (is_file($subPageCode)) {
        include $subPageCode;
    }
} else {
    AccessDenied();
}
