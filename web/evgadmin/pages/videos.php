<?php
(function ($pix, $pixdb, $evg, $isSuperAdmin) {
    if($isSuperAdmin) {
        $pgn = max(0, intval($_GET['pgn'] ?? 0));
        $vdConds = [
            '#SRT' => 'id desc',
            '__page' => $pgn,
            '__limit' => 20,
            '__QUERY__' => array()
        ];

        $videos = $pixdb->get(
            'videos',
            $vdConds
        );

        loadStyle('pages/videos/videos');
        loadScript('pages/videos/videos');
?>
        <h1>Vidoes</h1>
        <?php
        breadcrumbs(['Videos']);
        ?>

        <div class="video-section" id="videoSection">
            <div class="vd-hed">
                <span class="heading mb30">
                    <span class="sub-head title-text">
                        Watch Our Video
                    </span>
                </span>
            </div>
            <?php
            $pix->pagination(
                $videos->pages,
                $pgn,
                5,
                null,
                'pt30 mb50 text-left'
            );
            ?>
            <ul class="f-lst" id="videoListMain">
                <?php
                foreach ($videos->data as $vd) {
                    $videoId = null;
                    $matches = [];

                    if (preg_match('/youtube\.com|youtu\.be/', $vd->video)) {
                        $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})#';
                        if (preg_match($pattern, $vd->video, $matches)) {
                            $videoId = $matches[1]; 
                        }
                    }
                ?>
                    <li class="video" data-url="<?php echo $vd->video; ?>">
                        <span class="pnt videoPrevw">
                            <img src="<?php echo $videoId ? 'https://img.youtube.com/vi/' . $videoId . '/0.jpg' : 'images/video-thumb.jpg'; ?>" 
                                alt="<?php echo $vd->title; ?>" />
                        </span>
                        <span class="fetu">
                            <span class="video-text"><?php echo $vd->title; ?></span>
                            <span class="editable-sec">
                                <span class="material-symbols-outlined fe-icn edit-video" 
                                    data-id="<?php echo $vd->id; ?>" 
                                    data-enable="<?php echo $vd->enable; ?>" 
                                    data-video="<?php echo $vd->video; ?>">
                                    edit
                                </span>
                                <span class="material-symbols-outlined fe-icn delete-video" 
                                    data-id="<?php echo $vd->id; ?>">
                                    delete
                                </span>
                            </span>
                        </span>
                    </li>
                <?php
                }
                ?>
            </ul>
            <?php
            $pix->pagination(
                $videos->pages,
                $pgn,
                5,
                null,
                'pt30 mb50 text-left'
            );
            if (!$videos->pages) {
                NoResult(
                    'No videos found',
                    'We couldn\'t find any results. Maybe try a new search.'
                );
            }
            ?>
        </div>
        <?php
        StickMenu(
            [
                'add',
                'Add More',
                'addVideoBtn',
            ]
        );
    } else {
        AccessDenied();
    }
})($pix, $pixdb, $evg, $isSuperAdmin);
?>