<?php
error_reporting(E_ALL);
ini_set('memory_limit', '500M');
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');
// ini_set('max_execution_time', 3600);
ini_set('max_execution_time', 30);
define('BASEDIR', dirname(dirname(dirname(__FILE__))) . '/');

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = '';
}
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '';
}

define(
    'ISLOCAL',
    preg_match('/^(localhost|binil|192\.168\.)/', $_SERVER['HTTP_HOST']) == 1
);

define(
    'VER',
    ISLOCAL ?
        rand(1, 9999999) :
        '1.1.12'
);

// application time settings
date_default_timezone_set(
    ISLOCAL ?
        'Asia/Kolkata' :
        'America/New_York'
);
$curTime = time();
$datetime = date('Y-m-d H:i:s');
$date = substr($datetime, 0, 10);
$year = substr($date, 0, 4);

define('FISCAL_YEAR_END', '09-30'); //September 30


if (!isset($noSession)) {
    session_start();
}

include_once 'root-config.php';

include_once dirname(__FILE__) . '/phpdotenv/vendor/autoload.php';
function loadEnv()
{
    global $root;
    if (is_dir($root->env)) {
        $dotenv = Dotenv\Dotenv::createImmutable($root->env);
        $dotenv->load();
    }
}
loadEnv();

include_once 'db-config.php';

## connection to db
$db = new PDO(
    'mysql:host=' . $dbConf->host . ';dbname=' . $dbConf->db,
    $dbConf->user,
    $dbConf->pass
);
$pdo = $db;

ob_start();

require_once 'db-manager.php';

## main variables
require_once 'pix.php';
$pix = new pix();


define('DOMAIN', $pix->domain);
define('ADMINURL', $pix->adminURL);


## Error Handling
error_reporting(E_ALL);
set_error_handler('phpErrorHandler');
function phpErrorHandler($num, $error, $file, $line)
{
    global $pix;

    if (
        stripos($error, 'getimagesize') !== false ||
        stripos($error, 'imagecreatefrom') !== false
    ) {
        return false;
    }

    $filePath = '';
    $logFlPath = '';
    $trace = debug_backtrace();
    foreach ($trace as $tc) {
        $tcFile = have($tc['file'], __FILE__);
        $tcFunc = have($tc['function']);
        $tcLine = have($tc['line']);

        $filePath .= "<div>" .
            (
                $pix->local ?
                ehGetLink($tcFile, $tc['line']) :
                $tcFile
            ) . ":<span style='color:skyblue;'>" . ($tc['line'] ?? '--') . "</span>
			" . ($tcFunc ?
                "<br />
			<span style='color:chartreuse;'>
				$tcFunc()
			</span>
			<br /><br />" :
                ''
            ) . "
		</div>";
        $logFlPath .= ($logFlPath ?
            "\r\n\t\t" :
            ''
        ) . ehGetLink($tcFile, $tcLine) . ':' .
            $tcLine . ($tcFunc ?
                ' => ' . $tcFunc . '()' :
                ''
            ) . '<br />';
    }

    if ($pix->local) {
        echo '<div 
            style="
                margin: 29px 15px; 
                background-color: #393939; 
                border-radius: 5px; 
                padding: 26px 30px;
                font-family: consolas, \'Lucida Console\', arial;
                font-size: 15px;
            "
        >
            <div 
                style="
                    color: #fff;
                    font-size: 21px;
                    margin-bottom: 12px;
                "
            >
                ' . $error . '
            </div>
            <div 
                style="
                    color: #ff0;
                    margin-bottom: 16px;
                "
            >
                ' . $filePath . '
            </div>
        </div>';
    }

    $errorId = $pix->makestring(60, 'ln');

    $errorStr = "\n\n\n" . '<!-- err-start-' . $errorId . ' -->
    <table cellpadding="7" cellspacing="0" style="margin-bottom:100px; line-height:15px;"  class="errorbox" id="err_' . $errorId . '">
		<tr>
			<td style="padding-right:30px;color: #ff0;">URL</td>
			<td>
				' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Unknown') . '
			</td>
		</tr>
		<tr>
			<td style="padding-right:30px;color: #ff0; vertical-align: top;">Error</td>
			<td>' . $error . '</td>
		</tr>
		<tr>
			<td style="padding-right:30px;color: #ff0;">File</td>
			<td>
				' . ehGetLink($file, $line) . '
			</td>
		</tr>
		<tr>
			<td style="padding-right:30px;color: #ff0;vertical-align: top;">Trace</td>
			<td>' . $logFlPath . '</td>
		</tr>
		<tr>
			<td style="padding-right:30px;color: #ff0;">Line</td>
			<td>' . $line . '</td>
		</tr>
		<tr>
			<td style="padding-right:30px;color: #ff0;">Time</td>
			<td>' . date('l d F y - h:i:s a') . '</td>
		</tr>
	</table>
    <!-- err-end-' . $errorId . ' -->';

    $errorDir = $pix->basedir . 'errors/';
    if (!is_dir($errorDir)) {
        mkdir($errorDir, 0755, true);
    }
    $errLogNode = fopen($errorDir . date('Y-m-d-H') . '.txt', 'a+');
    fwrite($errLogNode, $errorStr);
    fclose($errLogNode);
}
function ehGetLink($file, $line = false)
{
    if (ISLOCAL) {
        return '<a href="vscode://file/' . $file . ($line !== false ? ':' . $line : '') . '" style="color:#00e7ff;">
			' . $file . '
		</a>';
    } else {
        return $file;
    }
}

function devMode()
{
    echo '<style>
	body {background-color: #1a1a1a;color: #fff;font-size: 15px;}
    .xdebug-var-dump {font-family: comic sans ms;letter-spacing: 2px;}
    .xdebug-var-dump b {color: #9acd32;}
    .xdebug-var-dump i {color: #bdbdbd;}
    .xdebug-var-dump small {color: #969696;}
    .xdebug-var-dump font[color="#cc0000"],
    .xdebug-var-dump font[color="#4e9a06"],
    .xdebug-var-dump font[color="#75507b"] {color: #00ff7f;font-weight: bold;}
    .xdebug-var-dump font[color="#3465a4"] {color: #3f7fbb;text-transform: uppercase;}
	</style>';
}
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

function str2url($name, $allow = null)
{
    $file_name = strtolower($name);
    $file_name = preg_replace('/[^0-9a-zA-Z' . ($allow ?: '') . ']/', '-', $file_name);
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
function exf($fname)
{
    return strtolower(preg_replace('/^.*\.(.+?)$/', '$1', $fname));
}
function prettyJson($data)
{
    echo '<pre>', json_encode($data, JSON_PRETTY_PRINT), '</pre>';
}
function isValidImage($file)
{
    return preg_match('/\.(jpe*g|png|gif)$/i', $file['name']) == 1;
}
function pqt($string)
{
    global $pdo;
    return $pdo->quote($string);
}
function money($amt, $dec = true, $hideEndZero = false)
{
    $r = number_format(
        floatval($amt),
        $dec ? 2 : 0,
        '.',
        ','
    );
    if ($hideEndZero) {
        $r = preg_replace('/\.00$/', '', $r);
    }
    return $r;
}
function dollar($amt, $dec = true, $hideEndZero = false)
{
    $isNeg = $amt < 0;
    $money = money($amt, $dec, $hideEndZero);
    if (!$isNeg) {
        return '$' . $money;
    } else {
        return str_replace('-', '-$', $money);
    }
}
function loadStyle($css)
{
    global $pix;
    echo '<link rel="stylesheet" href="', $pix->adminURL, 'assets/css/', $css, '.css?v=', VER, '" />' . "\r\n";
}
function loadScript($js)
{
    global $pix;
    echo '<script src="', $pix->adminURL, 'assets/js/', $js, '.js?v=', VER, '"></script>' . "\r\n";
}
function loadModule($name)
{
    global $pix;

    if (isset($GLOBALS['moduleData' . $name])) {
        return $GLOBALS['moduleData' . $name];
    }

    $modFile = $pix->basedir . 'includes/modules/' . $name . '.php';
    if (is_file($modFile)) {
        $class = require($modFile);
        $GLOBALS['moduleData' . $name] = $class;
        return $class;
        // 
    } else {
        trigger_error('Module file not found');
    }
}
function q($str)
{
    global $db;
    return $db->quote($str);
}
function getRelDate($dt)
{
    global $year, $date;
    $dateFormat = 'Y F';
    if (stripos($dt, $date) !== false) {
        $dateFormat = 'h:i A';
    } elseif (stripos($dt, $year) !== false) {
        $dateFormat = 'd F';
    }
    return date($dateFormat, strtotime($dt));
}
function printCode($str)
{
    echo "<div style='background-color:#333; color:#eee; border-radius:10px; padding:30px;'><pre style='font-family:monospace;'>";
    
    print_r($str);
    echo "</pre></div>";
}
function collectObjData($list, $key)
{
    $datas = [];
    foreach ($list as $li) {
        if ($val = ($li->{$key})) {
            $datas[] = $val;
        }
    }
    return $datas;
}
function getKeys($obj)
{
    $keys = [];
    foreach ($obj as $ik => $iv) {
        $keys[] = $ik;
    }
    return $keys;
}

function formatDate($dte)
{
    global $date;
    $ldDate = substr($dte, 0, 10);
    $daysDiff = round(
        (
            strtotime($date) -
            strtotime($ldDate)
        ) / 86400
    );
    $dayLabel = '';
    $dateFormat = 'h:i A';
    switch ($daysDiff) {
        case 0:
            $dayLabel = 'Today, ';
            break;

        case 1:
            $dayLabel = 'Yesterday, ';
            break;

        default:
            $dateFormat = 'M j, Y';
            break;
    }

    return $dayLabel . date($dateFormat, strtotime($dte));
}

function processQueue($funName, $args)
{
    global $pix;

    $lastId = 0;
    $qDir = $pix->datas . 'queue/';

    if (!is_dir($qDir)) {
        mkdir($qDir, 0755, true);
    }
    $files = array_diff(
        scandir($qDir),
        array('.', '..', 'working')
    );

    if ($files) {
        $fNam = array_pop($files);
        $fNam = pathinfo($fNam, PATHINFO_FILENAME);
        $fNam = ltrim($fNam, '0');
        $lastId = $fNam + 1;
    } else {
        $lastId = 1;
    }

    $qFile = fopen($qDir . str_pad($lastId, 8, '0', STR_PAD_LEFT) . '.txt', 'w');
    $data = (object)[
        'func' => $funName,
        'args' => $args
    ];

    fwrite($qFile, serialize($data));
}

function printCsvData($data)
{
    echo '<table 
        border="1" 
        cellpadding="10" 
        cellspacing="0" 
        style="
            position: relative;
            font-family: monospace;
            background-color: #04202c;
            color: #fff;
        ">';

    $isHead = true;
    foreach ($data as $dr) {
        echo '<tr ' . ($isHead ?
            'style="
            background-color:#000; 
            font-weight:bold; 
            white-space:nowrap; 
            position:sticky; 
            top:0;"' : '') . '>';

        foreach ($dr as $dc) {
            echo '<td>', $dc, '</td>';
        }

        echo '</tr>';

        $isHead = false;
    }

    echo '</table>';
}

// include extra libs
require_once 'evergreen.php';
