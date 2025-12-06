<?php
$ctryInfo = $pixdb->get(
    'categories',
    [
        'type' => 'Benefit',
        '#SRT' => 'ctryName asc',
        'enable' => 'Y'
    ],
    'id,
    ctryName,
    itryIcon'
);

if ($ctryInfo->data) {

    $dataArr = array();
    foreach ($ctryInfo->data as $ctry) {
        $dataArr[] = (object)[
            'name' => $ctry->ctryName,
            'image' => $ctry->itryIcon ?
                $pix->uploadPath . 'category-image/' . $pix->thumb($ctry->itryIcon, '150x150') :
                null,
            'categoryId' => $ctry->id
        ];
    }
    $r->success = 1;
    $r->data = $dataArr;
    $r->message = 'Data Retrieved Successfully!';
    unset($r->status);
}
