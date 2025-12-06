<?php
(function ($pix, $pixdb, $evg, $r, $authUser) {
    $_ = $_POST;
    $inp = getJsonBody();

    if(
        isset(
            $inp->option,
            $inp->chkStatus
        )
    ) {
        $option = esc($inp->option);
        $status = $inp->chkStatus == 1 ? true : false;

        $validOptions = [
            'yaca-youth',
            'bhcp',
            'girlcon',
            'rise',
            'ghwins',
            'colgate',
            'advocacy-policy',
            'programs',
            'email',
            'text',
            'social-media'
        ];

        if(
            $option &&
            in_array($option, $validOptions, true)
        ) {
            $exNoti = $pixdb->getRow(
                'members',
                ['id' => $authUser->id],
                'id, notifications'
            );

            $notiArr = !empty($exNoti->notifications)
                ? explode(',', $exNoti->notifications)
                : [];

            if ($status) {
                $notiArr[] = $option;
            } else {
                $notiArr = array_diff($notiArr, [$option]);
            }

            $notiArr = array_values(array_unique(array_filter($notiArr)));

            $pixdb->update(
                'members',
                ['id' => $authUser->id],
                ['notifications' => !empty($notiArr) ? implode(',', $notiArr) : null]
            );

            $r->status = 'ok';
            $r->success = 1;
            $r->message = 'Set notification successfully!';
        }
    }
    // exit;
})($pix, $pixdb, $evg, $r, $authUser);
?>