<?php
$pl = getJsonBody();

if(isset($pl->officer)) {
    $officer = esc($pl->officer);

    if(
        $officer &&
        $pixdb->getRow(
            'officers',
            ['id' => $officer]
        )
    ) {
        $pixdb->delete(
            'officers',
            ['id' => $officer]
        );

        $r->status = 'ok';
        $r->success = 1;
        $r->message = 'Member removed successfully.';
    }
}
// exit;
?>