<?php
if (
    isset($_['nationId'])
) {
    $nationId = esc($_['nationId']);

    if ($nationId) {
        $regions = $pixdb->get(
            'regions',
            [
                'nation' => $nationId,
                'enabled' => 'Y',
                '#SRT' => 'id'
            ],
            'id, name'
        );

        $tmp = array();
        foreach ($regions->data as $dta) {
            $tmp[] = (object)[
                'regionId' => (int)$dta->id,
                'regionName' => $dta->name
            ];
        }

        $r->status = 'ok';
        $r->success = 1;
        $r->data = $tmp;
        $r->message = 'Data is retrieved successfully!';
    } else {
        $r->status = 'ok';
        $r->success = 1;
        $r->data = [];
        $r->message = 'Data is retrieved successfully!';
    }
}
