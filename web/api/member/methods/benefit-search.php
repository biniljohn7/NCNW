<?php
$requestBody = file_get_contents('php://input');
$postData = json_decode($requestBody);

if (
    is_object($postData) &&
    isset(
        $postData->pageId,
        $postData->search
    )
) {
    $pageId = intval($postData->pageId);
    $searchKey = esc($postData->search);

    if (
        $pageId &&
        $searchKey
    ) {
        $r->success = 1;
        $r->data = (object)[
            'list' => [],
            'currentPageNo' => $pageId,
            'totalPages' => 0
        ];
        $r->message = 'Data Retrieved Successfully!';
        unset($r->status);

        $bebefits = $pixdb->get(
            'benefits',
            [
                'status' => 'active',
                '__page' => max(0, ($pageId - 1)),
                '__limit' => 10,
                '#QRY' => '(name like "%' . $searchKey . '%" or shortDescr like "%' . $searchKey . '%" or descr like "%' . $searchKey . '%")'
            ]
        );

        if ($bebefits->data) {

            $ctryArray = array();
            $ctryIds = array();

            foreach ($bebefits->data as $bnft) {
                $ctryIds[] = $bnft->ctryId;
            }

            $ctryIds = array_unique(array_filter(array_map('esc', $ctryIds)));

            if (
                !empty($ctryIds)
            ) {
                $ctryInfo = $pixdb->get(
                    'categories',
                    [
                        'enable' => 'Y',
                        'type' => 'Benefit',
                        '#QRY' => 'id in (' . implode(',', $ctryIds) . ')'
                    ],
                    'id,
                    ctryName'
                );

                foreach ($ctryInfo->data as $ctry) {
                    $ctryArray[$ctry->id] = $ctry->ctryName;
                }
            }

            $bnftArr = array();
            foreach ($bebefits->data as $bnft) {
                $bnftArr[] = (object)[
                    'shortDetails' => $bnft->shortDescr ?? '',
                    'name' => $bnft->name ?? '',
                    'discount' => $bnft->discount ?? '',
                    'benefitId' => $bnft->id,
                    'categoryName' => $ctryArray[$bnft->ctryId] ?? '',
                    'categoryId' => $bnft->ctryId
                ];
            }

            $r->data = (object)[
                'list' => $bnftArr,
                'currentPageNo' => $bebefits->current + 1,
                'totalPages' => $bebefits->pages
            ];
        }
    }
}
