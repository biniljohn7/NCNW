<?php
require_once '../lib/lib.php';

$r = new stdClass();
$r->status = 'error';
$_ = $_REQUEST;

if (isset($_REQUEST['method'])) {
    $method = str2url($_REQUEST['method']);
    if ($method) {
        $incMethod = $pix->basedir . 'ajax/methods/' . $method . '.php';
        if (is_file($incMethod)) {
            include $incMethod;
        }
    }
}

$pix->json($r);
