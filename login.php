<?php
$_POST['loginpage'] = 1;
session_start();
// DB接続
require('Common.php');

if (!empty($_POST['email']) || !empty($_POST['password'])) {
    //ログイン失敗したら（一度チェックを通過するから）
    $error['login'] = 'ログインに失敗しました';

    $email = $_POST['email'];
    // *メアドとパスワードが共に入力されているか
    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        $login = $db->prepare('SELECT user_id, email, password, login_fail_date, lock_flg, login_fail_cut FROM m_users WHERE email=?');
        $login->execute(array($_POST['email']));
        $member = $login->fetch();

        // ログインしようとした時の日時
        $today = new DateTime();
        $datetime = $today->format('Y-m-d H:i:s');


        if (!empty($member['login_fail_date'])) {
            // 前回のログインミスの日時
            $pastday = $member['login_fail_date'];
            $pasttime = new DateTime($pastday);
            // 差分計算
            $interval['year'] = $pasttime->diff($today)->format("%y");    //年
            $interval['month'] = $pasttime->diff($today)->format("%m");   //月
            $interval['day'] = $pasttime->diff($today)->format("%d");     //日
            $interval['hour'] = $pasttime->diff($today)->format("%h");    //時
            $interval['minute'] = $pasttime->diff($today)->format("%i");  //分
            // 「年,月,日,時」⇒「分」
            $difference = ((($interval['year'] * 12 + $interval['month']) * 30 + $interval['day']) * 24 + $interval['hour']) * 60 + $interval['minute'];

            // １０分以上経過 ＆ 管理者からのロックではない時、ならばロックカウント、ロックフラグの初期化
            if ($difference >= "10" and $member['lock_flg'] !== "9") {
                $login = $db->prepare('UPDATE m_users SET login_fail_date= null, login_fail_cut= 0, lock_date= null, lock_flg= 0, upd_date=?, upd_user_id=? WHERE user_id=?');
                $login->execute(array($datetime, $member['user_id'], $member['user_id']));
            }
        }


        // *保存パスワード ＝ 入力パスワード かつ ロック中ではない時
        if (password_verify($_POST['password'], $member['password']) && $member['lock_flg'] === '0') {
            //ログイン成功：失敗項目の初期化
            $login = $db->prepare('UPDATE m_users SET login_fail_date= null, login_fail_cut= 0, lock_date= null, last_login_date=?, lock_flg= 0, upd_date=?,  upd_user_id=? WHERE user_id=?');
            $login->execute(array($datetime, $datetime, $member['user_id'], $member['user_id']));

            // SESSIONにユーザーID保持
            $_SESSION['login']['user_id'] = $member['user_id'];

            // エラー内容の初期化
            $error['login'] = '';
            header('Location: menu.php');
            exit();
        } elseif ($member['lock_flg'] === '1' || $member['lock_flg'] === '9') {
            // *ロック中
            $error['login'] = 'ロック中です';
        } else {
            // *パスワードが一致しない時
            $logmiss = $member['login_fail_cut'] + 1;

            if ($logmiss === 3) {
                // *３回ログインミスした時
                $penalty = $db->prepare('UPDATE m_users SET lock_date= ?, lock_flg=?, login_fail_cut=?, login_fail_date=?, upd_date=?, upd_user_id=? WHERE email=?');
                $penalty->execute(array($datetime, 1, $logmiss, $datetime, $datetime, $member['user_id'], $_POST['email']));
                $error['login'] = 'ログインに規定回数以上失敗したため、ロックしました。時間をおいて再ログインしてください';
            } else {
                // *３回未満のログインミス
                $penalty = $db->prepare('UPDATE m_users SET login_fail_cut=?, login_fail_date=?, upd_date=?, upd_user_id=? WHERE email=?');
                $penalty->execute(array($logmiss, $datetime, $datetime, $member['user_id'], $_POST['email']));
            }
        }
    } else {
        // メールアドレスとパスワードの片方でも未入力
        if ($_POST['email'] === '') {
            // エラーメアド
            $error['message'] .= '<br> メールアドレスが未入力です';
        }
        if ($_POST['password'] === '') {
            // エラーパスワード
            $error['message'] .= '<br> パスワードが未入力です';
        }
    }
}
session_destroy();
?>

<!DOCTYPE html>
<html>

<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【ログイン画面】</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <div>
        <a href="login.php">
            <h1>商品管理システム</h1>
        </a>

        <h3>
            <!-- ログイン失敗 -->
            <?php
            if ($error['login'] !== '') :
                print($error['login']);
                if (!empty($error['message'])) :
                    print($error['message']);
                endif; ?>
            <?php endif; ?>
        </h3>

        <form method="post" style="margin-top: 7%; background-color: whitesmoke; margin-right: 30%; margin-left: 30%;">
            <div style="font-size: 24px">
                <!-- メールアドレス入力 -->
                <strong>メールアドレス　　　</strong>
                <input type="text" name="email" size="40" maxlength="255" value="<?php print(htmlspecialchars($email, ENT_QUOTES)); ?>" />
                <br><br>
                <!-- パスワード入力 -->
                <strong>パスワード　　　　　</strong>
                <input type="password" name="password" size="40" maxlength="16" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
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
