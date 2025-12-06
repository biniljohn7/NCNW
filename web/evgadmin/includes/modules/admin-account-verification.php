<?php
if (!class_exists('accRecovery')) {
    class accRecovery
    {
        public function sendVerification($usr = null, $chngMail = null, $resend = true, $template = null, $mlArgs = [])
        {
            global $pix;

            $r          = new stdClass();
            $r->sent    = false;
            $r->nSendTm = 0;
            $nextSend   = 0;
            $logged     = false;
            $usrInfo    = null;

            if ($usr && $usr->id && $usr->email) {
                $usrInfo = $usr;
            } else {
                $lgUser = $pix->getLoggedUser();
                if ($lgUser) {
                    $usrInfo = $lgUser;
                    $logged = true;
                }
            }
            if ($usrInfo) {
                $new        = 1;
                $cTime      = time();
                $code       = '';
                $sCount     = 0;
                $nSendTm    = 0;

                $dir = $pix->datas . 'email-verification/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $logFile = $dir . $usrInfo->id . '.txt';

                if (is_file($logFile)) {
                    $data = file_get_contents($logFile);
                    if ($data) {
                        $data       = explode('|', $data);
                        $expire     = isset($data[0]) ? $data[0] : 0;
                        $code       = isset($data[1]) ? $data[1] : '';
                        $sCount     = isset($data[2]) ? $data[2] : 0;
                        $nSendTm    = isset($data[3]) ? $data[3] : 0;

                        if ($code != '' && $cTime <= $expire) {
                            $new = 0;
                        }
                    }
                }
                if ($new || !$resend) {
                    $code       = substr_replace($pix->makeString(15, 'l'), $usrInfo->id, rand(0, 10), 0);
                    $expire     = $cTime + 900;
                }

                if (!$resend) {
                    $sCount = 0;
                }

                if ($code != '') {
                    if ($nSendTm <= $cTime) {
                        $pix->e_mail(
                            $chngMail ? $chngMail : $usrInfo->email,
                            'Action Required: Verify Your Email Address Now',
                            $template ?: 'send-account-verification',
                            array_merge(
                                $mlArgs,
                                [
                                    'LINK' => $pix->adminURL . '?page=account-verify&sec=pwd-reset&t=' . $code,
                                    'VALID' => date('d M - h:i a', $expire),
                                    'USERNAME' => have($usrInfo->name)
                                ]
                            )
                        );

                        if ($sCount == 2) {
                            $nextSend = 60;
                        } elseif ($sCount == 3) {
                            $nextSend = 120;
                        } elseif ($sCount == 4) {
                            $nextSend = 180;
                        } elseif ($sCount == 5) {
                            $nextSend = 240;
                        } elseif ($sCount > 4) {
                            $nextSend = 300;
                        }

                        $sCount++;
                        if ($nextSend > 0) {
                            $nSendTm = $cTime + $nextSend;
                        }
                        $logf = fopen($logFile, 'w');
                        // expire|code|resendTimes|NextResendTime
                        fwrite($logf, $expire . '|' . $code . '|' . $sCount . '|' . $nSendTm);
                        fclose($logf);

                        $r->sent = true;
                    } else {
                        $r->errorMsg = 'Please wait few minutes before you try again';
                    }
                } else {
                    $r->errorMsg = 'Failed to send verification mail';
                }
                $r->nSendTm = $nSendTm;
            } else {
                $r->errorMsg = 'Please login to your account';
            }
            return $r;
        }
        public function checkEmailVerifn($code, $usr = null, $check = false, $chngMail = null)
        {
            global $pix;

            $r = new stdClass();
            $r->verified = false;
            $r->errorMsg = 'Invalid account verification request';

            $usrInfo    = null;
            $logged     = false;

            if ($usr && $usr->id && $usr->email) {
                $usrInfo = $usr;
            } else {
                $lgUser = $pix->getLoggedUser();
                if ($lgUser) {
                    $usrInfo = $lgUser;
                    $logged = true;
                }
            }

            if ($usrInfo) {
                $cTime = time();
                $logFile = $pix->datas . 'email-verification/' . $usrInfo->id . '.txt';
                if (is_file($logFile)) {
                    $data = file_get_contents($logFile);
                    if ($data) {
                        $data = explode('|', $data);
                        $expire = isset($data[0]) ? $data[0] : '';
                        $fetchCode = isset($data[1]) ? $data[1] : '';

                        if ($code == $fetchCode) {
                            if ($cTime <= $expire) {
                                $r->verified = true;
                                if (!$check) {
                                    unlink($logFile);
                                }
                                unset($r->errorMsg);
                            } else {
                                $this->sendVerification(null, null, true, 'user-email-verification');
                                $r->errorMsg = 'The verification link was expired. Now you will receive a new verification link, which will be good for another 15 minutes';
                            }
                        }
                    }
                }
            }

            $r->logged = $logged;
            return $r;
        }
    }
}

return new accRecovery();
