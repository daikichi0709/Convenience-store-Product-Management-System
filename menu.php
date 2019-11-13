<?php
session_start();
// DB接続
try {
    $db = new PDO('mysql:dbname=conveni_db;host=127.0.0.1; charset=utf8', 'root', '');
} catch (PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());
    exit();
}
if(empty($_SESSION['login'])){
    header('Location: login.php');
    exit();
}
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
            height: 100;
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
        <p>ログインユーザー名：<?php print($_SESSION['login']['user_name']) ?></p>
        <hr><br>
        <div style="display:inline-flex">
            <!-- 在庫管理一覧へ画面遷移 -->
            <form action="stock.php">
                <div>
                <input type="submit" value="在庫管理" style="font-size: 30px; margin:50px; float:left; height: 100px; width: 300px; background-color:#ffff99;" onmouseover="this.style.background='#99ccff'" onmouseout="this.style.background='#ffff99';">
                </div>
            </form>

            <?php if($_SESSION['login']['auth'] === "1") :?>
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
            <input type="submit" value="ログアウト" style="width: 200px;">
            </div>
        </form>
    </div>

</body>
</html>
