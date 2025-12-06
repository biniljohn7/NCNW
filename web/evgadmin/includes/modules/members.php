<?php
if (!class_exists('members')) {
    class members
    {
        const lgnAtmtFailDir = 'failed-logins';
        const sessMaxLife = 3600;
        const userSessName = 'userLoginCreds';
        const permissions = array(
            'message-send'
        );
        public function getLoginAttempts()
        {
            global $pix;
            $count = 0;
            $file = $pix->datas . self::lgnAtmtFailDir . '/' . str2url($_SERVER['REMOTE_ADDR']) . '.txt';
            if (is_file($file)) {
                $count = intval(
                    file_get_contents($file)
                );
            }
            return $count;
        }
        public function trackFailedLoginAttemp()
        {
            global $pix;
            $dir = $pix->datas . self::lgnAtmtFailDir;
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $count = 0;
            $file = $dir . '/' . str2url($_SERVER['REMOTE_ADDR']) . '.txt';
            if (is_file($file)) {
                $count = intval(
                    file_get_contents($file)
                );
            }
            $count++;
            file_put_contents($file, $count);
        }
        public function storeSession($user, $pass)
        {
            $ssnObj = new stdClass();
            $ssnObj->user = $user;
            $ssnObj->pass = $pass;
            $ssnObj->ip = $_SERVER['REMOTE_ADDR'];
            $ssnObj->exp = time() + self::sessMaxLife;
            $_SESSION[self::userSessName] = $ssnObj;
        }
        public function clearLoginAttempts()
        {
            global $pix;
            $pix->removeFile(
                $pix->datas . self::lgnAtmtFailDir . '/' . str2url($_SERVER['REMOTE_ADDR']) . '.txt'
            );
        }
        public function logout()
        {
            unset($_SESSION[self::userSessName]);
        }
        public function checkMemberExist($email = '', $id = 'new')
        {
            global $pixdb;
            $r = false;

            if (
                is_mail($email)
            ) {
                $new  = $id == 'new';
                $result = $pixdb->fetch(
                    'select
                    id, email
                from
                    members
                where
                    ' . (!$new ?
                        'id!=' . intval($id) . ' and ' :
                        ''
                    ) . '
                    email like "' . $email . '"'
                );

                $r = new stdClass();
                $r->exist = $result !== false;
                $r->email = $result && strtolower($result->email) == strtolower($email);
            }

            return $r;
        }

        public function getRoles($user)
        {
            $ldrshpRoles = [];

            if ($user->role && $user->id) {
                $roles = explode(',', $user->role);
            } else {
                return $ldrshpRoles;
            }

            // The variants to treat as "section-leader"
            $sectnLdrshpTypes = ['section-leader', 'section-president', 'section-delegate'];
            if (array_intersect($roles, $sectnLdrshpTypes)) {
                $roles = array_diff($roles, $sectnLdrshpTypes);
                $roles[] = 'section-leader';
            }
            $roles = array_values($roles);

            if (empty($roles)) return $ldrshpRoles;

            // Map roles to their handler methods
            $roleMap = [
                'state-leader'   => [self::class, 'stateLeaderInfo'],
                'section-leader' => [self::class, 'sectionLeaderInfo'],
                // 'section-president' => [self::class, 'sectionLeaderInfo'],
                // 'section-delegate' => [self::class, 'sectionLeaderInfo'],
                'affiliate-leader' => [self::class, 'affiliateLeaderInfo'],
                'collegiate-leaders' => [self::class, 'collegiateLiaisonInfo'],
                'section-officer' => [self::class, 'electOfficerInfo']
            ];

            foreach ($roles as $role) {
                if (isset($roleMap[$role])) {
                    $data = call_user_func($roleMap[$role], $user);
                    if (!empty($data)) {
                        $ldrshpRoles = array_merge($ldrshpRoles, $data);
                    }
                }
            }

            return $ldrshpRoles;
        }

        public function stateLeaderInfo($user)
        {
            global $pixdb, $evg;

            $info = [];
            $getCircle =  $pixdb->getCol('state_leaders', ['mbrId' => $user->id], 'stateId');
            foreach ($getCircle as $stateId) {
                $state = $evg->getState($stateId, 'id, name');
                if ($state) {
                    $info[] = (object)[
                        'role'   => 'State Leader',
                        'circle' => $state->name
                    ];
                }
            }
            return $info;
        }

        public function sectionLeaderInfo($user)
        {
            global $pixdb, $evg;

            $info = [];
            $getCircle = $pixdb->get('section_leaders', ['mbrId' => $user->id], 'secId, type')->data;
            foreach ($getCircle as $rw) {
                $section  = $evg->getChapter($rw->secId, 'id, name');
                if ($section) {
                    $info[] = (object)[
                        'role'   => 'Section ' . ucfirst($rw->type),
                        'circle' => $section->name
                    ];
                }
            }
            return $info;
        }
        public function affiliateLeaderInfo($user)
        {
            global $pixdb, $evg;

            $info = [];
            $getCircle = $pixdb->getCol('affiliate_leaders', ['mbrId' => $user->id], 'affId');
            foreach ($getCircle as $affId) {
                $affiliate = $evg->getAffiliation($affId, 'id, name');
                if ($affiliate) {
                    $info[] = (object)[
                        'role'   => 'Affiliate Leader',
                        'circle' => $affiliate->name
                    ];
                }
            }
            return $info;
        }
        public function collegiateLiaisonInfo($user)
        {
            global $pixdb, $evg;

            $info = [];
            $getCircle = $pixdb->getCol('collegiate_leaders', ['mbrId' => $user->id], 'coliId');
            foreach ($getCircle as $coliId) {
                $collegateSectn = $evg->getCollgueSection($coliId, 'id, name');
                if ($collegateSectn) {
                    $info[] = (object)[
                        'role'   => 'Collegiate Liaison',
                        'circle' => $collegateSectn->name
                    ];
                }
            }
            return $info;
        }
        public function electOfficerInfo($user)
        {
            global $pixdb, $evg;

            $info = [];
            $getCircle = $pixdb->get('officers', ['memberId' => $user->id], 'title, circle, circleId');
            foreach ($getCircle->data as $rw) {
                $role = $pixdb->getRow('officers_titles', ['id' => $rw->title], 'title');
                if ($rw->circle == 'section') {
                    $section = $evg->getChapter($rw->circleId, 'id, name');
                    if ($section) {
                        $info[] = (object)[
                            'role'   => $role->title,
                            'circle' => $section->name
                        ];
                    }
                } elseif ($rw->circle == 'affiliate') {
                    $affiliate = $evg->getAffiliation($rw->circleId, 'id, name');
                    if ($affiliate) {
                        $info[] = (object)[
                            'role'   => $role->title,
                            'circle' => $affiliate->name
                        ];
                    }
                } elseif ($rw->circle == 'collegiate') {
                    $collegiate = $evg->getCollgueSection($rw->circleId, 'id, name');
                    if ($collegiate) {
                        $info[] = (object)[
                            'role'   => $role->title,
                            'circle' => $collegiate->name
                        ];
                    }
                }
            }
            return $info;
        }
    }
}

return new members();
