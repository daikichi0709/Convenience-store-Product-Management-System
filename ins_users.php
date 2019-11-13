<?php
session_start();
// DB接続
try {
    $db = new PDO('mysql:dbname=conveni_db;host=127.0.0.1; charset=utf8', 'root', '');
} catch (PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());
    exit();
}

if(!empty($_POST)){
    $_SESSION['join'] = $_POST;
    if($_SESSION['join']['password1'] === $_SESSION['join']['password2']) {
        $statement = $db->prepare('INSERT INTO m_users SET user_name=?, email=?, password=?, auth=?, ins_user_id=?, upd_user_id=?, ins_date=?, upd_date=?, last_login_date=?, login_fail_cut=0, del_flg=0, lock_flg=0');

            $statement->execute(array(
            $_SESSION['join']['user_name'],
            $_SESSION['join']['email'],
            sha1($_SESSION['join']['password1']),
            $_SESSION['join']['auth'],
            $_SESSION['login']['user_id'],
            $_SESSION['login']['user_id'],
            time(),
            time(),
            time()
        ));
        $kari = $_SESSION['join'];
        unset($_SESSION['join']);
        if(empty($_SESSION['join'])){
            header('Location: menu.php');
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
    <?php if ($error[''] === ''): ?>
    <p>* </p>
    <?php endif; ?>
    </h3>

    <form action="" method="post">
        <div>
            <!-- 商品名 -->
            <strong style="width: 200px;">ユーザー名　　　　　　</strong>
            <textarea placeholder="ユーザー名を入力してください" name="user_name"" rows="1" cols="100" maxlength="255" ><?php print(htmlspecialchars($_SESSION['join']['user_name'], ENT_QUOTES)); ?></textarea>

            <br><br>
            <!-- 商品説明 -->
            <strong style="width: 200px;">メールアドレス　　　　</strong>
            <textarea placeholder="メールアドレスを入力してください" name="email" rows="1" cols="100" maxlength="255" ><?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES)); ?></textarea>

            <br><br>
            <!-- 仕入先 -->
            <strong style="width: 200px;">パスワード　　　　　　</strong>
            <textarea placeholder="パスワードを設定してください" name="password1" rows="1" cols="100" maxlength="255" ><?php print(htmlspecialchars($_SESSION['join']['password1'], ENT_QUOTES)); ?></textarea>

            <br><br>
            <!-- 生産国 -->
            <strong style="width: 200px;">パスワード（確認）　　</strong>
            <textarea placeholder="確認のため設定したパスワードを入力してください" name="password2" rows="1" cols="100" maxlength="255" ><?php print(htmlspecialchars($_SESSION['join']['password2'], ENT_QUOTES)); ?></textarea>

            <br><br>
            <!-- 価格 -->
            <strong style="width: 200px;">権限　　　　　　　　　</strong>
            <textarea placeholder="権限を決めて下さい【1:管理者,2:発注担当者,3:閲覧者】" name="auth" rows="1" cols="100" maxlength="255" ><?php print(htmlspecialchars($_SESSION['join']['auth'], ENT_QUOTES)); ?></textarea>

        </div>
        <br><br>
        <div>
            <!-- 登録ボタン -->
            <input type="submit" value="登録" style="font-size: 30px; width: 150px; height: 50px;">
        </div>
    </form>
    </div>

    <a href="menu.php"><p style="margin-left: 20%; text-align: left;">≪ 戻る</a>
</body>
</html>
