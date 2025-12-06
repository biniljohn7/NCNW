<?php
if (
    isset($_['regionId'])
) {
    $regionId = esc($_['regionId']);

    if ($regionId) {
        $states = $pixdb->get(
            'states',
            [
                'region' => $regionId,
                'enabled' => 'Y',
                '#SRT' => 'name asc'
            ],
            'id, name'
        );

        $tmp = array();
        foreach ($states->data as $dta) {
            $tmp[] = (object)[
                'id' => (int)$dta->id,
                'name' => $dta->name
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
