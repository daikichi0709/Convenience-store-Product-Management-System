<?php
session_start();
// DB接続
require('Common.php');

$ok_code = $_SESSION['result'];

$keyid = $_SESSION['login']['user_id'];
$login_userdata = $db->prepare('SELECT auth FROM m_users WHERE user_id=?');
$login_userdata->execute(array($keyid));
$auth = $login_userdata->fetch();

if ($auth['auth'] !== "1") {
    header('Location: error.php');
    exit();
}

$page = $_REQUEST['page'];

if (empty($page)) {
    $page = 1;
}
$page = max($page, 1);


$usercnt = $db->query('SELECT COUNT(*) AS cnt FROM m_users WHERE del_flg  <>1 ');
$cnt = $usercnt->fetch();
$maxPage = ceil($cnt['cnt'] / 10); //ceil():切り上げ

$page = min($page, $maxPage);

//出力ページで出す項目の一番上の番号の計算式
$start = ($page - 1) * 10;

//ユーザーの一覧情報の取得
$users = $db->prepare('SELECT u.user_id, u.user_name, u.email, u.auth, u.lock_flg , a.auth_name FROM m_users as u INNER JOIN m_auth as a ON u.auth = a.auth AND u.del_flg <> 1 ORDER BY u.user_id ASC LIMIT ?,10');
$users->bindParam(1, $start, PDO::PARAM_INT);
$users->execute();
?>

<!DOCTYPE html>
<html>

<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【ユーザー一覧画面】</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        body {
            height: 100;
            text-align: center;
            margin-top: 3%;
            background-image: url("レトロ.jpg");
        }

        h1 {
            margin-right: 20%;
            margin-left: 20%;
            background-color: whitesmoke;
        }

        h3 {
            color: red;
        }

        table {
            background-color: whitesmoke;
            width: 80%;
            height: 40%;
        }
    </style>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/usersamp.js"></script>
</head>

<body>
    <div>
        <a>
            <h1>ユーザー一覧</h1>
        </a>
        <?php if ($ok_code === 1) : ?>
            <script>
                alert('登録が完了しました')
            </script>
        <?php elseif ($ok_code === 2) : ?>
            <script>
                alert('ユーザーが存在しません')
            </script>
        <?php endif; ?>
        <?php unset($_SESSION['result']); ?>

        <p style="font-size: 20px; margin-left: 80%; text-align: right; background-color:whitesmoke; width: 100px;">
            <a href="ins_users.php">新規追加</a>

        </p>

        <table align="center" border="3" style="font-size: 24px;">
            <!-- 1行目：項目名 -->
            <tr>
                <th>NO.</th>
                <th>ユーザー名</th>
                <th>メールアドレス</th>
                <th>権限</th>
                <th>ロック<br>状態</th>
                <th>　</th>
            </tr>

            <!-- 2行目以降：項目 -->
            <?php while ($user = $users->fetch()) : ?>
                <tr>
                    <td><?php print(htmlspecialchars($user['user_id'], ENT_QUOTES)) ?></td>
                    <td><?php print(htmlspecialchars($user['user_name'], ENT_QUOTES)) ?></td>
                    <td><?php print(htmlspecialchars($user['email'], ENT_QUOTES)) ?></td>
                    <td><?php print(htmlspecialchars($user['auth_name'], ENT_QUOTES)) ?></td>
                    <td><?php if ($user['lock_flg'] === '1' || $user['lock_flg'] === '9') : ?>ロック中<?php endif; ?></td>

                    <td>
                        <a href="upd_users.php?user_id=<?php print(htmlspecialchars($user['user_id'], ENT_QUOTES)); ?>">編集</a>
                        |<input type="hidden" name="user_id" class="user_id" value="<?php print(htmlspecialchars($user['user_id'], ENT_QUOTES)); ?>" />
                        <a class="delete" href="">削除</a>
                        |<input type="hidden" name="user_id" class="user_id" value="<?php print(htmlspecialchars($user['user_id'], ENT_QUOTES)); ?>" />
                        <a class="lock" href=""><?php if ($user['lock_flg'] === '0') : ?>ロック<?php elseif ($user['lock_flg'] === '1' || $user['lock_flg'] === '9') : ?>ロック解除<?php endif; ?></a>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

        </table>

        <br>

        <p style="margin-left: 70%; text-align: right; background-color:whitesmoke; width: 250px;">

            <?php if ($page > 1) : ?>
                <a href="users.php?page=1">《</a>
                <a href="users.php?page=<?php print(htmlspecialchars($page - 1)); ?>">〈</a>
                <?php else : ?>《〈
            <?php endif; ?>

            <?php for ($pagecut = 1; $pagecut <= $maxPage; $pagecut++) : ?>
                <a href="users.php?page=<?php print(htmlspecialchars($pagecut)); ?>"><?php print(htmlspecialchars($pagecut)); ?></a>
            <?php endfor; ?>

            <?php if ($page < $maxPage) : ?>
                <a href="users.php?page=<?php print(htmlspecialchars($page + 1)); ?>">〉</a>
                <a href="users.php?page=<?php print(htmlspecialchars($maxPage)); ?>">》</a>
                <?php else : ?>〉》
            <?php endif; ?>

        </p>

    </div>
    <a href="menu.php">
        <p style="background-color:whitesmoke; margin-left: 10%; text-align: left; font-size: 18px; width: 75px;">≪ 戻る</p>
    </a>
</body>

</html>
