<?php
if (!$pix->canAccess('events')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb) {
    $_ = $_POST;

    if (
        isset(
            $_['name'],
            $_['date'],
            $_['enddate'],
            $_['category'],
            $_['nation'],
            $_['region'],
            $_['state'],
            $_['chapter'],
            $_['address'],
            $_['description']
        )
    ) {
        $name = ucfirst(esc($_['name']));
        $date = esc($_['date']);
        $enddate = esc($_['enddate']);
        $category = esc($_['category']);
        $nation = esc($_['nation']);
        $region = esc($_['region']);
        $state = esc($_['state']);
        $chapter = esc($_['chapter']);
        $visibility = isset($_['visibility']);
        $address = esc($_['address']);
        $description = esc($_['description']);
        $id = esc($_['id'] ?? '');
        $new = !$id;

        // checking date
        if ($date) {
            $dateOk = 0;
            $date = strtotime(
                str_replace('/', '-', $date) . ' 23:59:59'
            );
            if ($date >= time() || !$new) {
                $dateOk = 1;
            } else {
                $date = false;
            }
        }

        $enddate = strtotime(
            str_replace('/', '-', $enddate) . ' 23:59:59'
        );

        $catData = $category ?
            $pixdb->getRow(
                'categories',
                ['id' => $category],
                'id, type'
            ) :
            false;

        $fetchNation = $nation ?
            $pixdb->getRow('nations', ['id' => $nation], 'id') :
            false;

        $regionOk = false;
        $stateOk = false;
        $chapterOk = false;

        if ($region && $fetchNation) {
            // checking region
            $regionData = $pixdb->getRow('regions', ['id' => $region], 'nation');
            if (
                $regionData &&
                $regionData->nation == $nation
            ) {
                $regionOk = true;

                // checking state
                if ($state) {
                    $stateData = $pixdb->getRow('states', ['id' => $state], 'region');
                    if (
                        $stateData &&
                        $stateData->region == $region
                    ) {
                        $stateOk = true;

                        // checking Section
                        if ($chapter) {
                            $chapterData = $pixdb->getRow('chapters', ['id' => $chapter], 'state');
                            if (
                                $chapterData &&
                                $chapterData->state == $state
                            ) {
                                $chapterOk = true;
                            }
                        }
                    }
                }
            }
        }
        if (!$regionOk) {
            $region = null;
        }
        if (!$stateOk) {
            $state = null;
        }
        if (!$chapterOk) {
            $chapter = null;
        }

        if (
            $name &&
            $date &&
            $catData &&
            $catData->type == 'Event' &&
            $fetchNation &&
            $dateOk &&
            $address &&
            $description
        ) {
            $data = false;
            if ($id) {
                $data = $pixdb->getRow(
                    'events',
                    ['id' => $id],
                    'id'
                );
            }
            if (
                $new ||
                (!$new && $data)
            ) {
                $scopeList = [
                    'national',
                    'state',
                    'regional',
                    'chapter'
                ];
                $scopeNum = ($region ? 1 : 0) +
                    ($state ? 1 : 0) +
                    ($chapter ? 1 : 0);
                $scope = $scopeList[$scopeNum];

                $dbData = [
                    'name' => $name,
                    'enabled' => $visibility ? 'Y' : 'N',
                    'date' => date('Y-m-d', $date),
                    'enddate' => $enddate ? date('Y-m-d', $enddate) : null,
                    'category' => $category,
                    'address' => $address,
                    'scope' => $scope,
                    'descrptn' => $description,
                ];
                if ($new) {
                    $iid = $pixdb->insert(
                        'events',
                        $dbData
                    );
                } else {
                    $iid = $id;
                    $pixdb->update(
                        'events',
                        ['id' => $iid],
                        $dbData
                    );
                }
                if ($iid) {
                    // saving location info
                    $locData = [
                        'nation' => $nation,
                        'region' => $region,
                        'state' => $state,
                        'chapter' => $chapter
                    ];
                    if (
                        $pixdb->getRow(
                            'event_locations',
                            ['event' => $iid],
                            'id'
                        )
                    ) {
                        $pixdb->update(
                            'event_locations',
                            ['event' => $iid],
                            $locData
                        );
                        // 
                    } else {
                        $locData['event'] = $iid;
                        $pixdb->insert(
                            'event_locations',
                            $locData
                        );
                    }

                    $pix->addmsg('Event saved', 1);
                    $pix->redirect('?page=events&sec=details&id=' . $iid);
                }
            }
        }
    }
})($pix, $pixdb);
