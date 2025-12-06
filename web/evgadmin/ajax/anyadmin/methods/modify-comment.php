<?php
$_ = $_POST;

if (
    isset(
        $_['id'],
        $_['comment']
    )
) {
    $id = esc($_['id']);
    $comment = esc($_['comment']);

    if (
        $id &&
        $comment &&
        $pixdb->getRow(
            'helpdesk_comments',
            ['id' => $id]
        )
    ) {
        $pixdb->update(
            'helpdesk_comments',
            ['id' => $id],
            ['comment' => $comment]
        );

        $r->status = 'ok';
        $r->comment = $comment;
    }
}
// exit;
