<?php
$memberships = [];
$mship = $pixdb->get(
    'memberships',
    [
        'member' => $lgUser->id,
        'enabled' => 'Y',
        '#QRY' => '(expiry >= ' . q($date) . ' OR expiry IS NULL)',
        '#SRT' => 'created desc limit 1'
    ]
)->data;
foreach ($mship as $row) {
    $subscriptionStatus = null;
    if ($row->installment && $row->stripeSubscribeId) {
        $subscriptionStatus = loadModule('stripe')->getSubscribeInfo($row->stripeSubscribeId);
    }
    $memberships[] = [
        [
            'label' => $row->planName,
            'amount' => dollar($row->amount)
        ],
        $row->expiry ?
            [
                'label' => 'Validity',
                'amount' => date('d M Y', strtotime($row->expiry))
            ] : null,
        $row->installment ?
            [
                'label' => 'Payment Type',
                'amount' => "$row->installment - payment"
            ] : null,
        [
            'label' => 'Payment Status',
            'amount' => $row->installment ? ($subscriptionStatus ?? 'Subscription status currently unavailable.') : $row->payStatus
        ]
    ];
}

$r->success = 1;
$r->status = 'ok';
$r->data = $memberships;
$r->message = 'Membership details loaded!';
