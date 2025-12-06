<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if (
    isset(
        $pl->name,
        $pl->email,
        $pl->description
    )
) {
    $name = esc($pl->name);
    $email = esc($pl->email);
    $description = esc($pl->description);

    if (
        $name != '' &&
        is_mail($email) &&
        $description != ''
    ) {
        $iid = $pixdb->insert(
            'enquiries',
            array(
                'name' => substr($name, 0, 100),
                'email' => $email,
                'description' => $description,
                'date' => $datetime
            )
        );
        if ($iid) {
            $r->success = 1;
            $r->status = 'ok';
            $r->message = 'Enquiry send successfully.';

            $evg->postNotification(
                'admin',
                false,
                'new-enquiry',
                'New Enquiry',
                "$name posted an enquiry",
                ['enquiry' => $iid]
            );
        }
    }
}
