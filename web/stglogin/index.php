<?php
include '../evgadmin/lib/lib.php';

if (isset($_POST['password'])) {
    $password = esc($_POST['password']);
    if ($password == 'Ev3rGrnD5!') {
        setcookie('stagingaccess', 'true', time() + 86400, '/');
        $pix->redirect($pix->domain);
    }

    echo 'Access denied';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-form input[type="password"] {
            margin-bottom: 10px;
            padding: 10px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-form input[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }

        .login-form input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <form class="login-form" action="" method="post">
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="submit" value="Login">
    </form>
</body>

</html>