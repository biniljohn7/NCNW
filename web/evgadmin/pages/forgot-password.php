<?php
$rd = '';
if (isset($_GET['rd'])) {
    $rd = escape($_GET['rd']);
}

// redirect if logged
if ($lgUser) {
    $pix->redirect(
        $pix->adminURL . $rd
    );
}
$section = str2url($_GET['sec'] ?? '');
if (!$section) {
    $section = 'password-request';
}

$pages = $pix->basedir . 'pages/forgot-password/' . $section . '.php';
if (is_file($pages)) {
    include_once($pages);
}
