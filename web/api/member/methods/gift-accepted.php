<?php
devMode();

$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if (isset($pl->id)) {
    $id = esc($pl->id);

    if ($id) {
        $activeMembership = $pixdb->getRow(
            'memberships',
            [
                'member' => $lgUser->id,
                'enabled' => 'Y',
                '#QRY' =>  '(expiry >= ' . q($date) . ' OR expiry IS NULL)'
            ]
        );

        if (!$activeMembership) {
            $validGift = $pixdb->getRow(
                'memberships',
                [
                    'id' => $id,
                    'member' => $lgUser->id
                ],
                'expiry'
            );
            if ($validGift) {
                $enabled = $validGift->expiry > $date || $validGift->expiry == null;
                $pixdb->update(
                    'memberships',
                    ['id' => $id],
                    [
                        'accepted' => 'Y',
                        'enabled' => $enabled ? 'Y' : 'N',
                        'acceptedOn' => $date
                    ]
                );
                if ($enabled) {
                    $r->data->loginData = (object)[
                        'membershipExpireDate' => $validGift->expiry,
                        'membershipStatus' => 'active'
                    ];
                }

                $r->status = 'ok';
                $r->success = 1;
                $r->message = 'Gift accepted successfully';
            } else {
                $r->message = 'Invalid request';
            }
        } else {
            $r->message = 'You cannot proceed because an active membership already exists for you.';
        }
    }
}

// exit;
