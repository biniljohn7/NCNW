<?php
$_ = $_POST;

if (
    isset(
        $_['request'],
        $_FILES['files']
    )
) {
    $request = esc($_['request']);
    $files = $_FILES['files'];
    $name = esc($files['name']);
    $explodeName = explode('.', $name);
    $extension = end($explodeName);

    if (strlen($name) > 100) {
        $name = $pix->makestring(20, 'lu') . '.' . $extension;
    } else {
        $name = substr($name, 0, 100);
    }

    if (
        $request &&
        (
            $hDesk = $pixdb->getRow(
                'help_desk',
                ['id' => $request],
                'id, reqCompletion'
            )
        )
    ) {
        if ($hDesk->reqCompletion) {
            $validDate = 0;
            $reqCompletion = strtotime(
                str_replace('/', '-', $hDesk->reqCompletion)
            );

            if ($reqCompletion >= time()) {
                $validDate = 1;
            } else {
                $reqCompletion = false;
            }
        }

        if (
            $reqCompletion &&
            $validDate
        ) {
            $dateDir = $pix->setDateDir('request-images');
            $imgRoot = null;
            if (isValidImage($files)) {
                $imgRoot = $pix->addIMG($files, $dateDir->absdir, 'random', 1500);
                if ($imgRoot) {
                    $absFile = $dateDir->absdir . $imgRoot;
                    $imgRoot = $dateDir->uplroot . $imgRoot;

                    $pix->make_thumb('request-files', $absFile);
                }
            } else {
                $uploadedName = $pix->makestring(40, 'ln') . basename($name);
                $uploadPath = $dateDir->absdir . $uploadedName;
                if (move_uploaded_file($files['tmp_name'], $uploadPath)) {
                    $imgRoot = $dateDir->uplroot . $uploadedName;
                }
            }
            if ($imgRoot) {
                $rid = $pixdb->insert(
                    'help_desk_files',
                    [
                        'request' => $request,
                        'file' => $imgRoot,
                        'name' => $name
                    ]
                );
                if ($rid) {
                    $pixdb->run(
                        'update `help_desk` set attachments = ifnull(attachments, 0) + 1 where id = ' . q($request)
                    );

                    $r->status = 'ok';
                    $r->rid = $rid;
                    $r->name = $name;
                    $r->file = $dateDir->uplpath . $pix->thumb($imgRoot, 'w1500');
                    $r->count = $pixdb->getRow(
                        'help_desk',
                        ['id' => $request],
                        'attachments'
                    )->attachments;
                    $r->request = $request;
                }
            }
        } else {
            $r->errorMsg = 'requested completion date has expired.';
        }
        // var_dump($r);
    }
}
// exit;
