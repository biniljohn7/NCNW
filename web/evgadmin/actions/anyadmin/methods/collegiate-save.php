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
    $name = ucwords(escape($_['name']));
    $status = isset($_['status']) ? 'Y' : 'N';
    $csId = esc($_['csId'] ?? '');
    $new = !$csId;

    if ($name) {
        $data = false;
        if ($csId) {
            $data = $pixdb->get(
                'collegiate_sections',
                [
                    'id' => $csId,
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
            $exCollegiate = $pixdb->getRow(
                'collegiate_sections',
                ['name' => $name],
                'id'
            );
            if (
                $exCollegiate &&
                $exCollegiate->id == $csId
            ) {
                $exCollegiate = false;
            }
            if (!$exCollegiate) {
                $dbData = [
                    'name' => $name,
                    'enabled' => $status
                ];
                if ($new) {
                    $dbData['createdAt'] = $datetime;
                    $iid = $pixdb->insert(
                        'collegiate_sections',
                        $dbData
                    );
                } else {
                    $iid = $csId;
                    $dbData['updatedAt'] = $datetime;
                    $pixdb->update(
                        'collegiate_sections',
                        [
                            'id' => $iid
                        ],
                        $dbData
                    );
                }
                if ($iid) {
                    $pix->addmsg('Collegiate section saved', 1);
                    $pix->redirect('?page=collegiate-sections');
                }
            } else {
                $pix->addmsg('The collegiate section already exist');
            }
        }
    }
}
