<?php
if (!$pix->canAccess('advocacy')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['title'],
        $_['scope'],
        $_['legislator'],
        $_['senator'],
        $_['contact'],
        $_['recipient'],
        $_['recipEmail'],
        $_['address'],
        $_['desc'],
        $_['pdf']
    )
) {
    $title = esc($_['title']);
    $scope = esc($_['scope']);
    $ctry = [];
    if (
        isset($_['ctry']) &&
        is_array($_['ctry'])
    ) {
        $ctry = array_unique(
            array_filter(
                array_map('esc', $_['ctry'])
            )
        );
    }
    $legislator = esc($_['legislator']);
    $senator = esc($_['senator']);
    $contact = esc($_['contact']);
    $recipient = esc($_['recipient']);
    $recipEmail = esc($_['recipEmail']);
    $address = esc($_['address']);
    $desc = esc($_['desc']);
    $pdf = esc(urldecode($_['pdf']));
    $status = isset($_['status']);
    $aid = esc($_['aid'] ?? '');
    $new = !$aid;

    $pdfUpload = isset($_FILES['pdfUpload']) ? $_FILES['pdfUpload'] : null;

    if (
        $title &&
        $scope &&
        $contact &&
        $recipient &&
        $recipEmail &&
        $address
    ) {
        $advData = false;
        if ($aid) {
            $advData = $pixdb->getRow(
                'advocacies',
                [
                    'id' => $aid
                ],
                'id, image'
            );
        }
        $filePath = null;
        if ($pdfUpload['tmp_name']) {
            $dateDir = $pix->setDateDir('advocacy-pdf');
            $fileName = $pix->makestring(50, 'ln') . '.' . exf($pdfUpload['name']);
            if (
                move_uploaded_file(
                    $pdfUpload['tmp_name'],
                    $dateDir->absdir . $fileName
                )
            ) {
                $filePath = $dateDir->uplroot . $fileName;
            };
        }
        if (
            $new ||
            (
                !$new &&
                $advData
            )
        ) {
            $dbData = [
                'title' => $title,
                'enabled' => $status ? 'Y' : 'N',
                'scope' => $scope,
                'locations' => json_encode($ctry),
                'senator' => $senator,
                'legislator' => $legislator,
                'contact' => $contact,
                'recipient' => $recipient,
                'recipAddr' => $address,
                'recipEmail' => $recipEmail,
                'descrptn' => $desc,
                'pdfContent' => $pdf,
                'pdfUpload' => $filePath,
            ];
            if ($new) {
                $dbData['createdAt'] = $datetime;
                $iid = $pixdb->insert(
                    'advocacies',
                    $dbData
                );
            } else {
                $iid = $aid;
                $pixdb->update(
                    'advocacies',
                    [
                        'id' => $iid
                    ],
                    $dbData
                );
            }
            if ($iid) {
                //var_dump(basename(__FILE__) . ':' . __LINE__);
                $pix->addmsg('Advocacy saved', 1);
                $pix->redirect('?page=advocacy&sec=details&id=' . $iid);
            }
        }
    }
}
//exit;
