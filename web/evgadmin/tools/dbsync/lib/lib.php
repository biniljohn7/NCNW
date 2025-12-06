<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$libdir = dirname(__FILE__) . '/../';

error_reporting(E_ALL);
ini_set('memory_limit', '500M');
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');
ini_set('max_execution_time', 3600);

date_default_timezone_set('Asia/Kolkata');
$datetime = date('Y-m-d H:i:s');
$date = substr($datetime, 0, 10);
$year = substr($date, 0, 4);

session_start();
ob_start();

include $libdir . 'config.php';
include $libdir . 'lib/pix.php';

$confKey = $pix->local ? 'local' : 'live';
$db = new PDO(
	'mysql:host=' . $config->{$confKey}->dbHost . ';dbname=' . $config->{$confKey}->dbName,
	$config->{$confKey}->dbUser,
	$config->{$confKey}->dbPass
);
$pdo = $db;

function have(&$var, $default = '')
{
	return isset($var) ? $var : $default;
}
function esc($s, $len = null)
{
	$s = escape($s, '|html_encode|strip_non_utf8|');
	return $len ? substr($s, 0, $len) : $s;
}
function escx($s)
{
	return escape($s, '|html_encode|strip_non_utf8|strip_tags|');
}
function escsl($s)
{
	return escape($s, '|strip_tags|strip_non_utf8|addslash|');
}
function escape($string, $filters = '|strip_tags|strip_non_utf8|')
{
	$string = is_string($string) || is_numeric($string) ? trim($string) : '';
	preg_match('/strip_non_utf8/i', $filters) ? $string = preg_replace('/[^\x00-\x7f\xA9\xAE\xA3\xA5]|(\&\#[0-9]{1,}\;)/', '', $string) : 0;
	preg_match('/strip_tags/i', $filters) ? $string = strip_tags($string) : 0;
	preg_match('/html_encode/i', $filters) ? $string = htmlentities($string) : 0;
	preg_match('/html_encode/i', $filters) ? $string = str_replace('\'', '&#39;', $string) : 0;
	preg_match('/filter_phone/i', $filters) ? $string = preg_replace('/[^0-9\+\ \-\)\(]/', '', $string) : 0;
	if (preg_match('/addslash/i', $filters)) {
		$string = addslashes($string);
	}
	return $string;
}

function str2url($name)
{
	$file_name = strtolower($name);
	$file_name = preg_replace('/[^0-9a-zA-Z]/', '-', $file_name);
	$file_name = preg_replace('/--+/', '-', $file_name);
	$file_name = preg_replace('/\-$|^\-/', '', $file_name);
	return $file_name;
}
function str2class($name)
{
	$file_name = strtolower($name);
	$file_name = preg_replace('/[^0-9a-zA-Z]/', '_', $file_name);
	$file_name = preg_replace('/__+/', '_', $file_name);
	$file_name = preg_replace('/\_$|^\_/', '', $file_name);
	return $file_name;
}
function is_mail($a)
{
	return filter_var($a, FILTER_VALIDATE_EMAIL);
}
