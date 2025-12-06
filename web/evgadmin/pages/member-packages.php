<?php
if ($pix->canAccess('paid-plans')) {
    (function ($pix, $pixdb, $evg) {
        $subPage = str2url($_GET['sec'] ?? '');
        if (!$subPage) {
            $subPage = 'list';
        }
        $subPageCode = dirname(__FILE__) . "/member-packages/$subPage.php";
        if (is_file($subPageCode)) {
            include $subPageCode;
        }
    })($pix, $pixdb, $evg);
} else {
    AccessDenied();
}
