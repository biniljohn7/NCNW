<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

if (
    isset(
        $pl->contactUsQuestionId,
        $pl->name,
        $pl->subject,
        $pl->description
    )
) {
    $qId = esc($pl->contactUsQuestionId);
    $name = esc($pl->name);
    $subject = esc($pl->subject);
    $description = esc($pl->description);

    if (
        $qId &&
        $name != '' &&
        $subject != '' &&
        $description != ''
    ) {
        $getQuestion = $pixdb->get(
            'enquiry_questions',
            [
                'id' => $qId,
                'single' => 1
            ],
            'question'
        );

        if ($getQuestion) {
            $iid = $pixdb->insert(
                'enquiries',
                array(
                    'memberId' => $lgUser->id,
                    'name' => substr($name, 0, 120),
                    'question' => $getQuestion->question,
                    'subject' => substr($subject, 0, 300),
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
                    $lgUser->id,
                    'new-enquiry',
                    'New Enquiry',
                    "$lgUser->firstName $lgUser->lastName posted an enquiry",
                    ['enquiry' => $iid]
                );
            }
        }
    }
}
