<?php
$_ = $_POST;

if (isset($_['id'])) {
    $id = esc($_['id']);
    $req = esc($_['req']);

    if (
        $id &&
        ($reqCmnt = $pixdb->getRow(
            'helpdesk_comments',
            ['id' => $id]
        ))
    ) {
        $qid = q($id);
        $pixdb->delete(
            'helpdesk_comments',
            ['#QRY' => "(replyto=$qid OR id=$qid)"]
        );

        $pixdb->run(
            "UPDATE help_desk
                SET `ttlComments`= (
                    SELECT COUNT(1)
                        FROM helpdesk_comments
                        WHERE request=$reqCmnt->request
                )
            WHERE id=$reqCmnt->request"
        );

        if ($reqCmnt->replyto) {
            $pixdb->run(
                "SET @COUNT = (
                    SELECT COUNT(1)
                        FROM helpdesk_comments
                        WHERE replyto=$reqCmnt->replyto
                );
                
                UPDATE helpdesk_comments
                    SET `replies`= @COUNT
                    WHERE id=$reqCmnt->replyto;"
            );
        }

        $r->status = 'ok';
        $r->count = $pixdb->getRow(
            'help_desk',
            ['id' => $req],
            'ttlComments'
        )->ttlComments;
    }
}
// exit;
