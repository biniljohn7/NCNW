<?php
devMode();
$inp = getJsonBody();
var_dump($inp);

$r->status = 'ok';
$r->message = 'Data Retrieved Successfully!';
$r->data = (object)[
    'nations' => [],
    'states' => [],
    'sections' => [],
    'affiliations' => [],
];

if (isset($inp->nations)) {
    $nations = esc($inp->nations);
    if ($nations == 'all') {
        $nations = $evg->getNations([], 'id, name');
        foreach ($nations as $nation) {
            $r->data->nations[] = (object)[
                'label' => $nation->name,
                'value' => $nation->id
            ];
        }
    }
}
if (isset($inp->states)) {
    $states = esc($inp->states);
    if ($states == 'all') {
        $states = $evg->getStates([], 'id, name, nation');
        foreach ($states as $state) {
            $r->data->states[] = (object)[
                'label' => $state->name,
                'value' => $state->id,
                'nation' => $state->nation
            ];
        }
    }
}
if (isset($inp->sections)) {
    $sections = esc($inp->sections);
    if ($sections == 'all') {
        $sections = $evg->getChapters([], 'id, name');
        foreach ($sections as $section) {
            $r->data->sections[] = (object)[
                'label' => $section->name,
                'value' => $section->id
            ];
        }
    }
}
if (isset($inp->affiliations)) {
    $affiliations = esc($inp->affiliations);
    if ($affiliations == 'all') {
        $affiliations = $evg->getAffiliations([], 'id, name');
        foreach ($affiliations as $affiliation) {
            $r->data->affiliations[] = (object)[
                'label' => $affiliation->name,
                'value' => $affiliation->id
            ];
        }
    }
}
