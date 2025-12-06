<?php
$HelpDesk = loadModule('helpdesk');

$subPage = str2url($_GET['sec'] ?? '');
if (!$subPage) {
    $subPage = 'list';
}
$subPageCode = dirname(__FILE__) . "/requests/$subPage.php";
if (is_file($subPageCode)) {
    include $subPageCode;
}
