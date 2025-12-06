<?php
if (!class_exists('admins')) {
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
        public function checkMemberExist($uname = '', $email = '', $id = 'new')
        {
            global $pixdb;
            $r = false;

            if (
                $uname != '' ||
                is_mail($email)
            ) {
                $new  = $id == 'new';
                $result = $pixdb->fetch(
                    'select
                    id, username, email
                from
                    admins
                where
                    ' . (!$new ?
                        'id!=' . intval($id) . ' and ' :
                        ''
                    ) . '
                    (
                        username like "' . $uname . '" or
                        email like "' . $email . '"
                    )'
                );

                $r = new stdClass();
                $r->exist = $result !== false;
                $r->username = $result && strtolower($result->username) == strtolower($uname);
                $r->email = $result && strtolower($result->email) == strtolower($email);
            }

            return $r;
        }
    }
}

return new members();
