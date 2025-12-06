<?php
include_once '../lib/lib.php';

$today = date('Y-m-d', strtotime('today'));
$tomorrow = date('Y-m-d', strtotime('tomorrow'));

$dues = $pixdb->get(
    [
        ['memberships', 's', 'member'],
        ['members', 'm', 'id']
    ],
    [
        '#QRY' => "expiry>=$today and expiry<=$tomorrow",
        'payStatus' => 'completed'
    ],
    'm.email,
    m.firstName,
    m.lastName,
    m.memberId,
    s.installment,
    s.payStatus,
    s.instllmntPhase,
    s.expiry'
)->data;

foreach ($dues as $row) {
    $mArgs = [
        'NAME' => "$row->firstName $row->lastName",
        'DUE' => date('F jS, Y', strtotime($row->expiry)),
        'LINK' => $pix->appDomain . 'dues'
    ];

    if (
        $row->installment &&
        $row->payStatus == 'pending' &&
        $row->instllmntPhase < $row->installment
    ) {
        $mArgs['BODYTXT'] = 'We would like to remind you that your upcoming installment is due on ' . date('F jS, Y', strtotime($row->expiry)) . '.';
    } else {
        $mArgs['BODYTXT'] = 'Please note that your current membership will expire on ' . date('F jS, Y', strtotime($row->expiry)) . ', 
        and we invite you to renew it at your earliest convenience.';
    }

    $pix->emailQueue(
        [
            $row->email,
            'Membership Payment Due Notification',
            'mmbrshp-reminder',
            $mArgs
        ]
    );
}

echo 'Sent dues reminders to ' . count($dues) . ' members';
