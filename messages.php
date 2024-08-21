<?php

if (@$_SESSION['user']['login'] != '') {
    $user_1 = $_SESSION['user']['id'];
    $user_2 = $_SESSION['user']['to'];

    $name_1 = $_SESSION['user']['name'];
    foreach ($_SESSION['users'] as $k => $v) {
        if ($v['id'] == $user_2) {
            $name_2 = $v['name'];
        }
    }

    //  выбор сообщений между выбранными пользователями
    $msg_list = [$user_1, $user_2, $user_2, $user_1];
    $msg = $pdo->prepare("SELECT * FROM msg WHERE (id_send_u=? AND id_recip_u=?) OR (id_send_u=? AND id_recip_u=?) ORDER BY msg_date");
    $msg->execute($msg_list);
    $row = $msg->fetchAll();

    echo '</br>';
    echo "
<form action='actions.php' method='POST'>
    <table style='width: 30%; border-collapse: collapse'>
        <thead>
            <tr>
                <th style='width: 5%; background-color: rgba(255,200,255,0.4)'><input type='checkbox' disabled title='Отметить все как прочитанные' /></th>
                <th style='background-color: rgba(255,200,255,0.4)' align='left'>$name_2</th>
                <th style='background-color: rgba(200,200,255,0.4)' align='right'>$name_1</th>
                <th style='width: 5%; background-color: rgba(200,200,255,0.4)'><input type='checkbox' disabled /></th>
            </tr>
        </thead>
        </tbody>
        ";

    foreach ($row as $r) {
        $check = ($r['msg_read'] == 1) ? "checked" : "";   //проверка на прочтение и для установки флажка

        if ($r['id_send_u'] == $user_1) {
            echo '
            <tr style="background-color: rgba(200,200,255,0.4)">
                <td align="right" colspan="3">' . $r['msg_date'] . '</br>' . $r['msg_txt'] . '</td>
                <td align="right"><input type="checkbox" disabled ' . $check . ' title="Прочитано получателем" /></td>
            </tr>
            ';
        } else {
            echo '
            <tr style="background-color: rgba(255,200,255,0.4)">
                <td><input type="checkbox"' . $check . ' title="Прочитать" name="checked[' . $r['id_msg'] . ']" value="1"/></td>
                <td align="left" colspan="3">' . $r['msg_date'] . '</br>' . $r['msg_txt'] . '</td>
            </tr>
            ';
        }
    }

    echo '
        <tfoot>
            <tr>
                <td colspan="4" align="right">
                    <textarea name="msg" style="width: 100%"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <input type="submit" name="read" value="Отметить прочтение" />
                </td>
                <td colspan="2" align="right">
                    <input type="submit" name="send" value="Отправить сообщение" />
                </td>
            </tr>
        </tfoot>
    </table>
</form>
    ';

}

?>