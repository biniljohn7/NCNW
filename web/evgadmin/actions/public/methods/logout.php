<?php
$members = loadModule('admins');
$members->logout();

$pix->remsg();
$pix->redirect($pix->adminURL);
