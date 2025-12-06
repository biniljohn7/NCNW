<?php
if($lgUser->type != 'admin') {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

if(
    isset(
        $_['id'],
        $_['title'],
        $_['embed_c']
    )
) {
    $id = esc($_['id']);
    $title = esc($_['title']);
    $video = esc($_['embed_c']);
    $enable = have($_['enable'], 0);

    if(
        $id &&
        $title &&
        $video
    ) {
        if (preg_match("/iframe/", $video)) {
            $video = htmlspecialchars_decode($video);
            preg_match_all('/src="(.+?)"/i', $video, $code);
            $code = isset($code[1][0]) ? $code[1][0] : '';
        } else {
            $code = $video;
        }
        
        if($code) {
            if($id == "new") {
                $iid = $pixdb->insert(
                    'videos',
                    [
                        'title' => $title,
                        'video' => $code,
                        'enable' => $enable == 1 ? 'Y' : 'N'
                    ]
                );

                if($iid != false) {
                    $r->status = 'ok';
                }
            } else {
                $pixdb->update(
                    'videos',
                    ['id' => $id],
                    [
                        'title' => $title,
                        'video' => $code,
                        'enable' => $enable == 1 ? 'Y' : 'N'
                    ]
                );

                $iid = $id;
                $r->status = 'ok';
            }
        }
    } 

    if($r->status == 'ok') {
        $youtube = false;
        if ($youtube = preg_match('/youtube/', $code)) {
            $vId = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
            preg_match($vId, $code, $matches);
        }
        $r->id = $iid;
        $r->video = $code;
        $r->vid = isset($matches[1]) ? $matches[1] : '';
        $r->title = $title;
        $r->enable = $enable == 1 ? 'Y' : 'N';
    }
}
?>