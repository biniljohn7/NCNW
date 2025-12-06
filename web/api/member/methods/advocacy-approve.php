<?php
devMode();

$_ = $_REQUEST;
if (
    $lgUser->verified = 'Y' &&
    isset(
        $_['id'],
        $_['signature']
    )
) {
    $advId = esc($_GET['id']);
    $sign = $_['signature'];

    $image = false;
    if (preg_match('/^data:image\/(png|jpeg|gif);base64,/', $sign, $matches)) {
        $extension = strtolower($matches[1] ?? '');
        $base64Data = substr($sign, strpos($sign, ',') + 1);
        $decodedData = base64_decode($base64Data, true);
        if ($decodedData !== false) {
            $image = @imagecreatefromstring($decodedData);
            if ($image) {
                imagedestroy($image);
            }
        }
    }

    if ($advId && $image) {
        $advocacy = $pixdb->get(
            'advocacies',
            [
                'id' => $advId,
                'enabled' => 'Y',
                'single' => 1
            ],
            'title,
            descrptn,
            pdfContent,
            recipEmail'
        );

        if ($advocacy) {
            // check this advocacy is already approved by logged user
            $approved = $pixdb->get(
                'member_advocacy',
                [
                    'member' => $lgUser->id,
                    'advocacy' => $advId,
                    'single' => 1
                ],
                'member'
            );

            if (!$approved) {
                $dbData = [
                    'member' => $lgUser->id,
                    'advocacy' => $advId
                ];

                $dateDir = $pix->setDateDir('signatures');
                $imageName = $pix->makestring(50, 'ln') . '.' . $extension;
                $imageUrl = $dateDir->absdir . $imageName;
                $add = file_put_contents($imageUrl, $decodedData);
                $pix->resize_image($imageUrl, 250);

                if ($add) {
                    $absFile = $dateDir->absdir . $imageName;
                    $imgRoot = $dateDir->uplroot . $imageName;
                    $dbData['signature'] = $imgRoot;

                    $mmbrInfo = $pixdb->getRow(
                        'members_info',
                        ['member' => $lgUser->id],
                        'prefix,
                        country,
                        state,
                        city'
                    );

                    $memberDetails = array(
                        'FULL_NAME' => $lgUser->firstName . ' ' . $lgUser->lastName,
                        'FIRST_NAME' => $lgUser->firstName,
                        'LAST_NAME' => $lgUser->lastName,
                        'PREFIX' => $mmbrInfo && $mmbrInfo->prefix ? $mmbrInfo->prefix : '',
                        'MEMBERSHIP_CODE' => $lgUser->memberId,
                        'COUNTRY' => $mmbrInfo && $mmbrInfo->country ? $mmbrInfo->country : '',
                        'STATE' => $mmbrInfo && $mmbrInfo->state ? $mmbrInfo->state : '',
                        'CITY' => $mmbrInfo && $mmbrInfo->city ? $mmbrInfo->city : ''
                    );
                    foreach ($memberDetails as $key => $value) {
                        $advocacy->pdfContent = str_replace($key, $value, $advocacy->pdfContent);
                    }

                    //create pdf
                    $pdfPath = loadModule('advocacy')->makeAdvMemAgreement(
                        $dateDir->abspath . $imageName,
                        $advocacy
                    );
                    $dbData['pdf'] = $pdfPath;

                    $info = $pix->getData('email-templates/advocacy');

                    $pix->e_mail(
                        $advocacy->recipEmail,
                        'Advocacy Support',
                        'advocacy-support',
                        [
                            'NAME' => $lgUser->firstName . ' ' . $lgUser->lastName,
                            'ADVTITLE' => $advocacy->title,
                            'ADVDESC' => $advocacy->descrptn,
                            'PDFCONTENT' => $advocacy->pdfContent,
                            'SIGNATURE' => $dateDir->abspath . $imageName,
                            'BODYTXT' => nl2br($info->body ?? ''),
                            'FT_TEXT_1' => nl2br($info->ftext1 ?? ''),
                            'FT_TEXT_2' => nl2br($info->ftext2 ?? '')
                        ]
                    );

                    $pixdb->insert(
                        'member_advocacy',
                        $dbData,
                        true
                    );

                    // post notification
                    $evg->postNotification(
                        'admin',
                        $lgUser->id,
                        'advocacy-signed',
                        'Advocacy Signed',
                        "$lgUser->firstName $lgUser->lastName has signed on advocacy \"$advocacy->title\".",
                        ['id' => $advId]
                    );

                    $r->status = 'ok';
                    $r->success = 1;
                    $r->message = 'Action submitted Successfully!';
                }
            } else {
                $r->message = 'You have already submitted this issue!';
            }
        }
    }
}
