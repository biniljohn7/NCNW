<?php
if ($isSuperAdmin) {
    (function ($pix, $pixdb, $evg, $isSuperAdmin) {
        $subPage = str2url($_GET['sec'] ?? '');
        if (!$subPage) {
            $subPage = 'list';
        }
        $subPageCode = dirname(__FILE__) . "/sub-admins/$subPage.php";
        if (is_file($subPageCode)) {
            include $subPageCode;
        }
    })($pix, $pixdb, $evg, $isSuperAdmin);
} else {
    AccessDenied();
}
