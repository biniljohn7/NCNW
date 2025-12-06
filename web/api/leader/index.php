<?php
$noSession = 1;
require_once '../../evgadmin/lib/lib.php';

$r = new stdClass();
$r->status = 'error';
$r->success = 0;
$r->data = (object)[];
$r->message = 'Invalid request. Please try again.';

$_ = $_REQUEST;

if (isset($_REQUEST['method'])) {
    $method = str2url($_REQUEST['method']);
    if ($method) {
        $incMethod = dirname(__FILE__) . "/methods/$method.php";
        if (is_file($incMethod)) {
            $apcHeaders = apache_request_headers();

            $accessToken = esc(
                ($apcHeaders['access-token'] ?? '') ?: ($apcHeaders['Access-Token'] ?? '')
            );

            // obtain token from GET
            if (!$accessToken && isset($_GET['token'])) {
                $accessToken = esc($_GET['token']);
            }

            if ($accessToken) {
                $authUser = $pixdb->get(
                    [
                        ['members_auth', 'ma', 'id'],
                        ['members', 'mm', 'id']
                    ],
                    [
                        'ma.token' => $accessToken,
                        'single' => 1
                    ],
                    'mm.*'
                );
                if ($authUser) {
                    if ($authUser->role) {
                        unset($authUser->password);
                        $lgUser = $authUser;

                        $userRoles = explode(',', $authUser->role);
                        $isStateLeader = in_array('state-leader', $userRoles);
                        $isSectionLeader = in_array('section-leader', $userRoles);
                        $isSectionPresident = in_array('section-president', $userRoles);
                        $isAffiliateLeader = in_array('affiliate-leader', $userRoles);
                        $isCollegiateLeader = in_array('collegiate-leaders', $userRoles);
                        $isOfficer = in_array('section-officer', $userRoles);

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
                        $r->message = 'Access Denied!';
                    }
                } else {
                    $r->message = 'Invalid authentication!';
                    $pix->unauthApiReqRes();
                }
            } else {
                $r->message = 'Authentication token cannot be empty!';
            }
        } else {
            $r->message = 'Method not found!' . $incMethod;
        }
    } else {
        $r->message = 'Method cannot be empty!';
    }
} else {
    $r->message = 'Method is required!';
}

if (isset($_REQUEST['dev'])) {
    devMode();
    echo prettyJson($r);
    exit;
}

$pix->json($r);
