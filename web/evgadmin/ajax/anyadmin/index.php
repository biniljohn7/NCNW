<?php
require_once '../../lib/lib.php';

$r = new stdClass();
$r->status = 'error';
$_ = $_REQUEST;

if (isset($_['method'])) {
    $method = str2url($_['method']);
    if ($method) {
        $incMethod = dirname(__FILE__) . "/methods/$method.php";
        if (is_file($incMethod)) {
            $lgUser = $pix->getLoggedUser();
            if (
                $lgUser &&
                (
                    $lgUser->type == 'admin' ||
                    $lgUser->type == 'sub-admin' ||
                    $lgUser->type == 'section-president' ||
                    $lgUser->type == 'section-leader' ||
                    $lgUser->type == 'state-leader'
                )
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
