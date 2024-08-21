<?php
include_once 'inc/header.inc.php';


if (isset($_GET['res_mes']))
    echo '<div class="res_mes">' . $_GET['res_mes'] . '</div>';

if (@$_SESSION['user']['login'] == '') {
    echo '
<form action="actions.php" method="POST">
    login: <input type="text" name="login" /><br />
    password: <input type="password" name="userpass" /><br />
    <input type="submit" name="auth" value="Login" />
</form>
';
} else {
    echo $_SESSION['user']['name'] . ' Переписка с пользователем
<form action="actions.php" method="POST">
    <select name="users">';

    foreach ($_SESSION['users'] as $u) {
        $selected = (@$_SESSION['user']['to'] == $u['id'])? "selected":"";
        echo '
        <option value ="' . $u['id'] . '"' . $selected . '>' . $u['name'] . '</option>';
    }


    echo '
    </select>
    <input type="submit" name="view" value="Показать"></>
</form>

<form action="actions.php" method="GET">
    <button name="logout" value="1">Logout</button>
</form>
	';
}

if (isset($_SESSION['user']['to'])) {
    include_once 'messages.php';
}

?>

