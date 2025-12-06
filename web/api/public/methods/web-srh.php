<?php
(function ($pix, $pixdb, $pdo, &$r) {

    $pl = file_get_contents('php://input');
    $pl = $pl ? json_decode($pl) : false;
    if (!is_object($pl)) {
        $pl = false;
    }

    if (isset($pl->key)) {
        $srh = esc($pl->key);
        if ($srh) {
            $qs = q('%' . $srh . '%');
            $r->success = 1;
            $r->status = 'ok';
            $r->data->suggestions = [];


            // search tables
            $searchTables = [
                'advocacies' => [
                    'title' => 'title',
                    'descr' => 'descrptn',
                    'url'   => 'advocacy/Issues/',
                    'where' => "enabled='Y'"
                ],
                'benefits' => [
                    'title' => 'name',
                    'descr' => 'shortDescr',
                    'url'   => 'benefits/',
                    'where' => "status='active'"
                ],
                'careers' => [
                    'title' => 'title',
                    'descr' => 'description',
                    'url'   => 'careers/',
                    'where' => "enabled='Y'"
                ],
                // 'cms' => [
                //     'title' => 'cmsName',
                //     'descr' => 'cmsContent',
                //     'url'   => '',
                //     'where' => "enabled='Y'"
                // ],
                'events' => [
                    'title' => 'name',
                    'descr' => 'descrptn',
                    'url'   => 'events/',
                    'where' => "enabled='Y'"
                ],
            ];

            $results = [];

            foreach ($searchTables as $tbl => $cfg) {
                $sql = "
                    SELECT id, {$cfg['title']} AS title, {$cfg['descr']} AS descr
                    FROM $tbl
                    WHERE {$cfg['where']}
                    AND (
                            {$cfg['title']} LIKE $qs
                            OR {$cfg['descr']} LIKE $qs
                        )
                    LIMIT 5
                ";

                $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);

                foreach ($rows as $row) {
                    $title = $row->title;
                    $desc  = strip_tags($row->descr);
                    $keyword = trim($srh);

                    $maxLen = 300;
                    $snippet = '';

                    if ($desc) {
                        $pos = stripos($desc, $keyword);
                        if ($pos !== false) {
                            $start = max(0, $pos - intval($maxLen / 4));
                        } else {
                            $start = 0;
                        }

                        $snippet = substr($desc, $start, $maxLen);
                        if ($start > 0) {
                            $snippet = '…' . $snippet;
                        }
                        if (strlen($desc) > $start + $maxLen) {
                            $snippet .= '…';
                        }
                    }
                    $state = null;
                    if ($tbl === 'advocacies') {
                        $state = ['advocacyId' => $row->id];
                    } elseif ($tbl === 'events') {
                        $state = ['eventId' => $row->id];
                    } elseif ($tbl === 'careers') {
                        $state = ['careerId' => $row->id];
                    } elseif ($tbl === 'benefits') {
                        $state = ['benefitId' => $row->id];
                    }

                    $results[] = (object)[
                        'title'       => $title,
                        'description' => $snippet,
                        'url'         => $cfg['url'] . rawurlencode($title),
                        'state'       => $state
                    ];
                }
            }

            $r->data->suggestions = $results;
        }
    }
})($pix, $pixdb, $pdo, $r);
