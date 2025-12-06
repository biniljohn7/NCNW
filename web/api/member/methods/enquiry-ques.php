<?php
$r->success = 1;
$r->status = 'ok';
$r->message = 'Data Retrieved Successfully!';
$r->data = $pixdb->get(
    'enquiry_questions',
    [],
    'id,
    question as name'
)->data;
