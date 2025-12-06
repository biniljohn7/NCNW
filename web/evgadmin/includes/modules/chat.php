<?php
if (!class_exists('chat')) {
    class chat
    {
        public function getDateTime($time)
        {

            return date('Y M d - h:i A', strtotime($time));
        }
        public function getShortTime($time)
        {
            $chatTime = strtotime($time);

            $today = date('d', time());
            $chatDay = date('d', $chatTime);

            $format = 'M Y';
            $diff = time() - $chatTime;

            if ($today == $chatDay) {
                $format = 'h:i A';
            } else if ($diff < 2592000) {
                $format = 'd M';
            }

            return date($format, strtotime($time));
        }
        public function chkAllowMsgng($msngr)
        {
            global $pixdb, $lgUser;

            $ret = false;

            if (is_object($msngr)) {
                $user = $msngr;
            } elseif (is_numeric($msngr)) {
                $user = $pixdb->get(
                    'users',
                    [
                        'id' => $msngr,
                        'single' => 1,
                        '#QRY' => 'id != ' . $lgUser->id
                    ],
                    'id,
                    name,
                    type,
                    avatar,
                    organization,
                    teamWith'
                );
            }

            if ($user) {
                if ($lgUser->type == 'admin') {
                    if ($user->type != 'admin') {
                        $ret = $user;
                    }
                } elseif ($lgUser->type == 'sub-admin') {
                    if ($user->type != 'sub-admin') {
                        $ret = $user;
                    }
                } else {
                    if ($user->type == 'admin') {
                        $ret = $user;
                    } else {
                        // check teammate
                        $inTeam = $pixdb->get(
                            'teams',
                            [
                                '#QRY' => "(organization = $lgUser->id and member = $user->id) or 
                                            (organization = $user->id and member = $lgUser->id)",
                                'single' => 1
                            ],
                            'member'
                        );

                        if ($inTeam) {
                            $ret = $user;
                        } else {

                            // check customer
                            $customer = $pixdb->get(
                                [
                                    ['campaigns', 'c', 'id'],
                                    ['orders', 'o', 'campaign']
                                ],
                                [
                                    '#QRY' => "(
                                            (
                                                c.organization=$lgUser->id" .
                                        ($lgUser->teamWith ?
                                            " or  c.organization=$lgUser->teamWith" :
                                            '') . "
                                            ) and 
                                            o.customer=$user->id
                                        ) or
                                        (c.organization=$user->id and o.customer=$lgUser->id)",
                                    'single' => 1
                                ],
                                'o.id'
                            );

                            if ($customer) {
                                $ret = $user;
                            }
                        }
                    }
                }
            }

            return $ret;
        }
    }
}

return new chat();
