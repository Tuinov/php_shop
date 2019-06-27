<?php
include "../config/config.php";
session_start();

var_dump(session_id());
die;

// setcookie('login', 'admin', time() + 3600);

// echo $_COOKIE['login'];

$key = 123;
$currect = $_GET['pass'];
// $currect = $_SESSION['pass'];
$allow = false;


if ($key == $currect) {
    $allow = true;
    $_SESSION['pass'] = $currect;

    if (isset($_GET['save'])) {
        setcookie('pass', '123', time() + 3600);
    }
    
} else {
    if ($_SESSION['pass'] == $key || $_COOKIE['pass'] == $key) {
        $allow = true;
    }
}

function is_auth () {
    if (isset($_COOKIE["hash"])){
        $hash = $_COOKIE["hash"];
        $db = get_db();
        $sql = "UPDATE `users` SET `hash` = '{$hash}'";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_assoc($result);
        $user = $row['login'];
        if (!empty($user)) {
            $_SESSION['login'] = $user;
        }
    }
    return isset($_SESSION['login']) ? true : false;
}

function get_user() {
   return is_auth() ? $_SESSION['login'] : false;
}

if (isset($_GET['send'])) {
    $login = $_GET['login'];
    $pass = $_GET['pass'];
}

if (isset($_GET['logout'])) {
    setcookie('hash');
    session_destroy();
    header("Location: /cookie.php");
}

if (!$allow) echo "
<form>
	<input type='text' name='login' placeholder='Логин'>
	<input type='password' name='pass' placeholder='Пароль'>
	Save? <input type='checkbox' name='save'>
	<input type='submit' name='send'>
</form>
";
else echo "Добро пожаловать, {$user} <a href='?logout'>выход</a>";

// $_SESSION['login'] = 'admin';





