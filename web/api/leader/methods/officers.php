<?php
$r->data->titles = $pixdb->get(
    'officers_titles',
    []
);
$officers = $pixdb->get(
    'officers',
    ['assignedBy' => $lgUser->id],
    'id, memberId, title, circle, circleId'
)->data;

$circle = null;
$circleId = null;
foreach ($officers as $info) {
    if ($info->circle && $info->circleId) {
        $circle = $info->circle;
        $circleId = $info->circleId;
        break;
    }
}
$others = [];
if ($circle && $circleId) {
    $others = $pixdb->get(
        'officers',
        [
            '#QRY' => 'assignedBy!=' . $lgUser->id,
            'circle' => $circle,
            'circleId' => $circleId
        ],
        'id, memberId, title'
    )->data;
}
$officers = array_merge($officers, $others);
$memberIds = collectObjData($officers, 'memberId');

if ($memberIds) {
    $memberInfo = $pixdb->fetchAssoc(
        [
            ['members', 'm', 'id'],
            ['members_info', 'i', 'member']
        ],
        ['m.id' => $memberIds],
        'm.id,
        m.firstName,
        m.lastName,
        m.memberId,
        i.city,
        i.zipcode,
        i.cruntChptr,
        i.collegiateSection,
        i.affilateOrgzn',
        'id'
    );
    $sectionIds = collectObjData($memberInfo, 'cruntChptr');
    $affiliateIds = collectObjData($memberInfo, 'affilateOrgzn');
    $allSections = [];
    $allAffiliates = [];
    if ($sectionIds) {
        $allSections = $pixdb->fetchAssoc(
            'chapters',
            ['id' => $sectionIds],
            'id,
            name',
            'id'
        );
    }
    if ($affiliateIds) {
        $allAffiliates = $pixdb->fetchAssoc(
            'affiliates',
            ['id' => $affiliateIds],
            'id,
            name',
            'id'
        );
    }
    foreach ($officers as $offcr) {
        if (isset($memberInfo[$offcr->memberId])) {
            $info = $memberInfo[$offcr->memberId];
            $offcr->offId = $offcr->id;
            $offcr->id = $offcr->memberId;
            $offcr->name = "$info->firstName $info->lastName";
            $offcr->memberId = $info->memberId ?? '--';
            $offcr->city = $info->city;
            $offcr->zipcode = $info->zipcode;
            $offcr->section = isset($allSections[$info->cruntChptr]) ? $allSections[$info->cruntChptr]->name : '--';
            $offcr->affiliation = isset($allAffiliates[$info->affilateOrgzn]) ? $allAffiliates[$info->affilateOrgzn]->name : '--';
        }
    }
}
$r->status = 'ok';
$r->success = 1;
$r->data->officers = $officers;
$r->message = '';
