<?php
$mship = $pixdb->get(
    'membership_plans',
    [
        'active' => 'Y'
    ]
);

$installments = [];

foreach ($mship->data as $dta) {
    if ($dta->installments) {
        $ins = explode(',', $dta->installments);
        in_array(2, $ins) ? $installments[$dta->id][] = 'Biannual Installments (automatic)' : 0;
        in_array(4, $ins) ? $installments[$dta->id][] = 'Quarterly Installments (automatic)' : 0;
    } else {
        $installments[$dta->id][] = 'One-Time Payment';
    }
}


$r->data = $mship->data ?? [];
$r->installments = $installments;

$r->success = 1;
$r->status = 'ok';
$r->message = 'Data Retrieved Successfully!';
