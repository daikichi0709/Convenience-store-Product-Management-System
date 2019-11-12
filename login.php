<?php
session_start();
// DB接続
try {
    $db = new PDO('mysql:dbname=conveni_db;host=127.0.0.1; charset=utf8', 'root', '');
} catch (PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());
}

if ($_COOKIE['email'] !== '') {
    $email = $_COOKIE['email'];
}

if (!empty($_POST)) {
    // パスワード入力あり
    $email = $_POST['email'];
    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        $login = $db->prepare('SELECT * FROM m_users WHERE email=? AND password=?');
        $login->execute(array($_POST['email'],$_POST['password']));

        $member = $login->fetch();

        if ($member['lock_flg'] === '0') {
            if ($member) {
                //ログイン成功
                // ・ログイン失敗日時：null
                // ・ログイン失敗回数：0
                // ・ロック日時：null
                // ・最終ログイン日時：現在日時
                // ・ロックフラグ：0
                // 上記５項目をupdateする
                $login = $db->prepare('UPDATE m_users SET login_fail_date= "", login_fail_cut= 0, lock_date= "", last_login_date=?, lock_flg= 0 WHERE user_id=?');
                $login->execute(array(date(Ymd),$member['user_id']));

                // SESSIONにユーザーID保持
                $_SESSION['user_id'] = $member['user_id'];
                $_SESSION['time'] = time();
                $_SESSION['user_name'] = $member['user_name'];

                header('Location: menu.php');
                exit();
            } else {
                //ログイン失敗
                $error['login'] = 'failed';
            }
        } elseif ($member['lock_flg'] === '1' || $member['lock_flg'] === '9') {
            //ロック中
            $error['login'] = 'block';
        } else {
            //ログイン失敗
            $error['login'] = 'failed';
        }
    } else {
        // メールアドレスとパスワードの片方でも未入力
        if ($_POST['email'] === '') {
            $message1['addr'] = 'noaddress';
        }
        if ($_POST['password'] === '') {
            $message2['pass'] = 'nopass';
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
        <a href="login.php">
        <h1>画面タイトル</h1>
        </a>
        <p></p>
        <hr>
            <!-- メールアドレス、パスワード -->
            <?php if ($message['addr'] !== 'noaddress' || $message['pass'] !== 'nopass') :?>
                    <h4 style="color: red;">
                <!-- ロック中　9：管理者側から　1：ログイン失敗 -->
                <?php if ($error['login'] === 'block'): ?>
                <p></p><p>* ロック中です。</p>
                <?php endif; ?>
                <!-- ログインに３回失敗 -->
                <?php if ($error['login'] === 'lock'): ?>
                <p>* ログインに規定回数以上失敗したため、ロックしました。時間をおいて再ログインしてください。</p>
                <?php endif; ?>
                <!-- メールアドレスが未入力 -->
                <?php if ($message1['addr'] === 'noaddress'): ?>
                <p>* メールアドレスが未入力です。</p>
                <?php endif; ?>
                <!-- パスワードが未入力です -->
                <?php if ($message2['pass'] === 'nopass'): ?>
                <p>* パスワードが未入力です。</p>
                <?php endif; ?>
                </h4>
        <?php endif; ?>

        <!-- ログイン失敗 -->
        <?php if ($error['login'] === 'failed'): ?>
         <p></p><p>* ログインに失敗しました。</p>
        <?php endif; ?>

    <form method="post">
        <div>
            <!-- メールアドレス入力 -->
            <strong style="width: 200px;">メールアドレス　　　</strong>
            <input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($email, ENT_QUOTES)); ?>" />
            <br><br>
            <!-- パスワード入力 -->
            <strong style="width: 200px;">パスワード　　　　　</strong>
            <input type="password" name="password" size="35" maxlength="16" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />

        </div>

        <br><br>

        <div>
            <!-- ログインボタン -->
            <input type="submit" value="ログイン" style="width: 200px;">
        </div>
    </form>
    </div>

</body>
</html>
