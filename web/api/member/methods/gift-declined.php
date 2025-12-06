<?php
devMode();

$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if(
    isset(
        $pl->giftId,
        $pl->reason
    )
) {
    $id = esc($pl->giftId);
    $reason = strtolower($pl->reason) == 'other' ? esc($pl->additionalReason) : esc($pl->reason);

    if(
        $id &&
        $reason
    ) {
        $pixdb->update(
            'memberships',
            ['id' => $id],
            [
                'accepted' => 'N',
                'acceptedOn' => $date,
                'decReason' => $reason
            ]
        );

        $r->status = 'ok';
        $r->success = 1;
        $r->giftId = $id;
        $r->message = 'Gift declined successfully';
    }
}

// exit;
?>