<?php
if (isset($_GET['careerid'])) {
    $careerid = esc($_GET['careerid']);

    if ($careerid) {

        $career = $pixdb->get(
            'careers',
            [
                'id' => $careerid,
                'single' => 1
            ],
            'title,
            image,
            address,
            description,
            type,
            enabled'
        );

        if (
            $career &&
            $career->enabled == 'Y'
        ) {
            $career->careerType = '';
            if ($career->type) {
                $type = $pixdb->getCol(
                    'career_types',
                    [
                        'id' => $career->type
                    ],
                    'name'
                );

                if ($type && isset($type[0])) {
                    $career->careerType = $type[0];
                }
            }
            unset($career->type);
            $career->image = $career->image ? ($pix->uploadPath . 'career-image/' . $pix->thumb($career->image, '450x450')) : null;

            $r->status = 'ok';
            $r->success = 1;
            $r->message = 'Viewed Successfully!';
            $r->data = $career;
        }
    }
}
