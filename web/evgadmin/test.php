<?php
include 'lib/lib.php';

var_dump($_ENV['MAILGUN_DOMAIN'], $_ENV['MAILGUN_APIKEY']);
exit;

// devMode();
header('content-type:text/plain');

$pix->local = 0;

$pix->send_mail(
  // 'print',
  'binilweb@gmail.com',
  'Hello',
  '<strong>Hi,</strong> How are you.'
);
