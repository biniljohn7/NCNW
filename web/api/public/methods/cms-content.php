<?php
if (isset($_GET['pageId'])) {
    $pageId = esc($_GET['pageId']);

    if ($pageId) {

        $cms = $pixdb->get(
            'cms',
            [
                'id' => $pageId,
                'enabled' => 'Y',
                'single' => 1
            ],
            'cmsContent'
        );

        if ($cms) {
            $r->status = 'ok';
            $r->success = 1;
            $r->message = 'Data retrived Successfully!';
            $r->data = [
                'content' => $cms->cmsContent
            ];
        }
    }
}
