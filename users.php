<?php
session_start();
// DB接続
require('Common.php');

$keyid = $_SESSION['login']['user_id'];
$login_userdata = $db->prepare('SELECT auth FROM m_users WHERE user_id=?');
$login_userdata->execute(array($keyid));
$auth = $login_userdata->fetch();

$ok_code = $_SESSION['login']['ok_code'];

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

//削除フラグが「1」のモノは取得しない
$users = $db->prepare('SELECT user_id, user_name, email, auth, lock_flg FROM m_users WHERE del_flg <> 1 ORDER BY user_id ASC LIMIT ?,10');
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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/usersamp.js"></script>
    <script type="text/javascript" src="js/lockswitch.js"></script>
</head>

<body>
    <?php if ($auth['auth'] !== "1") : ?>
        <!-- ユーザー管理一覧へ画面遷移 -->
        <form action="menu.php">
            <div style="display:inline-flex;">
                <input type="submit" value="メニュー画面へ移動" style="font-size: 30px; margin:50px; float:left; height: 100px; width: 300px; background-color:coral;" onmouseover="this.style.background='#99ccff'" onmouseout="this.style.background='#ff7f50';">
            </div>
        </form>
    <?php else : ?>

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
            <?php $_SESSION['login']['ok_code'] = 0; ?>

            <p style="font-size: 20px; margin-right: 15%; text-align: right;">
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
                        <td><?php if ($user['auth'] === '1') : ?>管理者<?php elseif ($user['auth'] === '2') : ?>発注担当者<?php elseif ($user['auth'] === '3') : ?>閲覧者<?php endif; ?></td>
                        <td><?php if ($user['lock_flg'] === '1' || $user['lock_flg'] === '9') : ?>ロック中<?php endif; ?></td>

                        <td><a href="upd_users.php?user_id=<?php print(htmlspecialchars($user['user_id'], ENT_QUOTES)); ?>">編集</a>
                            |<input type="hidden" name="user_id" class="user_id" value="<?php print(htmlspecialchars($user['user_id'], ENT_QUOTES)); ?>" />
                            <a class="push1" href="">削除</a>
                            |<input type="hidden" name="user_id" class="user_id" value="<?php print(htmlspecialchars($user['user_id'], ENT_QUOTES)); ?>" />
                            <a class="push2" href=""><?php if ($user['lock_flg'] === '0') : ?>ロック<?php elseif ($user['lock_flg'] === '1' || $user['lock_flg'] === '9') : ?>ロック解除<?php endif; ?></a>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

            </table>

            <br>

            <p style="margin-right: 20%; text-align: right;">

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
            <p style="margin-left: 10%; text-align: left;">≪ 戻る
        </a>
    <?php endif; ?>
</body>

</html>
