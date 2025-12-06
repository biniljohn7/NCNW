<?php
(function ($pix, $pixdb, $evg, $r) {
    $_ = $_REQUEST;

    if (isset($_['id'])) {
        $id = esc($_['id']);

        if ($id) {
            $news = $pixdb->getRow('news', ['slug' => $id]);
            if ($news) {
                $r->status = 'ok';
                $r->success = 1;
                $r->data = $news;
                $r->message = 'News loaded successfully!';
            }
        }
    }


    // if (isset($_GET['dev'])) {
    //     devMode();
    //     prettyJson($r);
    //     exit;
    // }
})($pix, $pixdb, $evg, $r);
