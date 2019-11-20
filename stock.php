<?php
session_start();
// DB接続
require('dbconnect.php');


$page = $_REQUEST['page'];

if($page == ''){
  $page = 1;
}
$page = max($page, 1);

$itemcnt = $db->query('SELECT COUNT(*) AS cnt FROM t_inventories');
$cnt = $itemcnt->fetch();
$maxPage = ceil($cnt['cnt'] / 10); //ceil():切り上げ

$page = min($page,$maxPage);

//出力ページで出す項目の一番上の番号の計算式
$start = ($page - 1) * 10;

$items = $db->prepare('SELECT item_id, item_name, price, stock, day FROM t_inventories ORDER BY item_id ASC LIMIT ?,10');
$items->bindParam(1,$start,PDO::PARAM_INT);
$items->execute();

$keyid = $_SESSION['login']['user_id'];
$login_userdata = $db->prepare('SELECT auth FROM m_users WHERE user_id=?');
$login_userdata->execute(array($keyid));
$auth = $login_userdata->fetch();

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

    <a>
    <h1>在庫一覧</h1>
    </a>

    <p style="font-size: 20px; margin-right: 10%; text-align: right;">
    <a href="ins_product.php">新規追加</a>
    <a href="">CSVダウンロード</a>
    </p>


    <center><table border="3" style="font-size: 24px;">
    <!-- 1行目：項目名 -->
    <tr>
    <th>NO.</th>
    <th>商品名</th>
    <th>価格</th>
    <th>在庫数</th>
    <th>入荷日</th>
    <th>　</th>
    </tr>

    <!-- 2行目以降：項目 -->
    <!-- <php for($i = 1;$i <= 10;$i++): ?> -->
    <?php while ($item = $items->fetch()): ?>
    <tr>
    <td><?php print($item['item_id']) ?></td>
    <td><?php print($item['item_name']) ?></td>
    <td><?php print($item['price']) ?></td>
    <td><?php print($item['stock']) ?></td>
    <td><?php print($item['day']) ?></td>

    <th><?php if($auth['auth'] === "1" || $auth['auth'] ==="2"): ?>
    <a href="upd_product.php">編集</a>|<a href="del_product.php">削除</a></th>
    <?php endif; ?></tr>
    <!-- <php endfor; ?> -->
    <?php endwhile; ?>

    </p>
    </div>

    </table></center>

    <br>

    <p style="margin-right: 20%; text-align: right;">

    <?php if($page > 1): ?>
    <a href="stock.php?page=1">《</a>
    <?php else: ?>《<?php endif; ?>

    <?php if($page > 1): ?>
    <a href="stock.php?page=<?php print($page-1); ?>">〈</a>
    <?php else: ?>〈<?php endif; ?>

    <a href="">1<?php  ?></a> <!-- 数字は変数に変えます。 -->
    <a href="">2<?php  ?></a> <!-- 数字は変数に変えます。 -->
    <a href="">3<?php  ?></a> <!-- 数字は変数に変えます。 -->
    <a href="">4<?php  ?></a> <!-- 数字は変数に変えます。 -->

    <?php if($page < $maxPage): ?>
    <a href="stock.php?page=<?php print($page+1); ?>">〉</a>
    <?php else: ?>〉<?php endif; ?>

    <?php if($page < $maxPage): ?>
    <a href="stock.php?page=<?php print($maxPage); ?>">》</a>
    <?php else: ?>》<?php endif; ?>
    </p>

    </div>
    <a href="menu.php"><p style="margin-left: 10%; text-align: left;">≪ 戻る</a>
</body>
</html>
