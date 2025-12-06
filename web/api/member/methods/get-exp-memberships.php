<?php
$list = $evg->getDues($lgUser->id, $lgUser->id);
$r->data->list = $list[$lgUser->id] ?? [];
$r->success = 1;
$r->status = 'ok';
$r->message = 'Membership details loaded!';
