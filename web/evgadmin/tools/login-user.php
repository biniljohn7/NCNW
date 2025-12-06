<?php
include '../lib/lib.php';

$_ = $_POST;

if (isset($_['uid'])) {
    $uid = esc($_['uid']);
    $pass = esc($_['pass'] ?? '');
    $error = 'data not matching';

    if (
        $uid &&
        (
            ISLOCAL ||
            $pass == 'devoctopix'
        )
    ) {
        devMode();
        $user = $pixdb->get(
            'members',
            array(
                'id' => $uid,
                'single' => 1
            ),
            'id,
            email,
            password'
        );
        if ($user) {
            $mToken = $pixdb->getVar(
                'members_auth',
                ['id' => $uid],
                'token'
            );

            if (!$mToken) {
                $mToken = $evg->generateAuthToken();
                $pixdb->insert(
                    'members_auth',
                    [
                        'id' => $uid,
                        'token' => $mToken
                    ],
                    true
                );
            }

            $pix->redirect(
                $pix->appDomain .
                    'usr10lgin8y70k3n/' .
                    $mToken
            );
        }
    }

    echo "<h1>$error</h1>";
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login User Account</title>

    <style>
        body {
            background-color: #020f1a;
            color: #d5d5d5;
            font-size: 16px;
        }

        body * {
            box-sizing: border-box;
            max-width: 100%;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        .login-form {
            display: flex;
            align-items: flex-start;
            position: relative;
        }

        .login-form .users-list {
            flex: 1 1 0;
        }

        .login-form .users-list .usr-row {
            display: flex;
            align-items: center;
            border: 1px solid #32404d;
            padding: 15px 14px;
            margin-bottom: 21px;
            border-radius: 10px;
            position: relative;
        }

        .login-form .users-list .usr-row:hover {
            background-color: #0a2339;
        }

        .login-form .users-list .usr-row .check:checked+.bdr {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            border: 3px solid #7fe50b;
            width: 100%;
            height: 100%;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, .2);
        }

        .login-form .users-list .usr-row .usr-name {
            flex: 1 1 0;
            padding-left: 24px;
            font-weight: bold;
        }

        .login-form .users-list .usr-row .usr-type {
            flex: 1 1 0;
        }

        .login-form .usr-action {
            position: sticky;
            top: 8px;
            width: 350px;
            margin-left: 40px;
        }

        .login-form .usr-action .pass-inp {
            margin-bottom: 17px;
        }

        .login-form .usr-action .pass-inp input {
            background-color: rgba(0, 0, 0, 0);
            color: #fff;
            border: 1px solid #6e7f8f;
            border-radius: 5px;
            padding: 12px 16px;
            width: 100%;
        }

        .login-form .usr-action button {
            background-color: #40505f;
            color: #fff;
            border: 0;
            font-size: 18px;
            padding: 13px 28px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <form action="" method="post" target="_blank" class="login-form">
        <div class="users-list">
            <?php
            $users = $pixdb->get(
                'members',
                array(
                    '#SRT' => 'firstName asc, middleName asc, lastName asc'
                ),
                'id, firstName, middleName, lastName, role'
            );
            foreach ($users->data as $usr) {
            ?>
                <label class="usr-row">
                    <input type="radio" class="check" name="uid" value="<?php echo $usr->id; ?>">
                    <span class="bdr"></span>
                    <span class="usr-name">
                        <?php echo "$usr->firstName $usr->middleName $usr->lastName"; ?>
                    </span>
                    <span class="usr-type">
                        <?php echo $usr->id; ?>
                    </span>
                    <span class="usr-type">
                        <?php echo $usr->role; ?>
                    </span>
                </label>
            <?php
            }
            ?>
        </div>
        <div class="usr-action">
            <?php
            if (!ISLOCAL) {
            ?>
                <div class="pass-inp">
                    <input type="password" name="pass" placeholder="Enter password">
                </div>
            <?php
            }
            ?>
            <button type="submit">
                Login
            </button>
        </div>
    </form>

</body>

</html>