<?php
$inp = getJsonBody();
if (
    isset(
        $inp->prefix,
        $inp->firstName,
        $inp->lastName,
        $inp->email,
        $inp->address,
        $inp->city,
        $inp->state,
        $inp->country,
        $inp->zipcode,
        $inp->section,
        $inp->affilation
    )
) {
    $inp->prefix = esc($inp->prefix);
    $inp->firstName = esc($inp->firstName);
    $inp->lastName = esc($inp->lastName);
    $inp->email = esc($inp->email);
    $inp->address = esc($inp->address);
    $inp->city = esc($inp->city);
    $inp->state = esc($inp->state);
    $inp->country = esc($inp->country);
    $inp->zipcode = esc($inp->zipcode);
    $inp->phone = have($inp->phone);
    $inp->section = esc($inp->section);
    $inp->affiliation = is_array($inp->affilation) ? array_unique(array_filter(array_map('esc', $inp->affilation))) : [];
    $inp->createdBy = $lgUser->id;

    $res = $evg->addMember($inp);

    if ($res->status == 'ok') {
        $r->status = 'ok';
        $r->success = 1;
        $r->data = $res->data;
        $r->message = $res->message;
    } else {
        $r->message = $res->message;
    }
}
