<?php
session_start();
// DB接続
try {
    $db = new PDO('mysql:dbname=conveni_db;host=127.0.0.1; charset=utf8', 'daikichi', 'daiki');
} catch (PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());
    exit();
}
if(empty($_SESSION['login'])){
    header('Location: login.php');
    exit();
}
    $keyid = $_SESSION['login']['user_id'];
    $login2 = $db->prepare('SELECT user_name, auth FROM m_users WHERE user_id=?');
    $login2->execute(array($keyid));
    $member2 = $login2->fetch();


?>

<!DOCTYPE html>
<html>
<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【メニュー画面】</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        html,
        body {
            height: 80%;
            text-align: center;
            margin-top: 20px;
        }

        table {
            width: 80%;
            height: 40%;
        }
    </style>
</head>
<body>
    <div>
        <h1>商品管理システム</h1>
        <p style="font-size: 24px;">ログインユーザー名：<strong><?php print($member2['user_name']." auth= \n".$member2['auth']) ?></strong></p>
        <br>
        <div style="display:inline-flex">
            <!-- 在庫管理一覧へ画面遷移 -->
            <form action="stock.php">
                <div>
                <input type="submit" value="在庫管理" style="font-size: 30px; margin:50px; float:left; height: 100px; width: 300px; background-color:#ffff99;" onmouseover="this.style.background='#99ccff'" onmouseout="this.style.background='#ffff99';">
                </div>
            </form>

            <?php if($member2['auth'] === "1") :?>
            <!-- ユーザー管理一覧へ画面遷移 -->
            <form action="users.php">
                <div>
                <input type="submit" value="ユーザー管理" style="font-size: 30px; margin:50px; float:left; height: 100px; width: 300px; background-color:#ffff99;" onmouseover="this.style.background='#99ccff'" onmouseout="this.style.background='#ffff99';">
                </div>
            </form>
            <?php endif; ?>
        </div>
        <br><br>
        <form action="login.php">
            <div>
            <input type="submit" value="ログアウト" style="font-size: 20px; height: 80px;width: 200px;">
            </div>
        </form>
    </div>

</body>
</html>
