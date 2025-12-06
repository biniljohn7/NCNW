<?php
if (
    isset($_['countryId'])
) {
    $countryId = esc($_['countryId']);

    if ($countryId) {
        $states = $pixdb->get(
            [
                ['regions', 'r', 'id'],
                ['states', 's', 'region']
            ],
            [
                'r.nation' => $countryId,
                'r.enabled' => 'Y',
                's.enabled' => 'Y'
            ],
            's.id, s.name'
        );

        $tmp = array();
        foreach ($states->data as $dta) {
            $dta->id = (int)$dta->id;
            $tmp[] = $dta;
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
