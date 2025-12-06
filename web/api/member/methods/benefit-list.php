<?php
$requestBody = file_get_contents('php://input');
$postData = json_decode($requestBody);

if (
    is_object($postData) &&
    isset(
        $postData->categoryId,
        $postData->page,
        $postData->type
    )
) {
    $categoryId = intval($postData->categoryId);
    $page = intval($postData->page);
    $type = esc($postData->type);

    if (
        $categoryId &&
        $page &&
        $type
    ) {
        $r->success = 1;
        $r->data = (object)[
            'list' => [],
            'currentPageNo' => $page,
            'totalPages' => 0
        ];
        $r->message = 'Data Retrieved Successfully!';
        unset($r->status);

        $ctryArray = array();
        $filter = [
            'ctryId' => $categoryId,
            'status' => 'active',
            '__page' => max(0, ($page - 1)),
            '__limit' => 15,
        ];
        if ($type != 'AllBenefits') {
            $filter['scope'] = $type;
        }

        $bebefits = $pixdb->get(
            'benefits',
            $filter,
            'id,
            discount,
            ctryId,
            shortDescr'
        );

        if ($bebefits->data) {

            $ctryIds = array();
            foreach ($bebefits->data as $bnft) {
                $ctryIds[] = $bnft->ctryId;
            }
            $ctryIds = array_unique(array_filter($ctryIds));

            if ($ctryIds) {
                $ctryInfo = $pixdb->get(
                    'categories',
                    [
                        '#QRY' => 'id in (' . implode(', ', $ctryIds) . ')'
                    ],
                    'id,
                    ctryName,
                    type,
                    enable'
                );

                foreach ($ctryInfo->data as $ctry) {
                    if (
                        $ctry->type == 'Benefit' &&
                        $ctry->enable == 'Y'
                    ) {
                        $ctryArray[$ctry->id] = $ctry->ctryName;
                    }
                }
            }

            $bnftArr = array();
            foreach ($bebefits->data as $bnft) {
                $bnftArr[] = (object)[
                    'discount' => $bnft->discount ?? 0,
                    'benefitId' => $bnft->id,
                    'categoryName' => $ctryArray[$bnft->ctryId] ?? '',
                    'categoryId' => $bnft->ctryId,
                    'shortDetails' => $bnft->shortDescr ?? ''
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
