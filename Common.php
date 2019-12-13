<?php
try {
    $db = new PDO('mysql:dbname=conveni_db;host=127.0.0.1; charset=utf8', 'daikichi', 'daiki');
} catch (PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());
    exit();
}

//正規のログインせず、他画面に遷移すのを防ぐ
if ($_POST['loginpage'] !== 1) {
    if (empty($_SESSION['login'])) {
        header('Location: login.php');
        exit();
    }
}
