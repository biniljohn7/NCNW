<?php
$inp = getJsonBody();
if (
    isset(
        $inp->firstName,
        $inp->lastName,
        $inp->email,
        $inp->zipcode,
        $inp->section,
        $inp->affiliation
    )
) {
    $inp->firstName = esc($inp->firstName);
    $inp->lastName = esc($inp->lastName);
    $inp->email = esc($inp->email);
    $inp->address = have($inp->address);
    $inp->city = have($inp->city);
    $inp->zipcode = esc($inp->zipcode);
    $inp->phone = have($inp->phone);
    $inp->section = esc($inp->section);
    $inp->affiliation = esc($inp->affiliation);
    $inp->createdBy = $lgUser->id;
    $id = esc($inp->id ?? '');
    $new = !$id;

    if($new) {
        $res = $evg->addMember($inp);
    } else {
        $res = $evg->updateMember($inp);
    }

    if ($res->status == 'ok') {
        $r->status = 'ok';
        $r->success = 1;
        $r->data = $res->data;
        $r->message = $res->message;
    } else {
        $r->message = $res->message;
    }
}
