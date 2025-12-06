<?php
$r->success = 1;
$r->status = 'ok';
$r->message = 'Data Retrieved Successfully!';

$mbrInfo = $pixdb->get(
    'members_info',
    [
        'member' => $lgUser->id,
        'single' => 1
    ],
    'refcode, pointsBalance'
);
$rflCode = $mbrInfo->refcode ?? null;
while (
    !$rflCode ||
    (
        $rflCode &&
        $pixdb->get(
            'members_info',
            [
                'refcode' => $rflCode,
                '#QRY' => 'member!=' . $lgUser->id,
                'single' => 1
            ],
            'member'
        )
    )
) {
    var_dump('regenerating...');
    $rflCode = $pix->makestring(8, 'un');
}
// store generated code
if (($mbrInfo->refcode ?? '') != $rflCode) {
    var_dump('updating..');
    $pixdb->insert(
        'members_info',
        [
            'member' => $lgUser->id,
            'refcode' => $rflCode
        ],
        true
    );
}

$refSharedList = $pixdb->get(
    [
        ['members', 'm', 'id'],
        ['members_referrals', 'r', 'user']
    ],
    [
        'r.refBy' => $lgUser->id,
        '#SRT' => 'm.id desc'
    ],
    'm.firstName,
    m.lastName,
    m.regOn as createdAt,
    r.pt4share as referralCodeSharingPoints'
)->data;
foreach ($refSharedList as $usr) {
    $usr->name = $usr->firstName . ' ' . $usr->lastName;
    $usr->referralCodeSharingPoints = intval($usr->referralCodeSharingPoints);
    unset(
        $usr->firstName,
        $usr->lastName
    );
}


$refby = $pixdb->get(
    [
        ['members', 'm', 'id'],
        ['members_referrals', 'r', 'refBy']
    ],
    [
        'r.user' => $lgUser->id,
        '#SRT' => 'm.id desc',
        'single' => 1
    ],
    'm.firstName,
    m.lastName,
    r.refCode as usedReferralCode,
    r.pt4use as referralCodeUsingPoints'
);

$refCodeUsdList = [];
if ($refby) {
    $refby->name = $refby->firstName . ' ' . $refby->lastName;
    $refby->referralCodeUsingPoints = intval($refby->referralCodeUsingPoints);
    $refby->createdAt = $lgUser->regOn;
    unset(
        $refby->firstName,
        $refby->lastName
    );
    $refCodeUsdList[] = $refby;
}

$r->data = (object)[
    'referralCode' => $rflCode,
    'referralCodeSharedList' => $refSharedList,
    'referralCodeUsedList' => $refCodeUsdList,
    'earnedPoints' => floatval($mbrInfo->pointsBalance ?? 0),
];
