<?php
$r = new stdClass();
$giftList = (object)[
    "giftedBy"              =>  "Albert John",
    "giftedOn"              =>  "2024-10-22",
    "membershipPlan"        =>  "Regular Membership",
    "MembershipValidity"    =>  "1 Year",
];

$r->success =   1;
$r->status  =   'ok';
$r->message =   'Gifts loaded!';
$r->data    =   $giftList;
print_r($r);
exit;
