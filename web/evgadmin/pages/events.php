<?php
if ($pix->canAccess('events')) {
    (function ($pix, $pixdb, $evg) {
        $subPage = str2url($_GET['sec'] ?? '');
        if (!$subPage) {
            $subPage = 'list';
        }
        $subPageCode = dirname(__FILE__) . "/events/$subPage.php";
        if (is_file($subPageCode)) {
            include $subPageCode;
        }
    })($pix, $pixdb, $evg);
} else {
    AccessDenied();
}
