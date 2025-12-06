<?php
(function ($pix, $pixdb, $evg, $r) {
    $_ = $_REQUEST;

    $scope = strtolower(esc($_['scope'] ?? 'national'));
    if (!(
        $scope == 'national' ||
        $scope == 'regional' ||
        $scope == 'state' ||
        $scope == 'chapter'
    )) {
        $scope = 'national';
    }

    //$news = $evg->getNews($scope);
    $news = $pixdb->get(
        'news',
        ['scope' => $scope]
    )->data;

    $r->success = 1;
    $r->data = $news;

    // if (isset($_GET['dev'])) {
    //     devMode();
    //     prettyJson($r);
    //     exit;
    // }
})($pix, $pixdb, $evg, $r);
