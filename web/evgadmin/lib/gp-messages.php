<?php
class GpMessages
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
        $groupId,
        $message
    ) {
        global $pix;

        $r = new stdClass();
        $r->posted = false;
        $r->errorMsg = '';

        $mKey = $pix->makestring(10, 'ln');

        $this->storeMsg(
            $mKey,
            $groupId,
            $message,
            true
        );
        $this->db->update(
            'message_groups',
            ['id' => $groupId],
            [
                'lastMsg' => substr($message, 0, 24) . (strlen($message) > 24 ? '..' : ''),
                'lastMsgOn' => $this->time
            ]
        );

        $r->posted = true;
        $r->mkey = $mKey;

        return $r;
    }
    public function storeMsg($key, $groupId, $msg, $isSend)
    {
        global $pix, $pixdb;

        $msgDir = $pix->datas . 'gp-messages/';
        if (!is_dir($msgDir)) {
            mkdir($msgDir, 0755, true);
        }

        $msgFile = $msgDir . $groupId . '.txt';
        $msgStr = '';

        if (!is_file($msgFile)) {
            $gpInfo = $pixdb->getRow('message_groups', ['id' => $groupId], 'title');
            if ($gpInfo) {
                $msgStr .= 'name:' . $gpInfo->title . "\n\n";
            }
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
    public function getChat($user, $gpId, $pgn = 0)
    {
        global $pix, $pixdb;

        $r = new stdClass();
        $r->chats = array();
        $r->cacheName = '';
        $r->totalPages = 0;

        if ($user) {
            $isGpMmbr = $pixdb->getRow('message_group_members', ['member' => $user, 'groupId' => $gpId], 'groupId');
            if (!$isGpMmbr) {
                return $r;
            }
        }
        $limit = $this->shwMsgLmt;

        $chatFile = $this->getFile($gpId);
        $data = $this->getFileData($gpId);
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
                        $msgData['msgImg'] = array(
                            'thumb' => $updir . $pix->thumb($msgImg, '150x150'),
                            'image' => $updir . $msgImg
                        );
                    }

                    $messages[] = (object)$msgData;
                }
            }

            $r->chats = $messages;
        }

        return $r;
    }
    public function getFile($gpId)
    {
        global $pix;
        return $pix->datas . 'gp-messages/' . $gpId . '.txt';
    }
    public function getFileData($gpId)
    {
        $r = false;
        $chatFile = $this->getFile($gpId);
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
    public function deleteMessage($groupId, $key)
    {
        global $pix;

        $r = new stdClass();
        $r->deleted = true;
        $r->lastMsg = '';
        $r->time = '';
        $r->shortTime = '';

        $lastMsg = null;
        $lastMsgOn = null;
        $msgFile = $pix->datas . 'gp-messages/' . $groupId . '.txt';
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

        $chats = $this->getChat(
            null,
            $groupId
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
            'message_groups',
            ['id' => $groupId],
            [
                'lastMsg' => $lastMsg,
                'lastMsgOn' => $lastMsgOn
            ]
        );

        return $r;
    }
    public function uploadImg(
        $groupId,
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
                    $groupId,
                    '{msgImg:' . $imgRoot . '}'
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
$pixGpMessages = new GpMessages();
