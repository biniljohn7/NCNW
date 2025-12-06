<?php
require_once '../../evgadmin/lib/lib.php';

$r = new stdClass();
$r->status = 'error';
$r->success = 0;
$r->data = (object)[];;
$r->message = 'Invalid request. Please try again.';

$_ = $_REQUEST;

if (isset($_REQUEST['method'])) {
    $method = str2url($_REQUEST['method']);
    if ($method) {
        $incMethod = dirname(__FILE__) . '/methods/' . $method . '.php';
        if (is_file($incMethod)) {

            function getJsonBody()
            {
                $body = file_get_contents('php://input');
                if ($body) {
                    $body = json_decode($body);
                }
                return is_object($body) ?
                    $body :
                    (object)[];
            }
            include $incMethod;
        } else {
            $r->message = 'Method not found!';
        }
    } else {
        $r->message = 'Method cannot be empty!';
    }
} else {
    $r->message = 'Method is required!';
}

$pix->json($r);
