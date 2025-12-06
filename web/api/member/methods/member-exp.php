<?php
if ($lgUser->id) {
    $membershipInfo = $pixdb->get(
        'memberships',
        [
            'member' => $lgUser->id,
            'single' => 1
        ]
    );
    $r->success = 1;
    $r->status = 'ok';
    $r->message = 'expiry data received';
    $r->data = ($membershipInfo && $membershipInfo->expiry !== null)
    ? ($date > $membershipInfo->expiry)
    : null;
}
