<?php
session_start();
// DB接続
require('Common.php');

if (isset($_REQUEST['user_id']) && is_numeric($_REQUEST['user_id'])) {
    $id = $_REQUEST['user_id'];

    $users = $db->prepare('SELECT user_name, email, password, auth FROM m_users WHERE user_id=?');
    $users->execute(array($id));
    $user = $users->fetch();

    if (empty($user)) {
        $_SESSION['login']['ok_code'] = 2; //ユーザー不在フラグ
        header('Location: users.php');
        exit();
    }
}

if (!empty($_POST)) {
    $_SESSION['user'] = $_POST;
    $errormessage  = '';

    // ユーザー名
    if (empty($_SESSION['user']['user_name'])) {
        $errormessage .= "ユーザー名が未入力です" . "<br>";
    } else {
        if (mb_strlen($_SESSION['user']['user_name']) > 20) {
            $errormessage .= "ユーザー名に入力できる文字数を超えています" . "<br>";
        }
    }

    // メールアドレス
    if (empty($_SESSION['user']['email'])) {
        $errormessage .= "メールアドレスが未入力です" . "<br>";
    } else {
        if (mb_strlen($_SESSION['user']['email']) > 255) {
            $errormessage .= "メールアドレスに入力できる文字数を超えています" . "<br>";
        }
    }

    // パスワード
    if (empty($_SESSION['user']['password'])) {
        $errormessage .= "パスワードが未入力です" . "<br>";
    } else {
        if (mb_strlen($_SESSION['user']['password']) < 6 || mb_strlen($_SESSION['user']['password']) >= 16) {
            $errormessage .= "パスワードが6文字以上16文字未満にしてください" . "<br>";
        }
    }

    // 権限
    if (empty($_SESSION['user']['auth'])) {
        $errormessage .= "権限が未設定です<br>";
    }



    // ユーザー編集処理
    if (empty($errormessage)) {
        if ($_SESSION['user']['password'] === $_POST['protpassword']) {
            $statement = $db->prepare('UPDATE m_users SET user_name=?, email=?, password=?, auth=?, upd_date=?, upd_user_id=? WHERE user_id=?');

            //パスワードの暗号化
            $hash_pass = password_hash($_SESSION['user']['password'], PASSWORD_DEFAULT);

            //日時設定
            $today = new DateTime();
            $instime = $today->format('Y-m-d H:i:s');

            $statement->execute(array($_SESSION['user']['user_name'], $_SESSION['user']['email'], $hash_pass,  $_SESSION['user']['auth'],  $instime,  $_SESSION['login']['user_id'], $id));

            unset($_SESSION['user']);
            if (empty($_SESSION['user'])) {
                $_SESSION['login']['ok_code'] = 1; //更新完了フラグ
                header('Location: users.php');
                exit();
            }
        }
        $errormessage .= "パスワードが一致しません<br>";
    }
} else {
    unset($_SESSION['user']);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【ユーザー更新画面】</title>
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
    <div style="font-size: 24px">
        <h1>ユーザー編集</h1>
        <hr>
        <?php echo $_SESSION['user']['user_id']; ?>
        <!-- エラーメッセージ -->
        <?php if (!empty($errormessage)) : ?>
            <p style="color: red; font-size: 20px;"><?php echo $errormessage; ?></p>
        <?php endif; ?>

        <form method="post">
            <div style="font-size: 24px">
                <!-- ユーザー名 -->
                <strong style="width: 200px;">ユーザー名　　　　　　</strong>
                <input type="text" placeholder="ユーザー名を入力してください" name="user_name" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['user'])) {
                                                                                                                                                    print(htmlspecialchars($user['user_name'], ENT_QUOTES));
                                                                                                                                                } else {
                                                                                                                                                    print(htmlspecialchars($_SESSION['user']['user_name'], ENT_QUOTES));
                                                                                                                                                }
                                                                                                                                                ?>">

                <br><br>
                <!-- メールアドレス -->
                <strong style="width: 200px;">メールアドレス　　　　</strong>
                <input type="text" placeholder="メールアドレスを入力してください" name="email" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['user'])) {
                                                                                                                                                print(htmlspecialchars($user['email'], ENT_QUOTES));
                                                                                                                                            } else {
                                                                                                                                                print(htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES));
                                                                                                                                            }
                                                                                                                                            ?>">

                <br><br>
                <!-- パスワード -->
                <strong style="width: 200px;">パスワード　　　　　　</strong>
                <input type="text" placeholder="パスワードを設定してください" name="password" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['user'])) {
                                                                                                                                                    print(htmlspecialchars($user['password'], ENT_QUOTES));
                                                                                                                                                } else {
                                                                                                                                                    print(htmlspecialchars($_SESSION['user']['password'], ENT_QUOTES));
                                                                                                                                                }
                                                                                                                                                ?>">

                <br><br>
                <!-- パスワード（確認） -->
                <strong style="width: 200px;">パスワード（確認）　　</strong>
                <input type="text" placeholder="確認のため設定したパスワードを入力してください" name="protpassword" maxlength="255" style="font-size: 18px; width: 500px">

                <br><br>
                <!-- 権限 -->
                <strong style="width: 200px;">権限　　　　　　　　　</strong>
                <input type="text" placeholder="権限を数値で決めて下さい【1:管理者,2:発注担当者,3:閲覧者】" name="auth" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['user'])) {
                                                                                                                                                                print(htmlspecialchars($user['auth'], ENT_QUOTES));
                                                                                                                                                            } else {
                                                                                                                                                                print(htmlspecialchars($_SESSION['user']['auth'], ENT_QUOTES));
                                                                                                                                                            }
                                                                                                                                                            ?>">

            </div>

            <br><br>

            <div>
                <!-- 登録ボタン -->
                <input type="submit" value="更新" style="font-size: 30px; width: 150px; height: 50px;">
            </div>
        </form>
    </div>
    <a href="users.php">
        <p style="margin-left: 20%; text-align: left;">≪ 戻る</p>
    </a>
</body>

</html>