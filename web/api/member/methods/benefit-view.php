<?php
$requestBody = file_get_contents('php://input');
$postData = json_decode($requestBody);

if (
    is_object($postData) &&
    isset(
        $postData->benefitId,
        $postData->categoryId
    )
) {
    $benefitId = intval($postData->benefitId);
    $categoryId = intval($postData->categoryId);

    if (
        $benefitId &&
        $categoryId
    ) {
        $bebefit = $pixdb->get(
            'benefits',
            [
                'id' => $benefitId,
                'single' => 1
            ]
        );

        if (
            $bebefit &&
            $bebefit->ctryId == $categoryId &&
            $bebefit->status == 'active'
        ) {
            $category = $pixdb->get(
                'categories',
                [
                    'id' => $categoryId,
                    'single' => 1
                ],
                'id,
                ctryName,
                type,
                enable'
            );

            if (
                $category &&
                $category->type == 'Benefit' &&
                $category->enable == 'Y'
            ) {
                $category = $category->ctryName;
            } else {
                $category = '';
            }


            $provider = $pixdb->get(
                'benefit_providers',
                [
                    'id' => $bebefit->provider,
                    'single' => 1
                ]
            );

            $r->success = 1;
            $r->data = (object)[
                'address' => $provider->address ?? '',
                'code' => $bebefit->code ?? '',
                'companyLogo' => $provider->logo ? (DOMAIN . 'uploads/provider-logo/' . $pix->get_file_variation($provider->logo, '150x150')) : '',
                'shortDetails' => $bebefit->shortDescr ?? '',
                'companyName' => $provider->name ?? '',
                'discount' => $bebefit->discount ?? '',
                'benefitId' => $bebefit->id,
                'categoryName' => $category,
                'providerContactNo' => $provider->phone ?? '',
                'phoneCode' => $provider->cntryCode ?? '',
                'details' => $bebefit->descr ?? '',
                'providerEmail' => $provider->email,
                'categoryId' => $bebefit->ctryId ?? ''
            ];
            $r->message = 'Data Retrieved Successfully!';
            unset($r->status);
        }
    }
}
