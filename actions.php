<?php
include_once 'inc/header.inc.php';

// Authorization, Login
if (isset($_POST['auth'])) {
    $SQL = "SELECT * FROM users WHERE lgn='" . $_POST['login'] . "'";
    $tmp = $pdo->query($SQL)->fetch();

    if ($tmp['psw'] == md5($_POST['userpass'])) {
        $_SESSION['user']['id'] = $tmp['id'];
        $_SESSION['user']['login'] = $tmp['lgn'];
        $_SESSION['user']['name'] = $tmp['name'];

        // список пользователей для сообщений
        $SQL = "SELECT id, name FROM users WHERE id <> '" . $tmp['id'] . "'";
        $tmp = $pdo->query($SQL)->fetchall();
        foreach ($tmp as $t) {
            $_SESSION['users'][] = ["id" => $t['id'], "name" => $t['name']];
        }

        header("Location: index.php?res_mes=Authorized");
        exit;
    }
    header("Location: index.php?res_mes=User not found");
    exit;

}

// Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    unset($_SESSION['users']);

    header("Location: index.php?res_mes=Logged out");
    exit;
}

// Send message
if (isset($_POST['send'])) {

    $msg_send = [$_SESSION['user']['id'], $_SESSION['user']['to'], $_POST['msg'], date("Y-m-d H:i:s")]; // формат MySQL DATETIME
    $msg = $pdo->prepare("INSERT INTO msg VALUES (null,?,?,?,?,0)");

    $msg->execute($msg_send);

    header("Location: index.php");
    exit;
}

// Read message
if (isset($_POST['read'])) {
    $msg_read = $_POST['checked']; //передает только отмеченные
    $user = $_SESSION['user']['id'];
    $SQL = "SELECT id_msg, msg_read FROM msg WHERE id_recip_u = $user";
    $tmp = $pdo->query($SQL)->fetchAll();

    // получение отмеченных в виде id_msg - msg_read
    foreach ($tmp as $val) {
        $tmp_check[$val[0]] = $val[1];
    }

    $msg = $pdo->prepare("UPDATE msg SET msg_read = ? WHERE id_msg = ?");

    // отменить прочтение - получить список всех для получателя , сравнить массив, изменить только отличающиеся
    if (isset($msg_read)) {
        $result = array_intersect($tmp_check, $msg_read);
        // снять признак прочтения
        foreach ($result as $rk => $rv) {
            if ($rk != array_key_exists($rk, $msg_read)) {
                $msg_read[$rk] = 0;
            } else {
                $msg_read[$rk] = 1;
            }
        }
    } else {
        // все признаки сняты
        $msg_read = array_fill_keys(array_keys($tmp_check), 0);
    }

    // отметить/снять прочитанные
    foreach ($msg_read as $key => $val) {
        $check = [$val, $key];
        $msg->execute($check);
    }

    header("Location: index.php");
    exit;
}

if(isset($_POST['view'])) {
    echo ' hm';
    var_dump($_POST['users']);
    $_SESSION['user']['to'] = $_POST['users'];
    var_dump($_SESSION['user']['to']);
    //exit;
    header("Location: index.php");
    exit;
}

?>