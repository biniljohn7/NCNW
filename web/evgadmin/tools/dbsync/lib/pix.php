<?php
class pix
{
    public $domain = "";
    public $basedir = "";
    public $local = false;
    public function __construct()
    {
        global $config;
        // $this->domain       = $root->domain;
        // $this->appUrl       = $root->appUrl;
        $this->basedir = dirname(dirname(__FILE__)) . '/';
        $this->local = $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'ncnw.haguetechnologies.io';

        $ckey = $this->local ? 'local' : 'live';

        $this->domain = $config->{$ckey}->url;

        // $this->upload_dir    = $this->basedir . 'uploads/';
        // $this->db           = $pix_db;

        // ## fetching requested address
        // $reqAdr = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        // $this->reqURI = $reqAdr;
    }
    public function display_post($method = '_POST')
    {
        $methodData = $GLOBALS[$method];
        echo "<pre>\n$" . "_ = $" . $method . ";\nif(isset(\n\t";
        $str = "";
        foreach ($methodData as $key => $value) {
            $str .= '$' . '_[\'' . $key . '\'],' . "\n\t";
        }
        $str = substr($str, 0, -3);
        echo $str . "\n)){\n";
        foreach ($methodData as $key => $value) {
            echo "\t$" . "$key = " . 'esc($' . '_[\'' . $key . '\']);' . "\n";
        }
        $str = "\n\tif(\n";
        foreach ($methodData as $key => $value) {
            $str .= "\t\t$" . "$key &&\n";
        }
        echo substr($str, 0, -4) . "\n\t){\n\t\techo 'Hello world !';\n\t}\n}</pre>";
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
    public function getSyncMarkDir()
    {
        global $config;
        $ckey = $this->local ? 'local' : 'live';
        $dir = $config->{$ckey}->syncDir;
        return $dir;
    }
    public function markSync($key)
    {
        $dir = $this->getSyncMarkDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $lgFile = fopen($dir . 'log.txt', 'a+');
        fwrite($lgFile, $key . "\n");
        fclose($lgFile);
    }
    public function redirect($link = '')
    {
        ob_clean();
        $link = $link == '' ? (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->domain) : $link;
        !preg_match('/^http/', $link) ? $link = $this->domain . $link : 0;
        header('location:' . $link);
        exit;
    }
    public function addmsg($msg = "", $code = 0)
    {
        $msg = $msg == "" ? "Sorry. We are unable to complete your action. Please try again" : $msg;
        $_SESSION["user_message"] = array("code" => $code, "msg" => $msg);
    }
    public function getmsg()
    {
        if (isset($_SESSION["user_message"])) {
            $msg_data = $_SESSION["user_message"];
            $status = "error";
            switch ($msg_data["code"]) {
                case 0:
                    $status = "error";
                    break;
                case 1:
                    $status = "success";
                    break;
                case 2:
                    $status = "warning";
                    break;
            }
            echo '<div class="user-notification ', $status, '">
				<div class="msg-block">
					<div class="got-it" onclick="document.body.removeChild(this.parentNode.parentNode)">Got it !</div>
					<div class="msg-display">', $msg_data['msg'], '</div>
				</div>
			</div><script>closeUserNoti();</script>';
            unset($_SESSION["user_message"]);
        }
    }
    public function json($obj)
    {
        ob_clean();
        header('content-type:application/json');
        echo json_encode($obj);
        exit;
    }
}
$pix = new pix();
