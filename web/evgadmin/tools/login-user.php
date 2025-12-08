<?php
include '../lib/lib.php';

$_ = $_POST;
$page  = max(0, intval($_GET['pgn'] ?? 0));
$searchKey = trim($_GET['search'] ?? '');

if ($searchKey !== '') {
    $safe = q('%' . addslashes($searchKey) . '%');

    $searchWhere = "(
        firstName LIKE $safe OR
        middleName LIKE $safe OR
        lastName LIKE $safe OR
        role LIKE $safe OR
        CAST(id AS CHAR) LIKE $safe
    )";
}



$args = [
    '#SRT'    => 'firstName asc, middleName asc, lastName asc',
    '__limit' => 100,
    '__page'  => $page
];

if (!empty($searchWhere)) {
    $args['__QUERY__'] = $searchWhere;
}


$users = $pixdb->get(
    'members',
    $args,
    'id, firstName, middleName, lastName, role'
);



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

        .pagination {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 25px 0;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            height: 40px;
            padding: 0 20px;
            border-radius: 8px;
            border: 1px solid #32404d;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1em;
            font-weight: 500;
            color: #fff;
            background: #0a2339;
        }

        .pagination a:hover {
            background: #417be6;
            color: #fff;
            border-color: #417be6;
        }

        .pagination .active,
        .pagination span.active {
            background: #417be6;
            color: #fff;
            border-color: #417be6;
            cursor: default;
        }

        .pagination .disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .search-bar-wrap {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #020f1a;
            padding: 15px 0;
            margin-bottom: 20px;
        }

        .search-bar-wrap input {
            width: 320px;
            padding: 12px 16px 12px 40px;
            border-radius: 10px;
            border: 1px solid #32404d;
            background: #0a2339;
            color: #fff;
            font-size: 14px;
        }

        .search-bar-wrap input:focus {
            outline: none;
            border-color: #417be6;
            box-shadow: 0 0 0 3px rgba(65, 123, 230, .15);
        }
    </style>
</head>

<body>
    <div class="search-bar-wrap">
        <form method="get" action="">
            <input type="text"
                id="memberSearch"
                name="search"
                placeholder="Search by name or role..."
                value="<?php $_GET['search'] ?? '' ?>">
            <input type="hidden" name="pgn" value="0">
        </form>
    </div>

    <form action="" method="post" target="_blank" class="login-form">
        <div class="users-list">
            <?php
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
            if (!$users->data) {
                echo "No user Found";
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
    <div style="margin-top:30px;">
        <?php
        echo $pix->pagination(
            $users->pages,
            $page,
            5,
            null,
            '',
            0,
            'pgn',
            null
        );
        ?>
    </div>


</body>

</html>