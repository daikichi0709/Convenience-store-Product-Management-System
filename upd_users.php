<?php
session_start();
// DB接続
require('Common.php');

$keyid = $_SESSION['login']['user_id'];
$login_userdata = $db->prepare('SELECT auth FROM m_users WHERE user_id=?');
$login_userdata->execute(array($keyid));
$auth = $login_userdata->fetch();

if ($auth['auth'] !== "1") {
    header('Location: error.php');
    exit();
}


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
//権限制御(変更できる権限のユーザーをオンにする)
$authcontrol = "";
if ($user['user_id'] === $_SESSION['login']['user_id'] || $_POST['userid'] === $_SESSION['login']['user_id']) {
    $authcontrol = "X";
}

// 権限マスタより権限情報を取得
$auths = $db->query('SELECT auth, auth_name FROM m_auth');

$errormessage  = '';
$errorauth  = '';
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

    // 権限 //
    if (!empty($upduser['auth'])) {
        //選択権限の存在チェック
        $spval = $db->prepare('SELECT auth FROM m_auth WHERE auth=?');
        $spval->execute(array($upduser['auth']));
        $authid = $spval->fetch();

        if ($authcontrol === "X") { //自分を選択（対象＝管理者）
            if ($authid['auth'] !== $user['auth']) {
                $errorauth = "あなたは有効な権限では、ありません<br>";
            }
        } else { //自分以外を選択
            $authcnt = $db->prepare('SELECT COUNT(auth) AS cut FROM m_auth WHERE auth=?');
            $authcnt->execute(array($upduser['auth']));
            $acnt = $authcnt->fetch();

            //権限選択が正しければ「真」
            if ($acnt['cut'] !== "1") {
                $errorauth = "権限が設定外です<br>";
            }
        }
    } else {
        $errormessage .= "権限が未設定です<br>";
    }

    // ユーザー編集処理
    if (empty($errormessage) && empty($errorauth)) {
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
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <div style="font-size: 24px">
        <h1>ユーザー編集</h1>
        <!-- エラーメッセージ -->
        <?php if (!empty($errormessage) || !empty($errorauth)) : ?>
            <p><?php echo $errormessage; ?></p>
            <p><?php echo $errorauth; ?></p>
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
                            <strong>ユーザー名　　　　　　</strong>
                            <input type="text" placeholder="ユーザー名を入力してください" name="user_name" maxlength="255" style="font-size: 18px; width: 500px;" value="<?php if (empty($_POST['user_name'])) {
                                                                                                                                                                print(htmlspecialchars($user['user_name'], ENT_QUOTES));
                                                                                                                                                            } else {
                                                                                                                                                                print(htmlspecialchars($_POST['user_name'], ENT_QUOTES));
                                                                                                                                                            }
                                                                                                                                                            ?>">

                            <br><br>
                            <!-- メールアドレス -->
                            <strong>メールアドレス　　　　</strong>
                            <input type="text" placeholder="メールアドレスを入力してください" name="email" maxlength="255" style="font-size: 18px; width: 500px;" value="<?php if (empty($_POST['email'])) {
                                                                                                                                                                print(htmlspecialchars($user['email'], ENT_QUOTES));
                                                                                                                                                            } else {
                                                                                                                                                                print(htmlspecialchars($_POST['email'], ENT_QUOTES));
                                                                                                                                                            }
                                                                                                                                                            ?>">

                            <br><br>
                            <!-- パスワード -->
                            <strong>パスワード　　　　　　</strong>
                            <input type="text" placeholder="パスワードを設定してください" name="password" maxlength="255" style="font-size: 18px; width: 500px;">

                            <br><br>
                            <!-- パスワード（確認） -->
                            <strong>パスワード（確認）　　</strong>
                            <input type="text" placeholder="確認のため設定したパスワードを入力してください" name="protpassword" maxlength="255" style="font-size: 18px; width: 500px;">

                            <br><br>
                            <?php if ($authcontrol !== "X") : ?>
                                <!-- 権限 -->
                                <strong>権限　　　　　　　　　</strong>
                                <select name="auth" style="font-size: 18px; width: 500px;">
                                    <option value="0">選択</option>

                                    <!-- 権限マスタ内の権限をすべて表示 -->
                                    <?php while ($selauth = $auths->fetch()) : ?>

                                        <option value="<?php print(htmlspecialchars($selauth['auth'], ENT_QUOTES)); ?>"><?php print(htmlspecialchars($selauth['auth_name'], ENT_QUOTES)); ?></option>

                                    <?php endwhile; ?>
                                </select>
                            <?php else : ?>
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
        <p class="backtab">≪ 戻る</p>
    </a>
</body>

</html>
