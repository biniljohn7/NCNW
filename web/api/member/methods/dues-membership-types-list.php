<?php
$r->status = 'ok';
$r->success = 1;
$r->data =  $pixdb->get(
    'membership_types',
    ['#SRT' => 'id asc'],
    'id as membershipTypeId,
    name as membershipTypeName'
)->data;
