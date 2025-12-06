<?php
$_ = $_REQUEST;

if (
    isset(
        $_['id'],
        $_['type']
    )
) {
    $id = esc($_['id']);
    $type = ucfirst(esc($_['type']));

    if ($id && $type) {

        if (
            (
                $type == 'Benefit' &&
                !$pix->canAccess('benefit')
            ) ||
            (
                $type == 'Event' &&
                !$pix->canAccess('events')
            )
        ) {
            $pix->addmsg('Access denied!');
            $pix->redirect();
        }

        $data = $pixdb->get(
            'categories',
            [
                'id' => $id,
                'single' => 1
            ],
            'type,itryIcon'
        );
        if (
            $data &&
            $data->type == $type
        ) {
            if ($data->itryIcon) {
                $pix->cleanThumb(
                    'category-icon',
                    $pix->uploads . 'category-image/' . $data->itryIcon
                );
            }

            // clean benefit categories
            if ($type == 'Benefit') {
                $pixdb->update(
                    'benefits',
                    ['ctryId' => $id],
                    ['ctryId' => null]
                );
            }

            // clean event categories
            if ($type == 'Event') {
                $pixdb->update(
                    'events',
                    ['category' => $id],
                    ['category' => null]
                );
            }

            $pixdb->delete(
                'categories',
                ['id' => $id]
            );

            if ($data->type == 'Benefit') {
                $pix->addmsg('Benefit category deleted successfully.', 1);
                $pix->redirect('?page=ctry-benefit');
            } else {
                $pix->addmsg('Event category deleted successfully.', 1);
                $pix->redirect('?page=ctry-event');
            }
        }
    }
}
// exit;
