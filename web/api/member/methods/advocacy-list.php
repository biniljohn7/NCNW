<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;
if (!is_object($pl)) {
    $pl = false;
}

$r->status = 'ok';
$r->success = 1;

$srh = have($pl->search, null);
$pgn = have($pl->pageId, 1);
$scope = have($pl->type, null);
$status = have($pl->advocacyType, null);
$pageLimit = 20;
$advCnds = [
    'enabled' => 'Y',
    '__QUERY__' => [],
    '__page' => $pgn - 1,
    '__limit' => $pageLimit
];

if ($srh) {
    $advCnds['__QUERY__'][] = 'title like "%' . $srh . '%"';
}

if ($scope && preg_match('/^(national|regional|state|chapter)$/i', $scope)) {
    $advCnds['scope'] = strtolower($scope);
}

//get advocacies submitted by Logged User
$advCnds['__QUERY__'][] = 'id 
' . (
    !preg_match('/submitted/i', $status) ?
    ' not '
    : ''
) . ' 
in (
    SELECT 
        advocacy 
    FROM 
        member_advocacy 
    WHERE 
        member = ' . $lgUser->id . '
)';

$advocacies = $pixdb->get(
    'advocacies',
    $advCnds,
    'id as advocacyId,
    title,
    legislator,
    senator,
    createdAt,
    pdf,
    pdfContent,
    contact,
    recipient as recipientName,
    recipAddr as recipientAddress,
    recipEmail as recipientEmail,
    image'
);

foreach ($advocacies->data as $itm) {
    $itm->createdAt = date('Y-m-d\TH:i:s.uP', strtotime($itm->createdAt));
    if ($itm->image) {
        $itm->image = $pix->uploadPath . 'advocacy-image/' . $pix->thumb($itm->image, '450x450');
    }
}

$r->data = (object)[
    'list' => $advocacies->data,
    'currentPageNo' => $advocacies->current + 1,
    'totalPages' => $advocacies->pages
];

$r->message = 'Data Retrieved Successfully!';
