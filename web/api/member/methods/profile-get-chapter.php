<?php
if (
    isset($_['stateId'])
) {
    $stateId = esc($_['stateId']);

    if (
        $stateId ||
        $stateId == 0
    ) {
        $filter = array(
            'enabled' => 'Y',
            '#SRT' => 'name ASC'
        );
        $stateId ? $filter['state'] = $stateId : 0;

        $chapters = $pixdb->get(
            'chapters',
            $filter,
            'id, name'
        );

        $tmp = array();
        foreach ($chapters->data as $dta) {
            $tmp[] = (object)[
                'chapterId' => (int)$dta->id,
                'chapterName' => $dta->name
            ];
        }

        $r->status = 'ok';
        $r->success = 1;
        $r->data = $tmp;
        $r->message = 'Data is retrieved successfully!';
    }
}
