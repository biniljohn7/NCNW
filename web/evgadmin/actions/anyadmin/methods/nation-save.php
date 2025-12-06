<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['name']
    )
) {
    $name = ucwords(esc($_['name']));
    $status = isset($_['status']) ? 'Y' : 'N';
    $nid = esc($_['nid'] ?? '');
    $new = !$nid;

    if ($name) {
        $data = false;
        if ($nid) {
            $data = $pixdb->get(
                'nations',
                [
                    'id' => $nid,
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
            $exNation = $pixdb->getRow(
                'nations',
                ['name' => $name],
                'id'
            );
            if (
                $exNation &&
                $exNation->id == $nid
            ) {
                $exNation = false;
            }
            if (!$exNation) {
                $dbData = [
                    'name' => $name,
                    'enabled' => $status
                ];
                if ($new) {
                    $dbData['createdAt'] = $datetime;
                    $iid = $pixdb->insert(
                        'nations',
                        $dbData
                    );
                } else {
                    $iid = $nid;
                    $dbData['updatedAt'] = $datetime;
                    $pixdb->update(
                        'nations',
                        [
                            'id' => $iid
                        ],
                        $dbData
                    );
                }
                if ($iid) {
                    $pix->addmsg('Country saved', 1);
                    $pix->redirect('?page=nation');
                }
            } else {
                $pix->addmsg('The country already exist');
            }
        }
    }
}
