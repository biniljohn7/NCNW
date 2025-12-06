<?php
if (!class_exists('accRecovery')) {
    class accRecovery
    {
        public function sendVerification($usr = null, $chngMail = null, $resend = true, $template = null, $otp = false)
        {
            global $pix, $pixdb;

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


                $data = $pixdb->get(
                    'members_verification',
                    [
                        'member' => $usrInfo->id,
                        'single' => 1
                    ]
                );

                if ($data) {
                    $expire     = strtotime($data->expiry);
                    $code       = $data->token;
                    $sCount     = $data->sendCount;
                    $nSendTm    = $data->nxtSendTm;

                    if ($code != '' && $cTime <= $expire) {
                        $new = 0;
                    }
                }

                if ($new || !$resend) {
                    $code       = $otp ? $pix->makeString(4, 'n') : substr_replace($pix->makeString(15, 'l'), $usrInfo->id, rand(0, 10), 0);
                    $expire     = $cTime + 900;
                }

                if (!$resend) {
                    $sCount = 0;
                }

                if ($code != '') {
                    if ($nSendTm <= $cTime) {

                        $tplInfo = $pix->getData('email-templates/account-verification');

                        $mailAttrs = [
                            'BODYTXT' => nl2br($tplInfo->body ?? ''),
                            'BTN-TEXT' => $tplInfo->btntext ?? 'Verify Now',
                            'ALT-LINK-TEXT' => nl2br($tplInfo->altlinktext ?? ''),
                        ];

                        if ($otp) {
                            $mailAttrs = array_merge(
                                $mailAttrs,
                                ['CODE' => $code]
                            );
                        } else {
                            $mailAttrs = array_merge(
                                $mailAttrs,
                                [
                                    'LINK' => $pix->appDomain . 'verification/' . $code,
                                    'VALID' => date('d M - h:i a', $expire),
                                    'USERNAME' => have($usrInfo->firstName)
                                ]
                            );
                        }

                        $tplText = $pix->getData('email-templates/account-verification');

                        $pix->e_mail(
                            $chngMail ? $chngMail : $usrInfo->email,
                            'Action Required: Verify Your Email Address Now',
                            $template ?: 'user-email-verification',
                            $mailAttrs
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

                        $pixdb->insert(
                            'members_verification',
                            [
                                'member' => $usrInfo->id,
                                'token' => $code,
                                'expiry' => date('Y-m-d H:i:s', $expire),
                                'sendCount' => $sCount,
                                'nxtSendTm' => $nSendTm
                            ],
                            true
                        );

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
            global $pix, $pixdb;

            $r = new stdClass();
            $r->verified = false;
            $r->errorMsg = 'Invalid verification request';

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

                $data = $pixdb->get(
                    'members_verification',
                    [
                        'member' => $usrInfo->id,
                        'single' => 1
                    ],
                    'expiry,
                    token'
                );
                if ($data) {
                    $expire     = strtotime($data->expiry);
                    $fetchCode  = $data->token;

                    if ($code == $fetchCode) {
                        if ($cTime <= $expire) {
                            $r->verified = true;
                            if (!$check) {
                                $pixdb->update(
                                    'members',
                                    [
                                        'id' => $usrInfo->id
                                    ],
                                    [
                                        'verified' => 'Y'
                                    ]
                                );
                                $pixdb->delete(
                                    'members_verification',
                                    [
                                        'member' => $usrInfo->id
                                    ]
                                );
                            }
                            unset($r->errorMsg);
                        } else {
                            $otp = preg_match('/^\d{4}$/', $fetchCode);
                            $this->sendVerification($usrInfo, null, true, $otp ? 'otp-send' : 'user-email-verification', $otp);
                            $r->errorMsg = 'The verification token was expired. Now you will receive a new verification details, 
                            which will be good for another 15 minutes';
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
