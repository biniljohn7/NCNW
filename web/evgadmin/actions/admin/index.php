<?php
require_once '../../lib/lib.php';
$pix->addmsg();

$_ = $_REQUEST;
if (isset($_['method'])) {
    $method = str2url($_['method'], '\/');
    if ($method) {
        $actionCodeFile = dirname(__FILE__) . "/methods/$method.php";
        if (is_file($actionCodeFile)) {
            $lgUser = $pix->getLoggedUser();
            if (
                $lgUser &&
                $lgUser->type == 'admin'
            ) {
                include_once $actionCodeFile;
            }
        }
    }
}
$pix->redirect();
