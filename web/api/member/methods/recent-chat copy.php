<?php
$r->status = 'ok';
$r->success = 1;

$rcntChat = $pixdb->get(
    [
        ['message_logs', 'l', 'sender'],
        ['members', 'm', 'id']
    ],
    [
        '#QRY' => 'lastMsg is not null and user=' . $lgUser->id,
        '#SRT' => 'lastMsgOn desc limit 10'
    ],
    'lastMsg as lastMessage,
    firstName,
    lastName,
    m.id as memberId,
    m.avatar'
)->data;

foreach ($rcntChat as $itm) {
    $itm->fullName = $itm->firstName . ' ' . $itm->lastName;
    $itm->profileImage = $itm->avatar ? $pix->avatar($itm->avatar, '150x150', 'avatars') : null;
    unset($itm->firstName, $itm->lastName, $itm->avatar);

    if (preg_match('/^\{msgImg\:/i', $itm->lastMessage)) {
        $itm->lastMessage = '';
    }
}

$r->data = $rcntChat;
$r->message = 'Data Retrieved Successfully!';
