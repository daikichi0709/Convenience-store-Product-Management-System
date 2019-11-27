<?php
session_start();
// DB接続
require('Common.php');

// JSONにする
header('Content-Type：application/json; charset=UTF-8');

//仮にPOSTの値が何もなければ不正リクエストとして返金
if (empty($_POST)) {
    $response[] = ['error' => '不正リクエストです', 'status' => 405];
    echo  json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}

$id = $_POST['item_id'];

$today = new DateTime();
$deltime = $today->format('Y-m-d H:i:s');

$items = $db->prepare('UPDATE t_inventories SET del_flg=1, upd_date=?, upd_user_id=? WHERE item_id=?');
$items->execute(array($deltime, $_SESSION['login']['user_id'], $id));

$response  = [];

// もらった値をそのままかえす
$response[] = ['id' => $_POST['item_id']];
echo  json_encode($response, JSON_UNESCAPED_SLASHES  | JSON_UNESCAPED_UNICODE);
exit();
