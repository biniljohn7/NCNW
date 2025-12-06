<?php
require_once '../lib/lib.php';

// $fcAdmin->addmsg();

$_ = $_REQUEST;
if (isset($_['method'])) {
    $method = str2url($_['method']);
    if ($method != '') {
        $actionCodeFile = 'methods/' . $method . '.php';
        if (
            is_file($actionCodeFile)
        ) {
            include_once $actionCodeFile;
        }
    }
}

$pix->redirect();
