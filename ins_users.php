<?php
session_start();
// DB接続
require('Common.php');

$errormessage  = '';
$join = [];

if (!empty($_POST)) {
    $join = $_POST;

    // ユーザー名
    if (empty($join['user_name'])) {
        $errormessage .= "ユーザー名が未入力です" . "<br>";
    } else {
        if (mb_strlen($join['user_name']) > 20) {
            $errormessage .= "ユーザー名に入力できる文字数を超えています" . "<br>";
        }
    }

    // メールアドレス
    if (empty($join['email'])) {
        $errormessage .= "メールアドレスが未入力です" . "<br>";
    } else {
        if (mb_strlen($join['email']) > 255) {
            $errormessage .= "メールアドレスに入力できる文字数を超えています" . "<br>";
        }
    }

    // パスワード
    if (empty($join['password'])) {
        $errormessage .= "パスワードが未入力です" . "<br>";
    } else {
        if (mb_strlen($join['password']) < 6 || mb_strlen($join['password']) >= 16) {
            $errormessage .= "パスワードが6文字以上16文字未満にしてください" . "<br>";
        }
    }

    // 権限
    if (empty($join['auth'])) {
        $errormessage .= "権限が未設定です<br>";
    }



    // ユーザー編集処理
    if (empty($errormessage)) {
        if ($join['password'] === $join['protpassword']) {
            $statement = $db->prepare('INSERT INTO m_users SET user_name=?, email=?, password=?, auth=?, ins_user_id=?, upd_user_id=?, ins_date=?, upd_date=?, last_login_date=?, login_fail_cut=0, del_flg=0, lock_flg=0');

            //パスワードの暗号化
            $hash_pass = password_hash($join['password'], PASSWORD_DEFAULT);
            //日時設定
            $today = new DateTime();
            $instime = $today->format('Y-m-d H:i:s');

            $statement->execute(array($join['user_name'], $join['email'], $hash_pass, $join['auth'], $_SESSION['login']['user_id'], $_SESSION['login']['user_id'], $instime, $instime, $instime));



            $_SESSION['login']['ok_code'] = 1; //更新完了フラグ
            header('Location: users.php');
            exit();
        }
        $errormessage .= "パスワードが一致しません<br>";
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
    <title>商品管理システム【ユーザー登録画面】</title>
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

        <hr>
        <?php echo $udeuser['user_id']; ?>
        <!-- エラーメッセージ -->
        <?php if (!empty($errormessage)) : ?>
            <p style="color: red; font-size: 20px;"><?php echo $errormessage; ?></p>
        <?php endif; ?>


        <form method="post">
            <div style="font-size: 24px">
                <!-- ユーザー名 -->
                <strong style="width: 200px;">ユーザー名　　　　　　</strong>
                <input type="text" placeholder="ユーザー名を入力してください" name="user_name" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (!empty($_POST)) {
                                                                                                                                                    print(htmlspecialchars($_POST['user_name'], ENT_QUOTES));
                                                                                                                                                }
                                                                                                                                                ?>">

                <br><br>
                <!-- メールアドレス -->
                <strong style="width: 200px;">メールアドレス　　　　</strong>
                <input type="text" placeholder="メールアドレスを入力してください" name="email" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (!empty($_POST)) {
                                                                                                                                                print(htmlspecialchars($_POST['email'], ENT_QUOTES));
                                                                                                                                            }
                                                                                                                                            ?>">

                <br><br>
                <!-- パスワード -->
                <strong style="width: 200px;">パスワード　　　　　　</strong>
                <input type="text" placeholder="パスワードを設定してください" name="password" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (!empty($_POST)) {
                                                                                                                                                    print(htmlspecialchars($_POST['password'], ENT_QUOTES));
                                                                                                                                                }
                                                                                                                                                ?>">

                <br><br>
                <!-- パスワード（確認） -->
                <strong style="width: 200px;">パスワード（確認）　　</strong>
                <input type="text" placeholder="確認のため設定したパスワードを入力してください" name="protpassword" maxlength="255" style="font-size: 18px; width: 500px">

                <br><br>
                <!-- 権限 -->
                <strong style="width: 200px;">権限　　　　　　　　　</strong>
                <input type="text" placeholder="権限を数値で決めて下さい【1:管理者,2:発注担当者,3:閲覧者】" name="auth" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (!empty($_POST)) {
                                                                                                                                                                print(htmlspecialchars($_POST['auth'], ENT_QUOTES));
                                                                                                                                                            }
                                                                                                                                                            ?>">

            </div>

            <br><br>
            <div>
                <!-- 登録ボタン -->
                <input type="submit" value="登録" style="font-size: 30px; width: 150px; height: 50px;">
            </div>
        </form>
    </div>

    <a href="users.php">
        <p style="margin-left: 20%; text-align: left;">≪ 戻る</p>
    </a>
</body>

</html>
