<?php
session_start();
// DB接続
require('Common.php');


if (!empty($_POST)) {
    $_SESSION['item'] = $_POST;
    $statement = $db->prepare('INSERT INTO t_inventories SET item_name=?, item_desc=?, item_comp=?, country=?, price=?, w_price=?, stock=?, day=?, del_flg=0, ins_date=?, ins_user_id=?, upd_date=?, upd_user_id=?');

    //日時設定
    $today = new DateTime();
    $instime = $today->format('Y-m-d H:i:s');

    $statement->execute($params = array($_SESSION['item']['item_name'], $_SESSION['item']['item_desc'], $_SESSION['item']['item_comp'],  $_SESSION['item']['country'],  $_SESSION['item']['price'], $_SESSION['item']['w_price'], $_SESSION['item']['stock'], $_SESSION['item']['day'], $instime,  $_SESSION['login']['user_id'], $instime,  $_SESSION['login']['user_id']));

    unset($_SESSION['item']);
    if (empty($_SESSION['item'])) {
        header('Location: menu.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【商品登録画面】</title>
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
        <h1>商品登録</h1>
        <hr>
        <!-- エラーメッセージ -->
        <h3>
            <?php if ($error[''] === '') : ?>
                <p>* </p>
            <?php endif; ?>
        </h3>

        <form action="" method="post">
            <div style="font-size: 24px">
                <!-- 商品名 -->
                <strong style="width: 200px;">商品名　　　　　</strong>
                <textarea placeholder="商品名を入力して下さい" name="item_name" rows="1" cols="100" maxlength="255"><?php print(htmlspecialchars($_SESSION['item']['item_name'], ENT_QUOTES)); ?></textarea>

                <br><br>
                <!-- 商品説明 -->
                <strong style="width: 200px;">商品説明　　　　</strong>
                <textarea placeholder="商品説明を入力して下さい" name="item_desc" rows="1" cols="100" maxlength="255"><?php print(htmlspecialchars($_SESSION['item']['item_desc'], ENT_QUOTES)); ?></textarea>

                <br><br>
                <!-- 仕入先 -->
                <strong style="width: 200px;">仕入先　　　　　</strong>
                <textarea placeholder="仕入先を入力して下さい" name="item_comp" rows="1" cols="100" maxlength="255"><?php print(htmlspecialchars($_SESSION['item']['item_comp'], ENT_QUOTES)); ?></textarea>

                <br><br>
                <!-- 生産国 -->
                <strong style="width: 200px;">生産国　　　　　</strong>
                <textarea placeholder="生産国を入力して下さい" name="country" rows="1" cols="100" maxlength="255"><?php print(htmlspecialchars($_SESSION['item']['country'], ENT_QUOTES)); ?></textarea>

                <br><br>
                <!-- 価格 -->
                <strong style="width: 200px;">価格　　　　　　</strong>
                <textarea placeholder="価格を入力して下さい" name="price" rows="1" cols="100" maxlength="255"><?php print(htmlspecialchars($_SESSION['item']['price'], ENT_QUOTES)); ?></textarea>

                <br><br>
                <!-- 仕入れ価格 -->
                <strong style="width: 200px;">仕入れ価格　　　</strong>
                <textarea placeholder="仕入れ価格を入力して下さい" name="w_price" rows="1" cols="100" maxlength="255"><?php print(htmlspecialchars($_SESSION['item']['w_price'], ENT_QUOTES)); ?></textarea>

                <br><br>
                <!-- 在庫数 -->
                <strong style="width: 200px;">在庫数　　　　　</strong>
                <textarea placeholder="在庫数を入力して下さい" name="stock" rows="1" cols="100" maxlength="255"><?php print(htmlspecialchars($_SESSION['item']['stock'], ENT_QUOTES)); ?></textarea>

                <br><br>
                <!-- 在庫数 -->
                <strong style="width: 200px;">入荷日　　　　　</strong>
                <textarea placeholder="入荷日を入力して下さい" name="day"" rows="1" cols="100" maxlength="255"><?php print(htmlspecialchars($_SESSION['item']['day'], ENT_QUOTES)); ?></textarea>

            </div>

            <br><br>

            <div>
                <!-- 登録ボタン -->
                <input type="submit" value="登録" style="font-size: 30px; width: 150px; height: 50px;">
            </div>
        </form>
    </div>
    <a href="menu.php">
        <p style="margin-left: 20%; text-align: left;">≪ 戻る
    </a>
</body>

</html>
