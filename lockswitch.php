<?php
session_start();
// DB接続
require('Common.php');

// JSONにする
header('Content-Type：application/json; charset=UTF-8');

//仮にPOSTの値が何もなければ不正リクエストとして返す
if (empty($_POST)) {
    $response[] = ['error' => '不正リクエストです', 'status' => 405];
    echo  json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}

$id = $_POST['user_id'];

$today = new DateTime();
$locktime = $today->format('Y-m-d H:i:s');


$userdata = $db->prepare('SELECT lock_flg FROM m_users WHERE user_id=?');
$userdata->execute(array($id));
$lockflg = $userdata->fetch();

if ($lockflg['lock_flg'] === '0') {
    $users = $db->prepare('UPDATE m_users SET lock_flg=9, lock_date=?, upd_date=?, upd_user_id=? WHERE user_id=?');
    $users->execute(array($locktime, $locktime, $_SESSION['login']['user_id'], $id));
} else {
    $users = $db->prepare('UPDATE m_users SET lock_flg=0, lock_date=null, upd_date=?, upd_user_id=? WHERE user_id=?');
    $users->execute(array($locktime, $_SESSION['login']['user_id'], $id));
}

$response  = [];

// もらった値をそのままかえす
$response[] = ['id' => $_POST['user_id']];
echo  json_encode($response, JSON_UNESCAPED_SLASHES  | JSON_UNESCAPED_UNICODE);
exit();
