<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['name'],
        $_['nation']
    )
) {
    $name = ucwords(esc($_['name']));
    $status = isset($_['status']) ? 'Y' : 'N';
    $nation = esc($_['nation']);
    $rid = esc($_['rid'] ?? '');
    $new = !$rid;

    if (
        $name &&
        $nation
    ) {
        $data = false;
        if ($rid) {
            $data = $pixdb->get(
                'regions',
                [
                    'id' => $rid,
                    'single' => 1
                ],
                'id'
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
                $pixdb->getRow('nations', ['id' => $nation], 'id')
            ) {
                $exRegion = $pixdb->getRow(
                    'regions',
                    ['name' => $name, 'nation' => $nation],
                    'id'
                );
                if (
                    $exRegion &&
                    $exRegion->id == $rid
                ) {
                    $exRegion = false;
                }
                if (!$exRegion) {
                    $dbData = [
                        'nation' => $nation,
                        'name' => $name,
                        'enabled' => $status
                    ];
                    if ($new) {
                        $dbData['createdAt'] = $datetime;
                        $iid = $pixdb->insert(
                            'regions',
                            $dbData
                        );
                    } else {
                        $iid = $rid;
                        $dbData['updatedAt'] = $datetime;
                        $pixdb->update(
                            'regions',
                            [
                                'id' => $iid
                            ],
                            $dbData
                        );
                    }
                    if ($iid) {
                        $pix->addmsg('Region saved', 1);
                        $pix->redirect('?page=region');
                    }
                } else {
                    $pix->addmsg('Region already exist');
                }
            } else {
                $pix->addmsg('Country does not exist');
            }
        }
    }
}
