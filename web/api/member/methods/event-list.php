<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

$r->status = 'ok';
$r->success = 1;

$scope = have($pl->location, null);
$locations = have($pl->locationIds, null);
$pgn = max(1, intval($pl->pageId ?? 1));
$pageLimit = 20;
$today = date('Y-m-d', strtotime('today'));
$lstCnds = [
    '#QRY' => 'enddate >= ' . q($today),
    'enabled' => 'Y',
    '__page' => $pgn - 1,
    '__limit' => $pageLimit
];

if (
    (
        $scope == 'Nation' ||
        $scope == 'Region' ||
        $scope == 'State' ||
        $scope == 'Chapter'
    ) &&
    !empty($locations)
) {
    $scope  = strtolower($scope);
    $lstCnds[$scope] = $locations;
}

$getEvents = $pixdb->get(
    [
        ['events', 'ev', 'id'],
        ['event_locations', 'lc', 'event']
    ],
    $lstCnds,
    'ev.id as eventId,
    date,
    name,
    address,
    image'
);

foreach ($getEvents->data as $ev) {
    $ev->date = date('F j, Y', strtotime($ev->date));
    if ($ev->image) {
        $ev->image = $pix->uploadPath . 'events/' . $pix->thumb($ev->image, '150x150');
    }
}

$r->data = (object)[
    'list' => $getEvents->data,
    'currentPageNo' => $getEvents->current + 1,
    'totalPages' => $getEvents->pages
];

$r->message = 'Data Retrieved Successfully!';
