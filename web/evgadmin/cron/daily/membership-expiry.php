<?php
include_once '../lib/lib.php';

$today = date('Y-m-d', strtotime('today'));

$pixdb->update(
    'memberships',
    [
        '#QRY' => "expiry<='$today' and expiry IS NOT null"
    ],
    [
        'enabled' => 'N'
    ],
    'member'
);
echo 'Checked for expired memberships';
