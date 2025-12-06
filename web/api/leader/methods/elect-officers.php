<?php
$pl = getJsonBody();
if ($isSectionLeader || $isAffiliateLeader || $isCollegiateLeader) {
    if (isset($pl->member, $pl->title)) {
        $memberId = esc($pl->member);
        $title = esc($pl->title);
        $officer = esc($pl->officer ?? '');
        $new = !$officer;

        if ($memberId && $title) {
            $member = $pixdb->getRow(
                'members',
                ['id' => $memberId]
            );
            $offcrTtl = $pixdb->getRow(
                'officers_titles',
                ['id' => $title]
            );
            if ($member && $offcrTtl) {
                $circle = null;
                $circleId = null;
                if ($isSectionLeader) {
                    $assignedFor = $pixdb->getRow(
                        'section_leaders',
                        [
                            'type' => 'leader',
                            'mbrId' => $lgUser->id
                        ],
                        'secId'
                    );
                    if ($assignedFor) {
                        $circle = 'section';
                        $circleId = $assignedFor->secId;
                    }
                } elseif ($isCollegiateLeader) {
                    $assignedFor = $pixdb->getRow(
                        'collegiate_leaders',
                        [
                            'mbrId' => $lgUser->id
                        ],
                        'coliId'
                    );
                    if ($assignedFor) {
                        $circle = 'collegiate';
                        $circleId = $assignedFor->coliId;
                    }
                } elseif ($isAffiliateLeader) {
                    $assignedFor = $pixdb->getRow(
                        'affiliate_leaders',
                        [
                            'mbrId' => $lgUser->id
                        ],
                        'affId'
                    );
                    if ($assignedFor) {
                        $circle = 'affiliate';
                        $circleId = $assignedFor->affId;
                    }
                }
                if ($circle && $circleId) {
                    $isDup = $pixdb->getRow(
                        'officers',
                        [
                            '#QRY' => 'memberId!=' . $memberId,
                            'title' => $title,
                            'circle' => $circle,
                            'circleId' => $circleId
                        ]
                    );
                    if (!$isDup) {
                        $data = false;
                        if ($officer) {
                            $data = $pixdb->getRow(
                                'officers',
                                ['id' => $officer],
                                'id'
                            );
                        }
                        if (
                            $new ||
                            (
                                !$new &&
                                $data
                            )
                        ) {
                            $dbData = [
                                'memberId' => $memberId,
                                'title' => $title,
                                'assignedBy' => $lgUser->id,
                                'circle' => $circle,
                                'circleId' => $circleId
                            ];
                            if ($new) {
                                $ins = $pixdb->insert(
                                    'officers',
                                    $dbData
                                );
                            } else {
                                $ins = $officer;
                                $pixdb->update(
                                    'officers',
                                    ['id' => $ins],
                                    $dbData
                                );
                            }
                        }
                        if ($ins) {
                            $r->status = 'ok';
                            $r->message = 'Done!';
                        }
                    } else {
                        $r->message = 'Only one officer per title is allowed.';
                    }
                }
            }
        }
    }
}
