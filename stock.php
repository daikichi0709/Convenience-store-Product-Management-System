<?php
session_start();
// DB接続
require('Common.php');


$page = $_REQUEST['page'];

if ($page == '') {
    $page = 1;
}
$page = max($page, 1);

//削除フラグが「1」のモノはカウントしない
$itemcnt = $db->query('SELECT COUNT(*) AS cnt FROM t_inventories WHERE NOT del_flg = 1 ');
$cnt = $itemcnt->fetch();
$maxPage = ceil($cnt['cnt'] / 10); //ceil():切り上げ

$page = min($page, $maxPage);

//出力ページで出す項目の一番上の番号の計算式
$start = ($page - 1) * 10;

//削除フラグが「1」のモノは取得しない
$items = $db->prepare('SELECT item_id, item_name, price, stock, del_flg, day FROM t_inventories WHERE NOT del_flg = 1 ORDER BY item_id ASC LIMIT ?,10');
$items->bindParam(1, $start, PDO::PARAM_INT);
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

        <?php if ($auth['auth'] === "1" || $auth['auth'] === "2") : ?>
            <p style="font-size: 20px; margin-right: 10%; text-align: right;">
                <a href="ins_product.php">新規追加</a>
                <!--↓↓↓↓↓↓↓↓↓　CSVダウンロード未実装　↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓-->
                <a href="">CSVダウンロード</a>
            </p><?php endif; ?>


        <center>
            <table border="3" style="font-size: 24px;">
                <!-- 1行目：項目名 -->
                <tr>
                    <th>NO.</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>入荷日</th>
                    <!-- 権限が「1」「2」のユーザーのみの仕様 -->
                    <?php if ($auth['auth'] === "1" || $auth['auth'] === "2") : ?>
                        <th>　</th>
                    <?php endif; ?>
                </tr>

                <!-- 2行目以降：項目 -->
                <?php while ($item = $items->fetch()) : ?>
                    <?php if ($item['del_flg'] === "0") : ?>
                        <tr>
                            <td><?php print($item['item_id']) ?></td>
                            <td><?php print($item['item_name']) ?></td>
                            <td><?php print($item['price']) ?></td>
                            <td><?php print($item['stock']) ?></td>
                            <td><?php print($item['day']) ?></td>

                            <!-- 権限が「1」「2」のユーザーのみの仕様 -->
                            <?php if ($auth['auth'] === "1" || $auth['auth'] === "2") : ?>
                                <th>
                                    <a href="upd_product.php?item_id=<?php print($item['item_id']); ?>">編集</a>
                                    |<a href="del_product.php?item_id=<?php print($item['item_id']); ?>" onclick="return confirm('本当に削除してよろしいですか？');">削除</a>
                                </th>
                            <?php endif; ?>
                        </tr>
                    <?php elseif ($item['del_flg'] === 1) : ?>
                    <?php endif; ?>
                <?php endwhile; ?>

                </p>
    </div>

    </table>
    </center>

    <br>

    <p style="margin-right: 20%; text-align: right;">

        <?php if ($page > 1) : ?>
            <a href="stock.php?page=1">《</a>
            <?php else : ?>《
        <?php endif; ?>

        <?php if ($page > 1) : ?>
            <a href="stock.php?page=<?php print($page - 1); ?>">〈</a>
            <?php else : ?>〈
        <?php endif; ?>

        <?php for ($pagecut = 1; $pagecut <= $maxPage; $pagecut++) : ?>
            <a href="stock.php?page=<?php print($pagecut); ?>"><?php print($pagecut); ?></a>
        <?php endfor; ?>

        <?php if ($page < $maxPage) : ?>
            <a href="stock.php?page=<?php print($page + 1); ?>">〉</a>
            <?php else : ?>〉
        <?php endif; ?>

        <?php if ($page < $maxPage) : ?>
            <a href="stock.php?page=<?php print($maxPage); ?>">》</a>
            <?php else : ?>》
        <?php endif; ?>
    </p>

    </div>
    <a href="menu.php">
        <p style="margin-left: 10%; text-align: left;">≪ 戻る
    </a>
</body>

</html>
