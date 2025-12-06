<?php
$_ = $_POST;

if (
    isset(
        $_['type'],
        $_['name']
    )
) {
    $type = esc($_['type']);
    $name = ucwords(esc($_['name']));
    $status = isset($_['status']) ? 'Y' : 'N';
    $ctryIcon = $_FILES['ctryIcon'] ?? null;
    $bid = esc($_['bid'] ?? '');
    $eid = esc($_['eid'] ?? '');
    $id = $bid ? $bid : $eid;
    $new = !$id;
    $imgRoot = '';

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

    if (
        (
            $type == 'Benefit' ||
            $type == 'Event'
        ) &&
        $name
    ) {
        $data = false;
        if (
            $id
        ) {
            $data = $pixdb->getRow(
                'categories',
                [
                    'id' => $id
                ],
                'id,itryIcon'
            );
        }
        if (
            $new ||
            (
                !$new &&
                $data
            )
        ) {
            if (
                $ctryIcon &&
                isValidImage($ctryIcon)
            ) {
                $dateDir = $pix->setDateDir('category-image');
                $uplphoto = $pix->addIMG($ctryIcon, $dateDir->absdir, 'random', 3500);
                if ($uplphoto) {
                    $absFile = $dateDir->absdir . $uplphoto;
                    $imgRoot = $dateDir->uplroot . $uplphoto;

                    $pix->make_thumb('category-icon', $absFile);

                    if (!$new && $data->itryIcon) {
                        $pix->cleanThumb(
                            'category-icon',
                            $dateDir->upldir . $data->itryIcon
                        );
                    }
                }
            }
            if (!$imgRoot) {
                $imgRoot = $new ? null : $data->itryIcon;
            }
            $newSlug = $pix->get_new_slug(
                $name,
                $id ? $id : false,
                'categories'
            );

            $dbData = [
                'ctryName' => $name,
                'type' => $type,
                'itryIcon' => $imgRoot,
                'enable' => $status,
                'slug' => $newSlug
            ];
            if ($new) {
                $iid = $pixdb->insert(
                    'categories',
                    $dbData
                );
            } else {
                $iid = $id;
                $pixdb->update(
                    'categories',
                    [
                        'id' => $iid
                    ],
                    $dbData
                );
            }
            if ($iid) {
                if ($type == 'Benefit') {
                    $pix->addmsg('Benefit category saved', 1);
                    $pix->redirect('?page=ctry-benefit');
                } elseif ($type == 'Event') {
                    $pix->addmsg('Event category saved', 1);
                    $pix->redirect('?page=ctry-event');
                }
            }
        }
    }
}
// exit;
