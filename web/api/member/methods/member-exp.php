<?php
if ($lgUser->id) {
    $membershipInfo = $pixdb->get(
        'memberships',
        [
            'member' => $lgUser->id,
            'enabled' => 'Y',
            '#QRY' => '((giftedBy IS NOT NULL AND accepted = "Y") OR giftedBy IS NULL)',
            '#SRT' => 'id desc',
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
