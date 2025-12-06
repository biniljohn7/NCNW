<?php
$r->success = 1;
$r->status = 'ok';
$r->message = 'Data Retrieved Successfully!';

$mship = $pixdb->get(
    'memberships',
    [
        'member' => $lgUser->id,
        'enabled' => 'Y',
        '#QRY' => "(expiry >= '$date' OR expiry IS NULL)",
        'single' => 1
    ],
    'expiry, planName'
);

$r->data  = [
    'fullName' => html_entity_decode($lgUser->firstName . ' ' . $lgUser->lastName, ENT_QUOTES, 'UTF-8'),
    'membershipNumber' => $lgUser->memberId,
    'membershipName' => $mship->planName ?? '--',
    'status' => $mship ? 
    (
        empty($mship->expiry) || strtotime($mship->expiry) >= time() 
            ? 'Active' 
            : 'Expired'
    ) 
    : 'Inactive',
    'validThru' => $mship && $mship->expiry ? $mship->expiry . ' 23:59:59' : false
];

// if (isset($_GET['debug'])) {
//     devMode();
//     prettyJson($r);
//     exit;
// }
