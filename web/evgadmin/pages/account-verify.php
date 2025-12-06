<?php
$vfnPg = str2url($_GET['sec'] ?? '');
if ($vfnPg) {
    $vfnPgCode = $pix->basedir . "pages/account-verify/$vfnPg.php";
    if (is_file($vfnPgCode)) {
        include_once $vfnPgCode;
    }
}
