<?php
devMode();
$_ = $_REQUEST;

if (isset($_['table'])) {
    $table = esc($_['table'] ?? '');
    $tables = $evg::tables;

    if (
        $table &&
        array_key_exists($table, $tables)
    ) {

        $fltrCond = [
            '#SRT' => 'id asc'
        ];
        if ($table !== 'officers_titles') {
            $fltrCond['#SRT'] = 'name asc';
        }

        $collectedData = $pixdb->get(
            $table,
            $fltrCond
        );

        $tmp = [];
        foreach ($collectedData->data as $row) {
            $id = '';
            if ($table === 'chapters') {
                $id = $row->secId ? " ( $row->secId )" : '';
            }
            $obj = (object)[
                'id'   => $row->id,
                'name' => ($row->name ?? $row->title ?? null) . $id
            ];

            $tmp[] = $obj;
        }

        if (!empty($tmp)) {
            $r->status = 'ok';
            $r->data = $tmp;
            $r->dataLabel = $tables[$table] ?? null;
        }
    } else {
        $r->errorMsg = 'Data could not be collected.';
    }
}
// exit;
