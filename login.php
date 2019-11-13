<?php
session_start();
// DB接続
try {
    $db = new PDO('mysql:dbname=conveni_db;host=127.0.0.1; charset=utf8', 'root', '');
} catch (PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());
    exit();
}

if ($_COOKIE['email'] !== '') {
    $email = $_COOKIE['email'];
}

if (!empty($_POST)) {
    //ログイン失敗したら（一度チェックを通過するから）
    $error['login'] = '* ログインに失敗しました';

    $email = $_POST['email'];
    // ＊メアドとパスワードが共に入力されているか
    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        $login = $db->prepare('SELECT * FROM m_users WHERE email=?');
        $login->execute(array($_POST['email']));
        $member = $login->fetch();

        // ＊保存パスワード　＝　入力パスワード かつ　ロック中ではない
        if ($member['password'] === sha1($_POST['password']) && $member['lock_flg'] === '0') {
            //ログイン成功：失敗項目の初期化
            $login = $db->prepare('UPDATE m_users SET login_fail_date= null, login_fail_cut= 0, lock_date= null, last_login_date=?, lock_flg= 0 WHERE user_id=?');
            $login->execute(array(time(),$member['user_id']));

            // SESSIONにユーザーID保持
            $_SESSION['login']['user_id'] = $member['user_id'];
            $_SESSION['login']['time'] = time();
            $_SESSION['login']['user_name'] = $member['user_name'];
            $_SESSION['login']['auth'] = $member['auth'];
            // 初期化
            $error['login'] = '';
            header('Location: menu.php');
            exit();

        } elseif($member['lock_flg'] === '1' || $member['lock_flg'] === '9') {
            // ＊ロック中
            $error['login'] = '* ロック中です。';

        } else {
            // ＊パスワードが一致しない
            $logmiss = $member['login_fail_cut'] + 1;
            if($logmiss === 3){
                // ３回ログインミスした
                $penalty = $db->prepare('UPDATE m_users SET lock_flg=?, login_fail_cut=?, login_fail_date=? WHERE email=?');
                $penalty->execute(array(1,$logmiss,time(),$_POST['email']));
                $error['login'] = '* ログインに規定回数以上失敗したため、ロックしました。時間をおいて再ログインしてください。';
            } else {
                // ＊3回未満のログインミス
                $penalty = $db->prepare('UPDATE m_users SET login_fail_cut=?, login_fail_date=? WHERE email=?');
                $penalty->execute(array($logmiss,time(),$_POST['email']));
            }
        }

    } else {
        // メールアドレスとパスワードの片方でも未入力
        if ($_POST['email'] === '') {
            // エラーメアド
            $error['message'] .= '<br> メールアドレスが未入力です。';
        }
        if ($_POST['password'] === '') {
            // エラーパスワード
            $error['message'] .= '<br> パスワードが未入力です。';
        }
    }
}
unset($_SESSION['login']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【ログイン画面】</title>
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
        <h1>商品管理システム</h1>
        </a>

        <h3 style="color: red;">
        <!-- ログイン失敗 -->
        <?php
        if ($error['login'] !== ''):
            print($error['login']);
            if (!empty($error['message'])):
                print($error['message']);
            endif; ?>
        <?php endif; ?>
        </h3>

    <form method="post" style="margin-top: 10%;">
        <div>
            <!-- メールアドレス入力 -->
            <strong>メールアドレス　　　</strong>
            <input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($email, ENT_QUOTES)); ?>" />
            <br><br>
            <!-- パスワード入力 -->
            <strong>パスワード　　　　　</strong>
            <input type="password" name="password" size="35" maxlength="16" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
        </div>

        <br><br>

        <div>
            <!-- ログインボタン -->
            <input type="submit" value="ログイン" style="width: 200px;">
        </div>
    </form>
    </div>

    <!-- 確認用（新規画面＆パスワードの暗号化コード完成まで付加） -->
    <form action="menu.php">
        <div>
            <input type="submit" value="menu" style="margin-left: 70%; margin-top: 10%; float:left; width: 300px;">
        </div>
    </form>

</body>
</html>
