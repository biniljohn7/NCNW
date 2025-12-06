<?php
$dir = dirname(__FILE__) . '/../../errors/';

function esc($s)
{
    return escape($s, '|html_encode|strip_non_utf8|');
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

## deleting file
if (
    isset(
        $_GET['delfile'],
        $_GET['dir'],
        $_GET['file']
    )
) {
    if ($_GET['dir'] && $_GET['file']) {
        $getDir = esc($_GET['dir']);
        $getFile = esc($_GET['file']);
        if ($getDir && $getFile) {
            $dlDir = preg_replace('/[\/\\\]/', '', $getDir);
            $dlFile = preg_replace('/[\/\\\]/', '', $getFile);
            if ($dlDir && $dlFile) {
                if ($dlDir == 'Base Directory') {
                    $dlDir = '';
                } else {
                    $dlDir .= '/';
                }
                $delFile = $dir . $dlDir . $dlFile;
                if (is_file($delFile)) {
                    unlink($delFile);
                }
            }
        }
    }
    header('location:' . $_SERVER['PHP_SELF']);
}

// delete error line
$_ = $_POST;
if (
    isset(
        $_['id'],
        $_['log']
    )
) {
    $id = str2url($_['id']);
    $log = esc($_['log']);

    if (
        $id &&
        $log
    ) {
        $log = str2url(
            substr($log, 0, 13)
        );

        $logFile = $dir . $log . '.txt';
        if (is_file($logFile)) {
            $erCont = str_replace(
                "\n",
                "[NLINE]",
                file_get_contents($logFile)
            );

            preg_match_all(
                '/<!-- err-start-' . $id . ' -->.+?<!-- err-end-' . $id . ' -->/i',
                $erCont,
                $erMatch
            );

            if (isset($erMatch[0][0])) {
                $erCont = str_replace(
                    $erMatch[0][0],
                    '',
                    $erCont
                );

                $erCont = str_replace(
                    "[NLINE]",
                    "\n",
                    $erCont
                );

                file_put_contents(
                    $logFile,
                    $erCont
                );

                echo 'ok';
            }
        }
    }
    exit;
}

// 
$dirData = [];
if (is_dir($dir)) {
    $dirData = array_reverse(
        array_slice(
            scandir($dir),
            2
        )
    );
}

$errorSize = 0;
$dirClass = array();
foreach ($dirData as $dirItem) {
    if (is_dir($dir . $dirItem)) {
        $subDir = $dir . $dirItem . '/';
        $subScan = array_slice(scandir($subDir), 2);
        foreach ($subScan as $scItem) {
            if (is_file($subDir . $scItem)) {
                $dirClass[$dirItem][] = $scItem;
                $errorSize += filesize($subDir . $scItem);
            }
        }
    } else {
        $dirClass['Base Directory'][] = $dirItem;
        $errorSize += filesize($dir . $dirItem);
    }
}

if (isset($_GET['getnewerror'])) {
    header('content-type:application/json');
    echo json_encode(
        array(
            'size' => $errorSize
        )
    );
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>NCNW - Errors</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js?v=5"></script>
    <script type="text/javascript" src="assets/main.js?v=1"></script>

</head>

<body>
    <div class="err-loader">
        <div class="sidebar">
            <?php
            $initDir = '';
            $initFile = '';

            $loadFile = false;

            if (isset($_GET['getdata'], $_GET['dir'], $_GET['file'])) {
                if ($_GET['dir'] && $_GET['file']) {
                    $getDir = esc($_GET['dir']);
                    $getFile = esc($_GET['file']);
                    if ($getDir && $getFile) {
                        $initDir = preg_replace('/[\/\\\]/', '', $getDir);
                        $initFile = preg_replace('/[\/\\\]/', '', $getFile);
                    }
                }
            }

            foreach ($dirClass as $dirName => $fList) {
            ?>
                <div class="side-head">
                    <div class="link-item">
                        <a href="" class="nav-link">
                            <?php echo $dirName; ?>
                        </a>
                    </div>
                </div>
                <?php
                foreach ($fList as $file) {
                    if (!$initFile) {
                        $initDir = $dirName;
                        $initFile = $file;
                    }
                ?>
                    <div class="side-child">
                        <div class="link-item">
                            <a href="<?php echo $_SERVER['PHP_SELF'], '?getdata=1&dir=', $dirName, '&file=', $file; ?>" class="nav-link <?php echo $initDir == $dirName && $initFile == $file ? 'active' : ''; ?>">
                                <?php echo $file; ?>
                            </a>
                            <a href="<?php echo $_SERVER['PHP_SELF'], '?delfile=1&dir=', $dirName, '&file=', $file; ?>" class="file-del">X</a>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <div class="content-area">
            <?php

            if ($initFile) {
                if ($initDir == 'Base Directory') {
                    $initDir = '';
                } else {
                    $initDir .= '/';
                }
                $loadFile = $dir . $initDir . $initFile;
                if (!is_file($loadFile)) {
                    $loadFile = false;
                }
            }

            if ($loadFile) {
                echo file_get_contents($loadFile);
            }
            ?>
        </div>
    </div>

    <script>
        var errorSize = <?php echo $errorSize; ?>;
    </script>
</body>

</html>