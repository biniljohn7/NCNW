<?php
require_once '../../lib/lib.php';

$r = new stdClass();
$r->status = 'error';
$_ = $_REQUEST;

if (isset($_['method'])) {
    $method = str2url($_['method']);
    if ($method) {
        $incMethod = $pix->basedir . 'ajax/admin/methods/' . $method . '.php';
        if (is_file($incMethod)) {
            $lgUser = $pix->getLoggedUser();
            if (
                $lgUser &&
                $lgUser->type == 'admin'
            ) {
                include_once $incMethod;
            } else {
                $r->errorMsg = 'User not logged';
            }
        } else {
            $r->errorMsg = 'Unknown method';
        }
    } else {
        $r->errorMsg = 'Method is empty';
    }
} else {
    $r->errorMsg = 'Method is missing';
}

$pix->json($r);
