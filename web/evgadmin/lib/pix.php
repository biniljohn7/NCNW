<?php
class pix
{
    const userSessName = 'userLoginCreds';
    const sessMaxLife = 3600;
    public $domain = '';
    public $adminURL = '';
    public $appDomain = '';
    public $basedir = '';
    public $local = false;
    public $upload_dir = '';
    public $uploadPath = '';
    public $loggedUser = false;
    public $userPerms = null;
    public $reqURI = '';
    public $sessTime = 900;
    public $thumb = array(
        'team-avatar' => array(
            array(
                'width' => 75,
                'height' => 75,
                'thumb' => true
            ),
            array(
                'width' => 100,
                'height' => 100,
                'thumb' => true
            ),
        ),
        'avatars' => [
            [
                'width' => 150,
                'height' => 150,
                'thumb' => true
            ],
            [
                'width' => 350,
                'height' => 350,
                'thumb' => true
            ],
        ],
        'provider-logo-pic' => array(
            array(
                'width' => 150,
                'height' => 150,
                'thumb' => true,
                'scale' => true
            ),
            array(
                'width' => 450,
                'height' => 450,
                'thumb' => true,
                'scale' => true
            ),
            array(
                'width' => 1500,
                'thumb' => false
            )
        ),
        'career-pic' => [
            [
                'width' => 150,
                'height' => 150,
                'thumb' => true,
            ],
            [
                'width' => 450,
                'height' => 450,
                'thumb' => true,
            ],
            [
                'width' => 1500,
                'thumb' => false
            ]
        ],
        'request-files' => [
            [
                'width' => 150,
                'height' => 150,
                'thumb' => true,
                'scale' => true
            ],
            [
                'width' => 350,
                'height' => 350,
                'thumb' => true,
                'scale' => true
            ],
            [
                'width' => 1500,
                'thumb' => false
            ]
        ],
        'events' => [
            [
                'width' => 150,
                'height' => 150,
                'thumb' => true,
            ],
            [
                'width' => 350,
                'height' => 350,
                'thumb' => true,
            ],
            [
                'width' => 750,
                'thumb' => false
            ]
        ],
        'category-icon' => array(
            array(
                'width' => 150,
                'height' => 150,
                'thumb' => true,
                'scale' => true
            ),
            array(
                'width' => 450,
                'height' => 450,
                'thumb' => true,
                'scale' => true
            ),
            array(
                'width' => 1500,
                'thumb' => false
            )
        ),
        'advocacy-icon' => array(
            array(
                'width' => 150,
                'height' => 150,
                'thumb' => true,
                'scale' => true
            ),
            array(
                'width' => 450,
                'height' => 450,
                'thumb' => true,
                'scale' => true
            ),
            array(
                'width' => 1500,
                'thumb' => false
            )
        ),
        'message-photos' => array(
            array(
                'width' => 150,
                'height' => 150,
                'thumb' => true
            )
        )
    );
    public $PROFILE_OPTIONS = [
        'prefix' => [
            2933 => 'Mr.',
            2934 => 'Mrs.',
            2935 => 'Dr.',
            2936 => 'Ms.',
            3475 => 'Ambassador',
            3476 => 'Atty.',
            3477 => 'Bishop',
            3478 => 'Commissioner',
            3479 => 'Elder',
            3480 => 'Judge',
            3481 => 'Mayor',
            3482 => 'Min.',
            3483 => 'Miss',
            3484 => 'Pastor',
            3485 => 'President',
            3486 => 'Representative',
            3487 => 'Rev.',
            3488 => 'Rev. Dr.',
            3489 => 'Senator',
            3490 => 'The Honorable'
        ],
        'suffix' => [
            2937 => 'Jr.',
            2938 => 'Sr.',
            2939 => 'II',
            2940 => 'III',
            3474 => 'IV'
        ],
        'degree' => [
            2941 => "Associate's degree",
            2942 => "Bachelor's degree",
            2943 => "Master's degree",
            2944 => "Doctoral degree",
            2945 => "Professional degree",
            3492 => "Less than high school",
            3493 => "High school diploma or equivalent",
            3494 => "Some college"
        ],
        'expertise' => [
            2959 => 'Analytical',
            2960 => 'Communication',
            2961 => 'Computer',
            2962 => 'Conceptual',
            2963 => 'Core Competencies',
            2964 => 'Creative Thinking',
            2965 => 'Critical Thinking'
        ],
        'salaryRange' => [
            2966 => 'No income',
            2967 => '0 - $40,000',
            2968 => '$40,001 to $80,000',
            2969 => '$80,001 to $120,000',
            2970 => '$120,001 to $160,000',
            3471 => '$160,001 to $200,000',
            3472 => '$200,000 to $240,000',
            3473 => '$240,000+'
        ],
        'houseHold' => [
            2971 => 'Single',
            2972 => 'Single with children',
            2973 => 'Married',
            2974 => 'Married with children',
            3469 => 'Widowed',
            3470 => 'Widowed with children',
            3495 => 'Never Married',
            3496 => 'Separated',
            3497 => 'Divorced'
        ],
        'racialIdentity' => [
            3498 => 'American Indian or Alaska Native',
            3499 => 'Asian',
            3500 => 'Black or African American',
            3501 => 'Hispanic, Latino, or Spanish Origin',
            3502 => 'Middle Eastern or North African',
            3503 => 'Native Hawaiian or Other Pacific Islander',
            3504 => 'White',
            3505 => 'Other (please specify)'
        ],
        'employmentStatus' => [
            3506 => 'Employed full-time',
            3507 => 'Employed part-time',
            3508 => 'Self-employed',
            3509 => 'Unemployed'
        ],
        'volunteerInterest' => [
            3510 => 'Health Equity',
            3511 => 'Education',
            3512 => 'Social Justice',
            3513 => 'Economic Empowerment',
            3514 => 'Mentorship'
        ]
    ];
    public $uploads = '';
    public $datas = '';
    public $db = false;
    public function __construct()
    {
        global $pix_db, $root;
        $this->domain = $root->domain;
        $this->adminURL = $root->domain . 'evgadmin/';
        $this->appDomain = $root->appDomain;
        $this->basedir = $root->basedir;
        $this->uploads = dirname($this->basedir) . '/uploads/';
        $this->uploadPath = $this->domain . 'uploads/';
        $this->datas = $this->basedir . '__datas/';
        $this->local = $root->local;
        $this->db = $pix_db;

        ## fetching requested address
        $reqAdr = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->reqURI = $reqAdr;
    }
    public function display_post($method = '_POST')
    {
        $methodData = $GLOBALS[$method];
        echo "<pre>\n$" . "_ = $" . $method . ";\n\nif(\n\tisset(\n\t";
        $str = "";
        foreach ($methodData as $key => $value) {
            $str .= "\t$" . '_[\'' . $key . '\'],' . "\n\t";
        }
        $str = substr($str, 0, -3);
        echo $str . "\n\t)\n){\n";
        foreach ($methodData as $key => $value) {
            echo "\t$" . "$key = " . 'esc($' . '_[\'' . $key . '\']);' . "\n";
        }
        $str = "\n\tif(\n";
        foreach ($methodData as $key => $value) {
            $str .= "\t\t$" . "$key &&\n";
        }
        echo substr($str, 0, -4) . "\n\t){\n\t\techo 'Hello world !';\n\t}\n}</pre>";
    }
    public function redirect($link = '')
    {
        ob_clean();
        $link = $link == '' ? (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->adminURL) : $link;
        !preg_match('/^http/', $link) ? $link = $this->adminURL . $link : 0;
        header('location:' . $link);
        exit;
    }
    public function getLoggedUser()
    {
        global $pixdb;
        $r = false;
        if ($this->loggedUser) {
            $r = $this->loggedUser;
        } else {
            if (isset($_SESSION[self::userSessName])) {
                $lgInfo = $_SESSION[self::userSessName];
                $cleanSession = true;
                if (
                    isset(
                        $lgInfo->user,
                        $lgInfo->pass,
                        $lgInfo->ip,
                        $lgInfo->exp
                    )
                ) {
                    if (
                        $lgInfo->user &&
                        $lgInfo->pass &&
                        $lgInfo->ip == $_SERVER['REMOTE_ADDR'] &&
                        $lgInfo->exp >= time()
                    ) {
                        $loginData = $pixdb->get(
                            'admins',
                            array(
                                is_mail($lgInfo->user) ?
                                    'email' : 'username' => $lgInfo->user,
                                'single' => 1
                            ),
                            'id,
                            type,
                            enabled,
                            name,
                            username,
                            email,
                            password,
                            perms,
                            memberid'
                        );
                        if (
                            $loginData &&
                            $loginData->enabled == 'Y' &&
                            $loginData->password == $lgInfo->pass
                        ) {
                            $r = $loginData;

                            $this->loggedUser = $r;
                            $cleanSession = false;
                            $_SESSION[self::userSessName]->exp = time() + self::sessMaxLife;
                        }
                    }
                }
                if ($cleanSession) {
                    unset($_SESSION[self::userSessName]);
                }
            }
        }
        return $r;
    }
    public function canAccess($perm)
    {
        $lgUser = $this->getLoggedUser();
        if ($lgUser) {
            if ($lgUser->type == 'admin') {
                return true;
            } else if (
                $lgUser->type == 'section-president' &&
                ($perm == 'elect' || $perm == 'members')
            ) {
                return true;
            } elseif (
                $lgUser->type == 'state-leader' &&
                ($perm == 'elect' || $perm == 'members')
            ) {
                return true;
            } elseif (
                in_array(
                    $lgUser->type,
                    [
                        'officer-president',
                        'first-vice-president',
                        'second-vice-president',
                        'treasurer',
                        'collegiate-liaison'
                    ]
                ) &&
                $perm == 'members'
            ) {
                return true;
            } else {
                $perms = $this->getUserPermissions($lgUser);
                return isset($perms[$perm]);
            }
        }
        return false;
    }
    public function getUserPermissions($usr)
    {
        if (!$this->userPerms) {
            $perms = array_flip(
                array_filter(
                    explode(',', $usr->perms ?: '')
                )
            );
            $this->userPerms = $perms;
        } else {
            $perms = $this->userPerms;
        }
        return $perms;
    }
    public function addmsg(
        $msg = '',
        $code = 0
    ) {
        if (!$msg) {
            $msg = 'Sorry. We are unable to complete your action. Please try again.';
        }
        setcookie(
            'sysmsg',
            "$code:$msg",
            time() + 3600,
            '/'
        );
    }
    public function remsg()
    {
        setcookie('sysmsg', '', time() + 86400, '/');
    }
    public function getmsg()
    {
        if (isset($_COOKIE['sysmsg'])) {
            $msgData = $_COOKIE['sysmsg'];
            if ($msgData) {
                $msgCode = $msgData[0];
                $msgFunc = 'showError';
                switch ($msgCode) {
                    case '0':
                        $msgFunc = 'showError';
                        break;
                    case '1':
                        $msgFunc = 'showSuccess';
                        break;
                }
                echo '<script>
                	$(document).ready(function(){
                		popup.', $msgFunc, '(', json_encode(substr($msgData, 2)), ');
                	});
                </script>';
                $this->remsg();
            }
        }
    }
    public function encrypt($s)
    {
        return sha1($s . '#g6een');
    }
    public function removeFile($file)
    {
        if (
            $file &&
            is_file($file)
        ) {
            unlink($file);
        }
    }
    public function avatar($url, $size = '150x150', $uplFolder = 'avatar')
    {
        global $pix;
        return $pix->domain . (
            $url ?
            'uploads/' . $uplFolder . '/' . $pix->get_file_variation($url, $size) :
            'assets/images/avatar.svg'
        );
    }
    public function get_file_variation($file, $index)
    {
        return preg_replace(
            '/\.(.{2,4})$/i',
            '-' . $index . '.$1',
            $file ?? ''
        );
    }
    public function json($obj)
    {
        ob_clean();
        header('content-type:application/json');
        echo json_encode($obj);
        exit;
    }
    public function getPageNum($var = 'pgn')
    {
        return isset($_GET[$var]) ? max(0, intval($_GET[$var])) : 0;
    }
    public function remove_query_arg($key, $link = "current")
    {
        $link = $link == "current" ? $_SERVER["REQUEST_URI"] : $link;
        $key = preg_quote($key);
        $link = preg_replace('/(\&|\?)' . $key . '(\=*(([0-9a-zA-Z-_]{1,})*))/', '$1', $link);
        $link = preg_replace('/\&+/', '&', $link);
        $link = preg_replace('/(\&|\?)$/', '', $link);
        $link = preg_replace('/\?\&/', '?', $link);
        return $link;
    }
    public function pagination(
        $total,
        $current,
        $length = 5,
        $link = null,
        $classes = '',
        $begin_from = 0,
        $getVar = 'pgn',
        $hash = null
    ) {
        if (!$link) {
            $baseLink = $this->remove_query_arg($getVar);
            $joiner = preg_match('/\?/', $baseLink) ? '&' : '?';
            $link = $baseLink . $joiner . $getVar . '=%s';
        }

        $start_link = preg_replace(
            '/(\?|\&)$/',
            '',
            preg_replace(
                '/([0-9a-zA-Z]){1,}\=\%s(\&*)/',
                '',
                $link
            )
        );
        if ($total > 1) {
            echo '<div class="pagination ', $classes, '">';

            $start = $current - $length;
            $end = $current + $length;
            if ($start < $begin_from) {
                $end += $begin_from - $start;
                $start = $begin_from;
            }
            $tmp_total = $begin_from == 1 ? $total : ($total - 1);
            if ($end > $tmp_total) {
                $end = $tmp_total;
            }
            if (($end - $start) < ($length * 2)) {
                $start -= ($length * 2) - ($end - $start);
                $start = $start < $begin_from ? $begin_from : $start;
            }
            if ($hash) {
                $hash = '#' . $hash;
            }
            if ($start > $begin_from) {
                echo '<a class="quick-nav" href="' . $start_link . $hash . '">First</a>';
            }
            if ($current > $begin_from) {
                echo  '<a class="nxt-nav" href="' . (
                    ($current == 1 ?
                        $start_link :
                        str_replace('%s', $current - 1, $link)
                    ) .
                    $hash
                ) . '">Prev</a>';
            }
            if (
                $start > $begin_from ||
                $current > $begin_from
            ) {
                echo  '<span class="separator">..</span>';
            }
            for ($i = $start; $i <= $end; $i++) {
                $j = $begin_from == 1 ? $i : ($i + 1);
                echo '<a ', (
                    $i == $current ?
                    ' class="active"' :
                    ''
                ),
                ' href="', (
                    (
                        $i == $begin_from ?
                        $start_link :
                        str_replace('%s', $i, $link)
                    ) . $hash
                ), '">', $j, '</a>';
            }
            if (
                $current < $tmp_total ||
                $end < $tmp_total
            ) {
                echo  '<span class="separator">..</span>';
            }
            if ($current < $tmp_total) {
                echo  '<a class="nxt-nav" href="',
                str_replace('%s', $current + 1, $link),
                $hash,
                '">Next</a>';
            }
            if ($end < $tmp_total) {
                echo  '<a class="quick-nav" href="',
                str_replace('%s', $tmp_total, $link),
                $hash,
                '">Last</a>';
            }

            echo '</div>';
        }
    }
    public function showDate($date, $time = true, $showYear = false, $showTodayDate = true)
    {
        global $year, $curTime;
        $date = date(
            'd M Y' . ($time ? ' - h:i A' : ''),
            strtotime($date)
        );
        if (!$showTodayDate) {
            $date = str_replace(
                date('d M Y', $curTime) . ' - ',
                '',
                $date
            );
        }
        if (!$showYear) {
            $date = str_replace(
                ' ' . $year,
                '',
                $date
            );
        }
        return $date;
    }
    public function get_new_slug($name, $id = false, $table)
    {
        global $pixdb;
        $slug = str2url($name);
        if ($pixdb->get($table, array('slug' => $slug, '#QRY' => $id ? 'id!=' . intval($id) : '', 'single' => 1), 'id')) {
            $slugIndex = 1;
            $lastIndex = $pixdb->get($table, array(
                '#QRY' => 'slug regexp "' . preg_quote($slug) . '\-[0-9]{1,}$"',
                '#SRT' => 'replace(slug, "' . escape($slug) . '-", "")*1 desc limit 1',
                'single' => 1
            ), 'slug');
            if ($lastIndex) {
                $slugIndex = preg_replace('/^.*\-(\d{1,})$/', '$1', $lastIndex->slug) + 1;
            }
            $slug .= '-' . $slugIndex;
        }
        return $slug;
    }
    public function setDateDir($uploadDir)
    {
        $r = new stdClass;
        $dateDir = date('Y/m/d/');
        $dirBase = $this->uploads . '' . $uploadDir . '/';

        if (!is_dir($dirBase . $dateDir)) {
            mkdir($dirBase . $dateDir, 0755, true);
        }

        $r->uplroot = $dateDir;
        $r->uplpath = $this->domain . 'uploads/' . $uploadDir . '/';
        $r->abspath = $r->uplpath . $dateDir;
        $r->absdir = $dirBase . $dateDir;
        $r->upldir = $dirBase;

        return $r;
    }
    public function addIMG(
        $file,
        $folder,
        $name = 'random',
        $width = 300,
        $height = 'auto',
        $thump = false,
        $top = false,
        $scale = false,
        $flags = ''
    ) {
        $ret = '';
        $forceJpeg = preg_match('/FJPEG/', $flags);
        if (isset($file)) {
            $ext = exf($file['name']);
            if (preg_match('/^\[(.+?)\]$/i', $folder)) {
                $folder = $this->upload_dir . preg_replace('/^\[|\]$/', '', $folder) . '/';
                if (!is_dir($folder)) {
                    mkdir($folder);
                }
            }
            $name = $name == 'random' ? $this->makestring(40, 'ln') : $name;
            $path = $folder . $name . '.' . $ext;
            $name = $name . '.' . $ext;
            if ($file['tmp_name'] != '') {
                if (copy($file['tmp_name'], $path)) {
                    $proc = false;
                    $imgSize = getimagesize($path);
                    $imgWd = isset($imgSize[0]) ? $imgSize[0] : 1;
                    $imgHt = isset($imgSize[1]) ? $imgSize[1] : 1;
                    if (($imgWd * $imgHt) <= 25000000) {
                        $proc = $this->resize_image($path, $width, $height, $thump, $top, $scale, $flags);
                    }
                    if ($proc) {
                        if ($forceJpeg) {
                            $this->changeExt($path, 'jpg');
                            $name = preg_replace('/\.[a-z0-9]{1,}$/i', '.jpg', $name);
                        }
                        $ret = $name;
                    } else {
                        $this->removeFile($path);
                    }
                }
            }
        }
        return $ret;
    }
    public function resize_image(
        $image,
        $width = 300,
        $height = 'auto',
        $thump = false,
        $top = false,
        $scale = false,
        $flags = ''
    ) {
        $rtn = false;
        $forceJpeg = preg_match('/FJPEG/', $flags);
        if (file_exists($image) && !is_dir($image)) {
            $ext = exf($image);
            $cnc_func = '';
            $out_fnc = '';
            if ($ext == 'jpg' || $ext == 'jpeg') {
                $cnc_func = 'imagecreatefromjpeg';
                $out_fnc = 'imagejpeg';
            } else if ($ext == 'png') {
                $cnc_func = 'imagecreatefrompng';
                $out_fnc = 'imagepng';
            } else if ($ext == 'gif') {
                $cnc_func = 'imagecreatefromgif';
                $out_fnc = 'imagegif';
            }
            if ($cnc_func != '') {
                $source = call_user_func($cnc_func, $image);
                $source_width = imagesx($source);
                $source_height = imagesy($source);
                $destination_x = 0;
                $destination_y = 0;
                $destination_width = $source_width;
                $destination_height = $source_height;
                $range = $this->limit_range($source_width, $source_height, $width, $height, ($thump ? ($scale ? 'inner' : 'outer') : 'normal'));
                $destination_width = $range->width;
                $destination_height = $range->height;
                if ($thump) {
                    $destination_x = round((($destination_width - $width) / 2)) * -1;
                    $destination_y = $top ? 0 : round((($destination_height - $height) / 2)) * -1;
                }
                $canvas_width = $thump ? $width : $destination_width;
                $canvas_height = $thump ? $height : $destination_height;
                $newImg = imagecreatetruecolor($canvas_width, $canvas_height) ?: false;
                ## get source solor
                if ($forceJpeg) {
                    $rgb = array('red' => 255, 'green' => 255, 'blue' => 255);
                } else {
                    $fetchColor = imagecolorat($source, 0, 0);
                    $rgb = imagecolorsforindex($source, $fetchColor);
                }
                if (!$forceJpeg && $ext == 'png') {
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg, true);
                    $transparent = imagecolorallocatealpha($newImg, $rgb['red'], $rgb['green'], $rgb['blue'], $rgb['alpha']);
                    imagefilledrectangle($newImg, 0, 0, $canvas_width, $canvas_height, $transparent);
                } else {
                    $white_im = imagecreate($canvas_width, $canvas_height);
                    imagecolorallocate($white_im, $rgb['red'], $rgb['green'], $rgb['blue']);
                    imagecopyresampled($newImg, $white_im, 0, 0, 0, 0, $canvas_width, $canvas_height, $source_width, $source_height);
                }
                imagecopyresampled($newImg, $source, $destination_x, $destination_y, 0, 0, $destination_width, $destination_height, $source_width, $source_height);
                $forceJpeg ? $out_fnc = 'imagejpeg' : 0;
                call_user_func($out_fnc, $newImg, $image);
                imagedestroy($newImg);
                $forceJpeg ? $this->changeExt($image, 'jpg') : 0;
                $rtn = true;
            }
        }
        return $rtn;
    }
    public function changeExt($file, $ext)
    {
        $newFileName = preg_replace('/\.([a-z0-9]{2,})$/i', '.' . $ext, $file);
        is_file($file) ? rename($file, $newFileName) : 0;
        return $newFileName;
    }
    public function crop_image(
        $image,
        $x,
        $y,
        $width,
        $height
    ) {
        if (file_exists($image) && !is_dir($image) && $width > 0 && $height > 0) {
            $ext = exf($image);
            $cnc_func = '';
            $out_fnc = '';
            if (($ext == 'jpg' || $ext == 'jpeg')) {
                $cnc_func = 'imagecreatefromjpeg';
                $out_fnc = 'imagejpeg';
            } else if ($ext == 'png') {
                $cnc_func = 'imagecreatefrompng';
                $out_fnc = 'imagepng';
            } else if ($ext == 'gif') {
                $cnc_func = 'imagecreatefromgif';
                $out_fnc = 'imagegif';
            }
            if ($cnc_func != '') {
                $image_data = call_user_func($cnc_func, $image);
                $image_width = imagesx($image_data);
                $image_height = imagesy($image_data);
                ## get source solor
                $fetchColor = imagecolorat($image_data, 0, 0);
                $rgb = imagecolorsforindex($image_data, $fetchColor);
                if ($width > 0 && $height > 0) {
                    $canvas = imagecreatetruecolor($width, $height);
                    if ($ext == 'png') {
                        imagealphablending($canvas, false);
                        imagesavealpha($canvas, true);
                        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
                        imagefilledrectangle($canvas, 0, 0, $width, $height, $transparent);
                    } else {
                        $white_im = imagecreate($width, $height);
                        imagecolorallocate($white_im, $rgb['red'], $rgb['green'], $rgb['blue']);
                        imagecopyresampled($canvas, $white_im, 0, 0, 0, 0, $width, $height, $width, $height);
                    }
                    $dx = $x < 0 ? 0 - $x : 0;
                    $dy = $y < 0 ? 0 - $y : 0;
                    $sx = $x > 0 ? $x : 0;
                    $sy = $y > 0 ? $y : 0;
                    $image_width -= max(0, $x);
                    $image_height -= max(0, $y);
                    imagecopyresampled($canvas, $image_data, $dx, $dy, $sx, $sy, $image_width, $image_height, $image_width, $image_height);
                    call_user_func($out_fnc, $canvas, $image);
                    imagedestroy($canvas);
                }
            }
        }
    }
    public function limit_range(
        $org_width,
        $org_height,
        $tgt_width,
        $tgt_height = 'auto',
        $mode = 'normal'
    ) {
        $response = new stdClass();
        $response->width = $org_width;
        $response->height = $org_height;
        $autoheight = $tgt_height == 'auto';
        if ($org_width > $tgt_width || (!$autoheight && $org_height > $tgt_height)) {
            if ($mode == 'outer') {
                $base = $org_height < $org_width ? $org_width / $tgt_width : $org_height / $tgt_height;
                $response->width = round($org_width / $base);
                $response->height = round($org_height / $base);
                if ($response->width < $tgt_width) {
                    $base = $response->width / $tgt_height;
                    $response->width = round($response->width / $base);
                    $response->height = round($response->height / $base);
                }
                if ($response->height < $tgt_height) {
                    $base = $response->height / $tgt_height;
                    $response->width = round($response->width / $base);
                    $response->height = round($response->height / $base);
                }
            } else {
                $base = max(($org_width / $tgt_width), (!$autoheight ? $org_height / $tgt_height : 0));
                $response->width = max(1, round($org_width / $base));
                $response->height = max(1, round($org_height / $base));
            }
        }
        return $response;
    }
    public function makestring($len = 6, $chars = 'uln')
    {
        $pattern = '';
        $pattern .= preg_match('/n/', $chars) ? '1234567890' : '';
        $pattern .= preg_match('/l/', $chars) ? 'abcdefghijklmnopqrstuvwxyz' : '';
        $pattern .= preg_match('/u/', $chars) ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' : '';
        $pattern .= preg_match('/s/', $chars) ? '@#$&*-_+' : '';
        $str = '';
        if (strlen($pattern) > 0) {
            while (strlen($str) < $len) {
                $str .= $pattern[rand(0, strlen($pattern) - 1)];
            }
        }
        return $str;
    }
    public function make_thumb($type, $file_root)
    {
        global $pix;
        if (isset($this->thumb[$type])) {
            foreach ($this->thumb[$type] as $thumb) {
                if (isset($thumb["width"])) {
                    $width = $thumb["width"];
                    $height = isset($thumb["height"]) ? $thumb["height"] : "auto";
                    $variant = $height == "auto" ? 'w' . $width : $width . 'x' . $height;
                    $generated_file = $pix->make_file_variation($file_root, $variant);
                    $thumb_size = isset($thumb["thumb"]) ? $thumb["thumb"] : false;
                    $top = isset($thumb["top"]) ? $thumb["top"] : false;
                    $scale = isset($thumb["scale"]) ? $thumb["scale"] : false;
                    $pix->resize_image($generated_file, $width, $height, $thumb_size, $top, $scale);
                }
            }
        }
    }
    public function cleanThumb($type, $fileRoot, $cleanSource = true)
    {
        global $pix;
        if (isset($this->thumb[$type])) {
            foreach ($this->thumb[$type] as $thumb) {
                if (isset($thumb['width'])) {
                    $width = $thumb['width'];
                    $height = isset($thumb['height']) ? $thumb['height'] : 'auto';
                    $variant = $height == 'auto' ? 'w' . $width : $width . 'x' . $height;
                    $pix->removeFile($pix->get_file_variation($fileRoot, $variant));
                }
            }
        }
        if ($cleanSource) {
            $pix->removeFile($fileRoot);

            $parentDir = $fileRoot;
            for ($i = 0; $i < 3; $i++) {
                $parentDir = preg_replace('/\/[^\/]{1,}$/', '', $parentDir);
                if (is_dir($parentDir) && count(scandir($parentDir)) < 3) {
                    rmdir($parentDir);
                }
            }
        }
    }
    public function thumb($file, $size)
    {
        return $this->get_file_variation($file, $size);
    }
    public function make_file_variation($file, $index)
    {
        $new_file = '';
        if (is_file($file)) {
            $file_name = $this->get_file_variation($file, $index);
            if (copy($file, $file_name)) {
                $new_file = $file_name;
            }
        }
        return $new_file;
    }
    public function convertDate($dt)
    {
        return strtotime(
            str_replace(
                '/',
                '-',
                $dt
            )
        );
    }
    public function num2Suffix($emp)
    {
        $suffix = '';
        $shNum = $emp;
        if ($emp >= 1000000000000) {
            $suffix = 'T';
            $shNum = floor($emp / 1000000000000);
        } else  if ($emp >= 1000000000) {
            $suffix = 'B';
            $shNum = floor($emp / 1000000000);
        } else  if ($emp >= 1000000) {
            $suffix = 'M';
            $shNum = floor($emp / 1000000);
        } else  if ($emp >= 1000) {
            $suffix = 'K';
            $shNum = floor($emp / 1000);
        }
        return $shNum . $suffix;
    }
    public function getPluralText($num, $term, $suffix = false)
    {
        $term .= $num > 1 ? 's' : '';
        return  $num == 0 ?
            'No ' . $term : (
                $suffix ?
                $this->num2Suffix($num) :
                $num
            ) . ' ' .
            $term;
    }
    public function getExp($stDate)
    {
        $exp = 0;
        if ($stDate) {
            $from   = new DateTime($stDate);
            $to     = new DateTime('today');
            $exp    = $from->diff($to)->y;
        }
        return $exp . '+ yr' . ($exp != 1 ? 's' : '');
    }
    public function e_mail(
        $to,
        $subject,
        $template,
        $args,
        $tplbase = 'email',
        $baseArgs = array()
    ) {
        $r         = new stdClass();
        $r->sent   = false;
        $to       = is_array($to) ? $to : array($to);
        if ($subject && $template) {
            $template_file = $this->basedir . "templates/$template.html";
            $emailString = '';
            if (is_file($template_file)) {
                $templateCode = @file_get_contents($template_file);
                $templateCode = is_string($templateCode) ? $templateCode : '';
                $templateCode = preg_replace('/(\r\n)/', ' ', $templateCode);
                $templateCode = preg_replace('/ {2,}/', ' ', $templateCode);

                // if condition
                preg_match_all('/\{\{ *if\.(.+?) *}}/', $templateCode, $ifs);
                if (
                    isset($ifs[1]) &&
                    !empty($ifs[1])
                ) {
                    $ifNames = array_unique($ifs[1]);
                    foreach ($ifNames as $ifName) {
                        $ifName = trim($ifName);

                        preg_match_all(
                            '/\{\{ *if\.' . $ifName . ' *\}\}(.+?)\{\{ *endif\.' . $ifName . ' *\}\}/',
                            $templateCode,
                            $ifMatches
                        );
                        $ifData = $args[$ifName] ?? false;
                        foreach ($ifMatches[0] as $i => $condStr) {
                            $replStr = $ifMatches[1][$i];
                            $rplParts = preg_split('/\{\{ *else\.' . $ifName . ' *\}\}/i', $replStr);
                            $templateCode = str_replace(
                                $condStr,
                                $ifData ?
                                    $rplParts[0] :
                                    $rplParts[1] ?? '',
                                $templateCode
                            );
                        }
                    }
                }

                // loop
                preg_match_all('/\{\{ *loop\.(.+?) as/', $templateCode, $loops);
                if (isset($loops[1]) && !empty($loops[1])) {
                    $loopNames = array_unique($loops[1]);
                    foreach ($loopNames as $loopName) {
                        $loopName = trim($loopName);

                        preg_match_all('/\{\{ *loop\.' . $loopName . ' as ([a-zA-Z0-9\_]{1,}) *\}\}(.+?)\{\{ *endloop\.' . $loopName . ' *\}\}/', $templateCode, $loopMatches);
                        if (isset($loopMatches[1]) && is_array($loopMatches[1])) {
                            foreach ($loopMatches[1] as $lk => $loopVar) {
                                $loopRowStr   = '';
                                $loopStr   = isset($loopMatches[2][$lk]) ? $loopMatches[2][$lk] : '';
                                preg_match_all('/\{\{ *' . $loopVar . '.(.+?) *\}\}/', $loopStr, $lRowMatch);
                                if (isset($args[$loopName]) && is_array($args[$loopName])) {
                                    $loopData = $args[$loopName];
                                    foreach ($loopData as $lpRow) {
                                        if (!is_array($lpRow)) {
                                            $lpRow = (array)$lpRow;
                                        }
                                        $rowStr = $loopStr;
                                        if (isset($lRowMatch[1]) && is_array($lRowMatch[1])) {
                                            foreach ($lRowMatch[1] as $lrkey => $lrVar) {
                                                $lrDataTxt = $lpRow[$lrVar] ? $lpRow[$lrVar] : '';
                                                $lrDataFnd = isset($lRowMatch[0][$lrkey]) ? $lRowMatch[0][$lrkey] : '';
                                                $rowStr = str_replace($lrDataFnd, $lrDataTxt, $rowStr);
                                            }
                                        }
                                        $loopRowStr .= $rowStr;
                                    }
                                }
                                $loopFindStr = isset($loopMatches[0][$lk]) ? $loopMatches[0][$lk] : '';
                                if ($loopFindStr) {
                                    $templateCode = str_replace($loopMatches[0][$lk], $loopRowStr, $templateCode);
                                }
                            }
                        }
                    }
                }

                $matches = array('DOMAIN');
                $replaces = array($this->domain);
                foreach ($args as $key => $value) {
                    $matches[] = "[TMPL-$key]";
                    if (
                        is_string($value) ||
                        is_numeric($value)
                    ) {
                        $replaces[] = $value;
                    } else {
                        $replaces[] = '--';
                    }
                }

                $emailString = str_replace($matches, $replaces, $templateCode);
            }
            foreach ($to as $mailTo) {
                $this->send_mail($mailTo, $subject, $emailString, $tplbase, $baseArgs);
            }
            $r->sent = true;
        }
        return $r;
    }
    public function send_mail(
        $to,
        $subject,
        $content,
        $tplbase = 'email',
        $baseArgs = array()
    ) {
        global $year;
        $template = $this->basedir . 'templates/' . $tplbase . '.html';
        $template = is_file($template) ? @file_get_contents($template) : '';
        $template = $template == false ? '' : $template;

        preg_match_all('/\{\{ml_lbl\.(.*?)\}\}/', $content, $matches);
        if (isset($matches[1])) {
            $target = $matches[1];
            $keyFile = '';
            if (isset($target[0])) {
                $keyFile = substr($target[0], 0, strpos($target[0], '.'));
            }
            if ($keyFile != '') {
                $info = $this->getData("email-templates/$keyFile");

                $subject = ($info->subject ?? 0) ?: $subject;

                $toFind = $toRepl = array();
                foreach ($matches[1] as $match) {
                    $tmp = substr($match, strpos($match, '.') + 1);
                    $toFind[] = "{{ml_lbl.$match}}";
                    $toRepl[] = $info->{$tmp} ?? '';
                }
                $content = str_replace($toFind, $toRepl, $content);
            }
        }

        $tplFind  = array('[year]', '[subject]', '[content]', '[domain]');
        $tplRepl  = array($year, $subject, $content, $this->domain);
        foreach ($baseArgs as $agKey => $agVal) {
            $tplFind[] = '[' . $agKey . ']';
            $tplRepl[] = $agVal;
        }

        $template = str_replace($tplFind, $tplRepl, $template);

        // printing preview
        if ($to == 'print') {
            echo "<h1>SUBJECT: $subject</h1><hr />" .
                $template;
            // 
        } else {

            $to = is_array($to) ? $to : array($to);

            if ($this->local) {
                $mailDir = $this->basedir . 'emails/';
                if (!is_dir($mailDir)) {
                    mkdir($mailDir);
                    $h = fopen($mailDir . '.htaccess-local', 'w');
                    fwrite($h, 'Options +Indexes');
                    fclose($h);
                }
                foreach ($to as $mail_to) {
                    $rcpDir = $mailDir . $mail_to . '/';
                    if (!is_dir($rcpDir)) {
                        mkdir($rcpDir);
                    }
                    $mailStr = $template;
                    $mf = fopen($rcpDir . date('YMd-H.i.s') . '.htm', 'w');
                    fwrite($mf, $mailStr);
                    fclose($mf);
                }
            } else {

                if ($_SERVER['REMOTE_ADDR'] == '103.182.166.71') {
                    // if ($_SERVER['REMOTE_ADDR'] == '::1') {










                    $to = 'binilweb@gmail.com';









                    include 'mailgun.php';
                    $mg = new Mailgun();
                    $send = $mg->send(
                        $to,
                        $subject,
                        $template
                    );
                    // 
                } else {
                    $from = 'NCNW Communications <support@ncnw.org>';
                    $replyTo = 'support@ncnw.org';

                    $to = implode(', ', $to);
                    $mailheaders =   'MIME-Version: 1.0' . "\r\n" .
                        'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                        'From: ' . $from . "\r\n" .
                        'Reply-To: ' . $replyTo;

                    mail($to, $subject, $template, $mailheaders);
                }
            }
        }
    }
    public function getData($type, $decode = true)
    {
        $r = false;
        $dataFile = $this->datas . $type . '.json';
        if (is_file($dataFile)) {
            $data = file_get_contents($dataFile);
            if ($data) {
                $r = json_decode($data);
            }
        }
        if (
            $decode &&
            !is_object($r)
        ) {
            $r = new stdClass();
        }
        return $r;
    }
    public function setData($type, $data)
    {
        $dataFile = $this->datas . $type . '.json';
        $fileDir = dirname($dataFile);
        if (!is_dir($fileDir)) {
            mkdir($fileDir, 0755, true);
        }
        file_put_contents($dataFile, json_encode($data));
        return true;
    }
    public function remove_file($file)
    {
        $file != '' && is_file($file) ? unlink($file) : 0;
    }
    public function deleteDir($path)
    {
        if (is_dir($path) === true) {
            $files = array_diff(
                scandir($path),
                array('.', '..')
            );

            foreach ($files as $file) {
                $this->deleteDir(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        } elseif (is_file($path) === true) {
            return unlink($path);
        }

        return false;
    }
    public function isValidPassword($password)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$/', $password) === 1;
    }

    public function emailQueue($args)
    {
        $args = is_array($args) ? $args : array($args);
        processQueue(['pix', 'e_mail'], $args);
    }

    public function makeMemberId()
    {
        global $pixdb;
        $nMbrId = '';
        while (
            !$nMbrId  || (
                $nMbrId &&
                $pixdb->getRow('members', ['memberId' => $nMbrId], 'id')
            )
        ) {
            $nMbrId = $this->makestring(6, 'un');
        }
        return $nMbrId;
    }

    public function makeTxnId()
    {
        global $pixdb;
        $nTxnId = '';
        while (
            !$nTxnId  || (
                $nTxnId &&
                $pixdb->getRow('transactions', ['txnid' => $nTxnId], 'id')
            )
        ) {
            $nTxnId = $this->makestring(25, 'un');
        }
        return $nTxnId;
    }
    public function unauthApiReqRes()
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Unauthorized access'
        ]);
        exit;
    }
    public function logTmpFile($path, $ttl = 3600)
    {
        global $pixdb;
        $pixdb->insert(
            'tmp_files',
            [
                'path' => $path,
                'exp' => date('Y-m-d H:i:s', time() + $ttl)
            ]
        );
    }
}
