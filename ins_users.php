<?php
session_start();
// DB接続
require('Common.php');

if (!empty($_POST)) {
    $_SESSION['join'] = $_POST;
    if ($_SESSION['join']['password1'] === $_SESSION['join']['password2']) {

        $statement = $db->prepare('INSERT INTO m_users SET user_name=?, email=?, password=?, auth=?, ins_user_id=?, upd_user_id=?, ins_date=?, upd_date=?, last_login_date=?, login_fail_cut=0, del_flg=0, lock_flg=0');

        //パスワードの暗号化
        $hash_pass = password_hash($_SESSION['join']['password1'], PASSWORD_DEFAULT);
        //日時設定
        $today = new DateTime();
        $instime = $today->format('Y-m-d H:i:s');

        $statement->execute(array($_SESSION['join']['user_name'], $_SESSION['join']['email'], $hash_pass, $_SESSION['join']['auth'], $_SESSION['login']['user_id'], $_SESSION['login']['user_id'], $instime, $instime, $instime));

        unset($_SESSION['join']);
        if (empty($_SESSION['join'])) {
            header('Location: users.php');
            exit();
        }
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
    <title>タイトルを入力してください</title>
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
        <h1>ユーザー登録</h1>

        <!-- エラーメッセージ -->
        <h3>
            <?php if ($error[''] === '') : ?>
                <p>* </p>
            <?php endif; ?>
        </h3>

        <form action="" method="post">
            <div style="font-size: 24px">
                <!-- 商品名 -->
                <strong style="width: 200px;">ユーザー名　　　　　　</strong>
                <input type="text" placeholder="ユーザー名を入力してください" name="user_name" style="font-size: 18px; width: 500px" maxlength="255"><?php print(htmlspecialchars($_SESSION['join']['user_name'], ENT_QUOTES)); ?>

                <br><br>
                <!-- 商品説明 -->
                <strong style="width: 200px;">メールアドレス　　　　</strong>
                <input type="text" placeholder="メールアドレスを入力してください" name="email" style="font-size: 18px; width: 500px" maxlength="255"><?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES)); ?>

                <br><br>
                <!-- 仕入先 -->
                <strong style="width: 200px;">パスワード　　　　　　</strong>
                <input type="text" placeholder="パスワードを設定してください" name="password1" style="font-size: 18px; width: 500px" maxlength="255"><?php print(htmlspecialchars($_SESSION['join']['password1'], ENT_QUOTES)); ?>

                <br><br>
                <!-- 生産国 -->
                <strong style="width: 200px;">パスワード（確認）　　</strong>
                <input type="text" placeholder="確認のため設定したパスワードを入力してください" name="password2" style="font-size: 18px; width: 500px" maxlength="255"><?php print(htmlspecialchars($_SESSION['join']['password2'], ENT_QUOTES)); ?>

                <br><br>
                <!-- 価格 -->
                <strong style="width: 200px;">権限　　　　　　　　　</strong>
                <input type="text" placeholder="権限を数値で決めて下さい【1:管理者,2:発注担当者,3:閲覧者】" name="auth" style="font-size: 18px; width: 500px" maxlength="255"><?php print(htmlspecialchars($_SESSION['join']['auth'], ENT_QUOTES)); ?>

            </div>
            <br><br>
            <div>
                <!-- 登録ボタン -->
                <input type="submit" value="登録" style="font-size: 30px; width: 150px; height: 50px;">
            </div>
        </form>
    </div>

    <a href="users.php">
        <p style="margin-left: 20%; text-align: left;">≪ 戻る
    </a>
</body>

</html>
