<?php
class Messages
{
    public $db;
    public $joiner = '=====||=====';
    public $timeSep = ':::';
    public $msgMaxLimit = 1000;
    public $time = null;
    public $shwMsgLmt = 50;
    public function __construct()
    {
        global $pixdb, $datetime;
        $this->db = $pixdb;
        $this->time = $datetime;
    }
    public function post(
        $sender,
        $recipient,
        $message,
        $args = []
    ) {
        global $pix;

        $args = (object)$args;

        $r = new stdClass();
        $r->posted = false;
        $r->errorMsg = '';

        $usrInfos = $this->db->fetchAssoc(
            'members',
            array(
                '#QRY' => 'id in (' . $sender . ', ' . $recipient . ')'
            ),
            'id, 
            firstName, 
            lastName',
            'id'
        );

        if (count($usrInfos) == 2) {
            $logInfos = $this->db->fetchAssoc(
                'message_logs',
                array(
                    '#QRY' => '
                        (
                            (
                                user=' . $sender . ' and 
                                sender=' . $recipient . '
                            ) or 
                            (
                                user=' . $recipient . ' and 
                                sender=' . $sender . ' 
                            )
                        )'
                ),
                'user,
                unread',
                'user'
            );

            $rcpIds = array(
                array(
                    $sender,
                    $usrInfos[$recipient],
                    true,
                    0
                ),
                array(
                    $recipient,
                    $usrInfos[$sender],
                    false,
                    (isset($logInfos[$recipient]->unread) ? $logInfos[$recipient]->unread : 0)
                )
            );

            foreach ($rcpIds as $rid) {
                $dbData = array(
                    'user' => $rid[0],
                    'sender' => $rid[1]->id,
                    'lastMsg' => substr($message, 0, 24) . (strlen($message) > 24 ? '..' : ''),
                    'lastMsgOn' => $this->time,
                );
                if (isset($args->sendByTeam)) {
                    $dbData['sendByTeam'] = $args->sendByTeam;
                }
                if (!$rid[2]) {
                    $dbData['unread'] = $rid[3] + 1;
                }
                $this->db->insert(
                    'message_logs',
                    $dbData,
                    true
                );
            }

            $mKey = $pix->makestring(10, 'ln');

            $this->storeMsg(
                $mKey,
                $sender,
                $usrInfos[$recipient],
                $message,
                true
            );

            $this->storeMsg(
                $mKey,
                $recipient,
                $usrInfos[$sender],
                $message,
                false
            );

            $r->posted = true;
            $r->mkey = $mKey;
        }

        return $r;
    }
    public function storeMsg($key, $user, $recipient, $msg, $isSend)
    {
        global $pix;
        $msgDir = $pix->datas . 'messages/' . $user . '/';
        if (!is_dir($msgDir)) {
            mkdir($msgDir, 0755, true);
        }

        $msgFile = $msgDir . $recipient->id . '.txt';
        $msgStr = '';

        if (!is_file($msgFile)) {
            $msgStr .= 'name:' . $recipient->firstName . ' ' . $recipient->lastName . "\n\n";
        }

        $msgStr .= (
            ($isSend ? '>>>>' : '<<<<') .
            '{mkey:' . $key . '}' .
            $this->time . $this->timeSep . $msg .
            "\n\n\n" . $this->joiner . "\n\n\n"
        );

        $fnMsg = fopen($msgFile, 'a+');
        fwrite($fnMsg, $msgStr);
        fclose($fnMsg);
    }
    public function getChat($user, $recipient, $pgn = 0)
    {
        global $pix;

        $r = new stdClass();
        $r->chats = array();
        $r->cacheName = '';
        $r->totalPages = 0;

        $limit = $this->shwMsgLmt;

        $chatFile = $this->getFile($user, $recipient);
        $data = $this->getFileData($user, $recipient);
        if ($data) {
            preg_match_all('/^name\:(.+?)\n/i', $data, $mcs);
            if (isset($mcs[1][0])) {
                $r->cacheName = $mcs[1][0];
            }
            $data = array_filter(
                array_map(
                    'trim',
                    explode(
                        $this->joiner,
                        preg_replace('/^name\:.+?\n/', '', $data)
                    )
                )
            );

            $msgCnt = count($data);

            if ($msgCnt > $this->msgMaxLimit) {
                $allData = $data;
                $data = array_slice(
                    $data,
                    -1 * $this->msgMaxLimit
                );

                $oldData = array_diff($allData, $data);
                foreach ($oldData as $dt) {
                    preg_match(
                        '/\{msgImg\:(.*?)\}/i',
                        $dt,
                        $msgImg
                    );

                    if (isset($msgImg[1])) {
                        $pix->cleanThumb(
                            'message-photos',
                            $pix->uploads . 'message-photos/' . $msgImg[1]
                        );
                    }
                }

                $msgStr = 'name:' . $r->cacheName . "\n\n";
                foreach ($data as $dl) {
                    $msgStr .= $dl . "\n\n\n" . $this->joiner . "\n\n\n";
                }
                file_put_contents($chatFile, $msgStr);
            }

            if ($msgCnt > 0) {
                $pgn += 1;
                if (($pgn * $limit) > $msgCnt) {
                    $sliceLmt = $msgCnt - (($pgn - 1) * $limit);
                } else {
                    $sliceLmt = $limit;
                }
                $data = array_slice($data, - ($pgn * $limit), $sliceLmt);
                $r->totalPages = ceil($msgCnt / $limit);
            }

            $this->db->update(
                'message_logs',
                array(
                    'user' => $user,
                    'sender' => $recipient
                ),
                array(
                    'unread' => 0
                )
            );

            $messages = array();
            if (!empty($data)) {
                $chat = loadModule('chat');

                foreach ($data as $msg) {
                    $isSent = !!preg_match('/^\>{4}/', $msg);
                    $msg = explode(
                        $this->timeSep,
                        substr($msg, 4)
                    );

                    $msgId = null;
                    $msgTime = isset($msg[0]) ? $msg[0] : null;
                    $msgText = have($msg[1]);
                    $msgImg = null;

                    if ($msgTime) {
                        preg_match_all(
                            '/\{mkey\:([0-9a-z]{1,})\}/i',
                            $msgTime,
                            $msgMatch
                        );
                        if (
                            isset(
                                $msgMatch[0][0],
                                $msgMatch[1][0]
                            )
                        ) {
                            $msgId = $msgMatch[1][0];
                            $msgTime = str_replace(
                                $msgMatch[0][0],
                                '',
                                $msgTime
                            );
                        }
                    }

                    if ($msgText) {
                        preg_match_all(
                            '/\{msgImg\:(.*?)\}/i',
                            $msgText,
                            $msgMatch
                        );
                        if (
                            isset(
                                $msgMatch[0][0],
                                $msgMatch[1][0]
                            )
                        ) {
                            $msgText = str_replace(
                                $msgMatch[0][0],
                                '',
                                $msgText
                            );
                            $msgImg = $msgMatch[1][0];
                        }
                    }

                    $msgData = array(
                        'isSent' => $isSent,
                        'id' => $msgId,
                        'time' => $msgTime ? $chat->getDateTime($msgTime) : '',
                        'shortTime' => $msgTime ? $chat->getShortTime($msgTime) : '',
                        'text' => $msgText
                    );

                    if ($msgImg) {
                        $updir = $pix->uploadPath . 'message-photos/';
                        $msgData['msgImg'] = $updir . $pix->thumb(
                            $msgImg,
                            '150x150'
                        );
                    }

                    $messages[] = (object)$msgData;
                }
            }

            $r->chats = $messages;
        }

        return $r;
    }
    public function getFile($user, $recipient)
    {
        global $pix;
        return $pix->datas . 'messages/' . $user . '/' . $recipient . '.txt';
    }
    public function getFileData($user, $recipient)
    {
        $r = false;
        $chatFile = $this->getFile($user, $recipient);
        if (is_file($chatFile)) {
            $r = file_get_contents($chatFile);
        }
        return $r;
    }
    public function getNameFromData($data)
    {
        $r = '';
        preg_match_all('/^name\:(.+?)\n/i', $data, $mcs);
        if (isset($mcs[1][0])) {
            $r = $mcs[1][0];
        }
        return $r;
    }
    public function clear($user, $recipient)
    {
        $data = $this->getFileData($user, $recipient);
        if ($data) {
            $name = $this->getNameFromData($data);
            file_put_contents(
                $this->getFile(
                    $user,
                    $recipient
                ),
                'name:' . $name . "\n\n"
            );
        }
        $this->db->update('message_logs', array('user' => $user, 'sender' => $recipient), array('lastMsg' => null));
    }
    public function deleteMessage($id, $partner, $key)
    {
        global $pix;

        $r = new stdClass();
        $r->deleted = true;
        $r->lastMsg = '';
        $r->time = '';
        $r->shortTime = '';

        $lastMsg = null;
        $lastMsgOn = null;

        $paths = array(
            $id . '/' . $partner,
            $partner . '/' . $id
        );
        $msgDir = $pix->datas . 'messages/';
        foreach ($paths as $path) {
            $msgFile = $msgDir . $path . '.txt';
            if (is_file($msgFile)) {
                $msgs = file_get_contents($msgFile);
                $msgs = str_replace("\n", '__LB__', $msgs);

                if (!is_array($key)) {
                    $key = (array)$key;
                }

                foreach ($key as $mKey) {
                    $pattern = '/[\<\>]{4}\{mkey\:' . $mKey . '\}.+?\={5}\|\|\={5}(__LB__){3}/';
                    preg_match(
                        $pattern,
                        $msgs,
                        $msgMatch
                    );
                    if (isset($msgMatch[0])) {
                        preg_match(
                            '/\{msgImg\:(.*?)\}/i',
                            $msgMatch[0],
                            $msgImg
                        );
                        if (isset($msgImg[1])) {
                            $pix->cleanThumb(
                                'message-photos',
                                $pix->uploads . 'message-photos/' . $msgImg[1]
                            );
                        }
                    }

                    $msgs = preg_replace($pattern, '', $msgs);
                }

                $msgs = str_replace('__LB__', "\n", $msgs);
                file_put_contents($msgFile, $msgs);
            }
        }

        $users = array(
            array($id, $partner),
            array($partner, $id)
        );
        foreach ($users as $usr) {
            $chats = $this->getChat(
                $usr[0],
                $usr[1]
            );
            // $last = array_key_last($chats->chats);
            $last = key(array_slice($chats->chats, -1, 1, true));
            if ($last !== null) {
                $lastChat = $chats->chats[$last];
                $lastMsg = have($lastChat->text);
                $lastMsg = strlen($lastMsg) > 24 ?
                    substr($lastMsg, 0, 24) . '..' :
                    $lastMsg;

                $msgTime = have($lastChat->time, null);
                if ($msgTime) {
                    $msgTime = date_create_from_format('Y M d - h:i A', $msgTime);
                    $lastMsgOn = date_format($msgTime, 'Y-m-d H:i:s');

                    $r->time = $lastChat->time;
                    $r->shortTime = have($lastChat->shortTime);
                }

                $r->lastMsg = $lastMsg;
            }

            $this->db->update(
                'message_logs',
                array(
                    'user' => $usr[0],
                    'sender' => $usr[1]
                ),
                array(
                    'lastMsg' => $lastMsg,
                    'lastMsgOn' => $lastMsgOn
                )
            );
        }

        return $r;
    }
    public function uploadImg(
        $sender,
        $recipient,
        $image
    ) {
        global $pix;

        $r = new stdClass();
        $r->upload = false;
        $r->errorMsg = '';

        if (preg_match('/\.(jpe*g|png|gif)$/i', $image['name'])) {
            $dateDir = $pix->setDateDir('message-photos');
            $uplphoto =  $pix->addIMG($image, $dateDir->absdir, 'random', 1500);
            if ($uplphoto) {
                $absFile = $dateDir->absdir . $uplphoto;
                $imgRoot = $dateDir->uplroot . $uplphoto;

                $pix->make_thumb('message-photos', $absFile);

                $post = $this->post(
                    $sender,
                    $recipient,
                    '{msgImg:' . $imgRoot . '}',
                    array()
                );

                if ($post->posted) {
                    $r->upload = true;
                    $r->mkey = $post->mkey;
                    $r->photo = $dateDir->abspath . $uplphoto;
                    $r->thumb = $dateDir->abspath . $pix->thumb($uplphoto, '150x150');
                }
            }
        } else {
            $r->errormsg = 'Invalid Action';
        }

        return $r;
    }
}
$pixMessages = new Messages();
