<?php
$filterVd =  [
    '#SRT' => 'id desc',
    'enable' => 'Y'
];

$videos = $pixdb->get(
    'videos',
    $filterVd,
    'id,
    title,
    video'
);

$tmp = array();
foreach ($videos->data as $dta) {
    $videoId = null;
    $matches = [];

    if (preg_match('/youtube\.com|youtu\.be/', $dta->video)) {
        $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})#';
        if (preg_match($pattern, $dta->video, $matches)) {
            $videoId = $matches[1]; 
        }
    }

    $tmp[] = (object)[
        'id' => (int)$dta->id,
        'title' => $dta->title,
        'video' => $dta->video,
        'thumb' => $videoId
    ];
}

$r->status = 'ok';
$r->success = 1;
$r->data = $tmp;
$r->message = 'Data is retrieved successfully!';
