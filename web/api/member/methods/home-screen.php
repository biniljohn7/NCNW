<?php
$scope = strtolower(esc($_GET['scope'] ?? 'national'));
if (!(
    $scope == 'national' ||
    $scope == 'regional' ||
    $scope == 'state' ||
    $scope == 'chapter'
)) {
    $scope = 'national';
}

$r->status = 'ok';
$r->success = 1;

//Benefits
$benefits = $pixdb->get(
    [
        ['benefits', 'b', 'ctryId'],
        ['categories', 'c', 'id']
    ],
    [
        'b.status' => 'active',
        'scope' => $scope
    ],
    'c.ctryName,
    b.discount,
    b.id as benefitId,
    b.shortDescr,
    c.id as categoryId'
)->data;
foreach ($benefits as $b) {
    $b->categoryName = $b->ctryName;
    $b->shortDetails = $b->shortDescr;
    unset($b->ctryName, $b->shortDescr);
}

//Advocacies
$advocacies = $pixdb->get(
    'advocacies',
    [
        'enabled' => 'Y',
        'scope' => $scope
    ],
    'id as advocacyId,
    title,
    createdAt,
    senator,
    pdf, 
    legislator,
    contact,
    recipient as recipientName,
    recipAddr as recipientAddress,
    recipEmail as recipientEmail,
    image'
)->data;
foreach ($advocacies as $adv) {
    if ($adv->image) {
        $adv->image = $pix->uploadPath . 'advocacy-image/' . $pix->thumb($adv->image, '150x150');
    }
}

//Events
$today = date('Y-m-d', strtotime('today'));
$events = $pixdb->get(
    'events',
    [
        '#QRY' => 'enddate >= ' . q($today),
        'enabled' => 'Y',
        'scope' => $scope
    ],
    'id,
    name,
    date,
    descrptn,
    image,
    address'
)->data;
foreach ($events as $ev) {
    $ev->eventId = $ev->id;
    $ev->date = date('F d, Y', strtotime($ev->date));
    $ev->descrptn = substr($ev->descrptn, 0, 135);
    if ($ev->image) {
        $img = $ev->image;
        $ev->image = $pix->uploadPath . 'events/' . $pix->thumb($img, '150x150');
        $ev->image2 = $pix->uploadPath . 'events/' . $pix->thumb($img, '350x350');
    }
    unset($ev->id);
}

//$news = $evg->getNews($scope);
$news = $pixdb->get(
    'news',
    ['scope' => $scope]
)->data;

$r->data = (object)[
    'news' => $news,
    'benefits' => $benefits,
    'advocacies' => $advocacies,
    'events' => $events
];
$r->message = 'Data Retrieved Successfully!';
