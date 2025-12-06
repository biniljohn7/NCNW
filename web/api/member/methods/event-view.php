<?php
if (isset($_GET['id'])) {
    $evId = esc($_GET['id']);

    if ($evId) {
        $event = $pixdb->getRow(
            'events',
            ['id' => $evId],
            'name as title,
            descrptn as description,
            scope,
            date,
            enddate,
            enabled,
            image,
            address'
        );

        if (
            $event &&
            $event->enabled == 'Y'
        ) {
            $event->date = date('l, F j, Y g:ia', strtotime($event->date));
            $event->enddate = $event->enddate ? date('l, F j, Y g:ia', strtotime($event->enddate)) : null;
            $event->scope = ucfirst($event->scope);
            if ($event->image) {
                $event->image = $pix->uploadPath . 'events/' . $pix->thumb($event->image, 'w750');
            }

            $r->status = 'ok';
            $r->success = 1;
            $r->message = 'Viewed Successfully!';
            $r->data = $event;
        }
    }
}
