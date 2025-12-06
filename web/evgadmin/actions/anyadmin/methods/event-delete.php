<?php
if (!$pix->canAccess('events')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_REQUEST;

if (isset($_['id'])) {
    $id = esc($_['id']);

    if ($id) {
        $event = $pixdb->getRow('events', ['id' => $id], 'image');
        if ($event) {
            if ($event->image) {
                $pix->cleanThumb(
                    'events',
                    $pix->uploads . 'events/' . $event->image,
                    true
                );
            }

            $pixdb->delete('events', ['id' => $id]);

            $pix->addmsg('Event removed!', 1);
            $pix->redirect('?page=events');
        }
    }
}
