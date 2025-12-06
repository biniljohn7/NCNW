<?php
include 'lib/lib.php';

// devMode();
header('content-type:text/plain');

$pix->local = 0;

$pix->send_mail(
  // 'print',
  'binilweb@gmail.com',
  'Hello',
  '<strong>Hi,</strong> How are you.'
);
