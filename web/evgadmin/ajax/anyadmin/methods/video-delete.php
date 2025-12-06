<?php
if($lgUser->type != 'admin') {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (isset($_['id'])) {
    $id = esc($_['id']);

    if ($id) {
        $data = $pixdb->getRow(
            'videos',
            ['id' => $id]
        );

        if ($data) {
            $pixdb->delete(
                'videos',
                ['id' => $id]
            );
            
            $r->status = 'ok';
            $r->id = $id;
        }
    }
}
