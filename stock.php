<?php
session_start();
// DB接続
require('Common.php');


$page = $_REQUEST['page'];

if (empty($page)) {
    $page = 1;
}
$page = max($page, 1);

//削除フラグが「1」のモノはカウントしない
$itemcnt = $db->query('SELECT COUNT(*) AS cnt FROM t_inventories WHERE del_flg  <>1 ');
$cnt = $itemcnt->fetch();
$maxPage = ceil($cnt['cnt'] / 10); //ceil():切り上げ

$page = min($page, $maxPage);

//出力ページで出す項目の一番上の番号の計算式
$start = ($page - 1) * 10;

//削除フラグが「1」のモノは取得しない
$items = $db->prepare('SELECT item_id, item_name, price, stock, del_flg, day FROM t_inventories WHERE del_flg <> 1 ORDER BY item_id ASC LIMIT ?,10');
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
    <title>商品管理システム【在庫一覧画面】</title>
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
                <a href="download.php">CSVダウンロード</a>
            </p><?php endif; ?>


        <table align="center" border="3" style="font-size: 24px;">

            <tr>
                <th>NO.</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>入荷日</th>

                <?php if ($auth['auth'] === "1" || $auth['auth'] === "2") : ?>
                    <th>　</th>
                <?php endif; ?>
            </tr>


            <?php while ($item = $items->fetch()) : ?>
                <tr>
                    <td><?php print(htmlspecialchars($item['item_id'], ENT_QUOTES)) ?></td>
                    <td><?php print(htmlspecialchars($item['item_name'], ENT_QUOTES)) ?></td>
                    <td><?php print(htmlspecialchars($item['price'], ENT_QUOTES)) ?></td>
                    <td><?php print(htmlspecialchars($item['stock'], ENT_QUOTES)) ?></td>
                    <td><?php print(htmlspecialchars($item['day'], ENT_QUOTES)) ?></td>


                    <?php if ($auth['auth'] === "1" || $auth['auth'] === "2") : ?>
                        <td align="center">

                            <a href="upd_product.php?item_id=<?php print(htmlspecialchars($item['item_id'])); ?>">編集</a>
                            |<a href="del_product.php?item_id=<?php print(htmlspecialchars($item['item_id'])); ?>" onclick="return confirm('本当に削除してよろしいですか？');">削除</a>

                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>

            </p>
    </div>

    </table>
    <br>

    <p style="margin-right: 20%; text-align: right;">

        <?php if ($page > 1) : ?>
            <a href="stock.php?page=1">《</a>
            <?php else : ?>《
        <?php endif; ?>

        <?php if ($page > 1) : ?>
            <a href="stock.php?page=<?php print(htmlspecialchars($page - 1)); ?>">〈</a>
            <?php else : ?>〈
        <?php endif; ?>

        <?php for ($pagecut = 1; $pagecut <= $maxPage; $pagecut++) : ?>
            <a href="stock.php?page=<?php print(htmlspecialchars($pagecut)); ?>"><?php print(htmlspecialchars($pagecut)); ?></a>
        <?php endfor; ?>

        <?php if ($page < $maxPage) : ?>
            <a href="stock.php?page=<?php print(htmlspecialchars($page + 1)); ?>">〉</a>
            <?php else : ?>〉
        <?php endif; ?>

        <?php if ($page < $maxPage) : ?>
            <a href="stock.php?page=<?php print(htmlspecialchars($maxPage)); ?>">》</a>
            <?php else : ?>》
        <?php endif; ?>
    </p>

    </div>
    <a href="menu.php">
        <p style="margin-left: 10%; text-align: left;">≪ 戻る</p>
    </a>
</body>

</html>
