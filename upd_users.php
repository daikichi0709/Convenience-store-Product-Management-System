<?php
session_start();
// DB接続
require('Common.php');
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $id = $_GET['user_id'];
    $users = $db->prepare('SELECT user_id, user_name, email, password, auth FROM m_users WHERE user_id=?');
    $users->execute(array($id));
    $user = $users->fetch();

    if (empty($user)) {
        $_SESSION['result'] = 2; //ユーザー不在フラグ
        header('Location: users.php');
        exit();
    }
}
//権限制御
$authcontrol = "";
if ($user['user_id'] === $_SESSION['login']['user_id'] || $_POST['userid'] === $_SESSION['login']['user_id']) {
    $authcontrol = $user['auth'];
}

// 権限マスタより権限情報を取得
$auths = $db->query('SELECT auth, auth_name FROM m_auth');


$errormessage  = '';
$upduser = [];

if (!empty($_POST)) {
    $upduser = $_POST;
    // ユーザー名
    if (empty($upduser['user_name'])) {
        $errormessage .= "ユーザー名が未入力です" . "<br>";
    } else {
        if (mb_strlen($upduser['user_name']) > 20) {
            $errormessage .= "ユーザー名に入力できる文字数を超えています" . "<br>";
        }
    }

    // メールアドレス
    if (empty($upduser['email'])) {
        $errormessage .= "メールアドレスが未入力です" . "<br>";
    } else {
        if (mb_strlen($upduser['email']) > 255) {
            $errormessage .= "メールアドレスに入力できる文字数を超えています" . "<br>";
        }
    }

    // パスワード
    if (empty($upduser['password'])) {
        $errormessage .= "パスワードが未入力です" . "<br>";
    } else {
        if (mb_strlen($upduser['password']) < 6 || mb_strlen($upduser['password']) >= 16) {
            $errormessage .= "パスワードが6文字以上16文字未満にしてください" . "<br>";
        }
        if ($upduser['password'] !== $upduser['protpassword']) {
            $errormessage .= "パスワードが一致しません<br>";
        }
    }


    //ユーザーの一覧情報の取得
    $spval = $db->prepare('SELECT auth_name FROM m_auth WHERE auth=?');
    $spval->execute(array($upduser['auth']));
    $authname = $spval->fetch();

    // 権限
    if ($authcontrol === "1") {
        if (empty($spval)) {
            $errormessage .= "権限が未設定です<br>";
        } elseif ($authname['auth_name'] !== '管理者') {
            $errormessage .= "管理者権限ではありません<br>";
        }
    } else {
        if (empty($spval)) {
            $errormessage .= "権限が未設定です<br>";
        } elseif ($authname['auth_name'] !== '管理者' && $authname['auth_name'] !== '発注担当者' && $authname['auth_name'] !== '閲覧者') {
            $errormessage .= "権限が設定外です<br>";
        }
    }

    // ユーザー編集処理
    if (empty($errormessage)) {

        //パスワードの暗号化
        $hash_pass = password_hash($upduser['password'], PASSWORD_DEFAULT);

        //日時設定
        $today = new DateTime();
        $updtime = $today->format('Y-m-d H:i:s');

        $statement = $db->prepare('UPDATE m_users SET user_name=?, email=?, password=?, auth=?, upd_date=?, upd_user_id=? WHERE user_id=?');

        $statement->execute(array($upduser['user_name'], $upduser['email'], $hash_pass, $upduser['auth'],  $updtime,  $_SESSION['login']['user_id'], $upduser['userid']));

        $_SESSION['result'] = 1; //更新完了フラグ
        header('Location: users.php');
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
    <title>商品管理システム【ユーザー更新画面】</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        body {
            height: 100;
            text-align: center;
            margin-top: 3%;
            background-image: url("ラベンダー.jpg");
        }

        h1 {
            margin-left: 40%;
            width: 300px;
            background-color: #ffff52;
        }

        p {
            background-color: white;
            color: red;
            margin-left: 35%;
            margin-right: 35%;
            font-size: 20px;
        }

        h3 {
            color: red;
        }

        table {
            margin-left: 20%;
            background-color: whitesmoke;
            width: 60%;
            height: 50%;
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="font-size: 24px">
        <h1>ユーザー編集</h1>
        <hr>
        <!-- エラーメッセージ -->
        <?php if (!empty($errormessage)) : ?>
            <p><?php echo $errormessage; ?></p>
        <?php endif; ?>
        <table>
            <br>
            <tr>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="userid" value="<?php if (empty($_POST['userid'])) {
                                                                        print(htmlspecialchars($user['user_id'], ENT_QUOTES));
                                                                    } else {
                                                                        print(htmlspecialchars($_POST['userid'], ENT_QUOTES));
                                                                    }
                                                                    ?>">
                        <div style="font-size: 24px">
                            <!-- ユーザー名 -->
                            <strong style="width: 200px;">ユーザー名　　　　　　</strong>
                            <input type="text" placeholder="ユーザー名を入力してください" name="user_name" maxlength="255" style="font-size: 18px; width: 500px;" value="<?php if (empty($_POST['user_name'])) {
                                                                                                                                                                print(htmlspecialchars($user['user_name'], ENT_QUOTES));
                                                                                                                                                            } else {
                                                                                                                                                                print(htmlspecialchars($_POST['user_name'], ENT_QUOTES));
                                                                                                                                                            }
                                                                                                                                                            ?>">

                            <br><br>
                            <!-- メールアドレス -->
                            <strong style="width: 200px;">メールアドレス　　　　</strong>
                            <input type="text" placeholder="メールアドレスを入力してください" name="email" maxlength="255" style="font-size: 18px; width: 500px;" value="<?php if (empty($_POST['email'])) {
                                                                                                                                                                print(htmlspecialchars($user['email'], ENT_QUOTES));
                                                                                                                                                            } else {
                                                                                                                                                                print(htmlspecialchars($_POST['email'], ENT_QUOTES));
                                                                                                                                                            }
                                                                                                                                                            ?>">

                            <br><br>
                            <!-- パスワード -->
                            <strong style="width: 200px;">パスワード　　　　　　</strong>
                            <input type="text" placeholder="パスワードを設定してください" name="password" maxlength="255" style="font-size: 18px; width: 500px;">

                            <br><br>
                            <!-- パスワード（確認） -->
                            <strong style="width: 200px;">パスワード（確認）　　</strong>
                            <input type="text" placeholder="確認のため設定したパスワードを入力してください" name="protpassword" maxlength="255" style="font-size: 18px; width: 500px;">

                            <br><br>
                            <?php if ($authcontrol !== "1") : ?>
                                <!-- 権限 -->
                                <strong style="width: 200px;">権限　　　　　　　　　</strong>
                                <select name="auth" style="font-size: 18px; width: 500px;">
                                    <option value="">選択</option>

                                    <?php while ($selauth = $auths->fetch()) : ?>
                                        <option value="<?php print(htmlspecialchars($selauth['auth'], ENT_QUOTES)); ?>"><?php print(htmlspecialchars($selauth['auth_name'], ENT_QUOTES)); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php elseif ($authcontrol === "1") : ?>
                                <input type="hidden" name="auth" value=<?php print(htmlspecialchars($user['auth'], ENT_QUOTES)); ?>>
                            <?php endif; ?>
                        </div>

                        <br><br>

                        <div>
                            <!-- 登録ボタン -->
                            <input type="submit" value="更新" style="font-size: 30px; width: 150px; height: 50px;">
                        </div>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <a href="users.php">
        <p style="background-color:whitesmoke; margin-left: 20%; text-align: left; font-size: 18px; width: 75px;">≪ 戻る</p>
    </a>
</body>

</html>
