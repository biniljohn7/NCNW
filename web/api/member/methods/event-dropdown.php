<?php
$r->status = 'ok';
$r->success = 1;

$natIds = [];
$regIds = [];
$staIds = [];
$chpIds = [];

$eveLocs = $db->query('SELECT DISTINCT nation, region, state, chapter FROM event_locations')->fetchAll(PDO::FETCH_OBJ);
foreach ($eveLocs as $loc) {
    $natIds[] = $loc->nation;
    $regIds[] = $loc->region;
    $staIds[] = $loc->state;
    $chpIds[] = $loc->chapter;
}
$natIds = array_unique(array_filter($natIds));
$regIds = array_unique(array_filter($regIds));
$staIds = array_unique(array_filter($staIds));
$chpIds = array_unique(array_filter($chpIds));

$getNations = $pixdb->get('nations', ['id' => $natIds], 'id, name')->data;
$getRegions = $pixdb->get('regions', ['id' => $regIds], 'id, name')->data;
$getStates = $pixdb->get('states', ['id' => $staIds], 'id, name')->data;
$getChapters = $pixdb->get('chapters', ['id' => $chpIds], 'id, name')->data;

foreach ($getNations as $na) {
    $na->type = 'Nation';
}
foreach ($getRegions as $reg) {
    $reg->type = 'Region';
}
foreach ($getStates as $st) {
    $st->type = 'State';
}
foreach ($getChapters as $ch) {
    $ch->type = 'Chapter';
}

$r->data = (object)[
    'all' => array_merge($getNations, $getRegions, $getStates, $getChapters),
    'chapter' => $getChapters,
    'regional' => $getRegions,
    'national' => $getNations,
    'state' => $getStates
];

$r->message = 'Data Retrieved Successfully!';
