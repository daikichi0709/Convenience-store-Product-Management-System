<?php
session_start();
// DB接続
require('Common.php');

?>

<!DOCTYPE html>
<html>

<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【エラー画面】</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        body {
            height: 100;
            text-align: center;
            margin-top: 3%;
            background-image: url("犬２.jpg");
        }

        h1 {
            margin-right: 20%;
            margin-left: 20%;
            background-color: whitesmoke;
        }


        table {
            background-color: whitesmoke;
            width: 80%;
            height: 40%;
        }
    </style>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/usersamp.js"></script>
</head>

<body>
    <!-- ユーザー管理一覧へ画面遷移 -->
    <h1 style="font-size: 32px; color: red;">エラーです。</h1>
    <form action="menu.php">
        <div style="display:inline-flex;">
            <input type="submit" value="メニュー画面へ移動" style="font-size: 30px; margin-top:100px; float:left; height: 200px; width: 500px; background-color:coral;" onmouseover="this.style.background='#99ccff'" onmouseout="this.style.background='#ff7f50';">
        </div>
    </form>
</body>

</html>
