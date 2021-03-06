<?php
session_start();
// DB接続
require('Common.php');


$ok_code = $_SESSION['result'];

$page = $_GET['page'];

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
    <link rel="stylesheet" href="style.css">

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/sample.js"></script>
</head>

<body>
    <div>

        <h1>在庫一覧</h1>

        <?php if ($ok_code === 1) : ?>
            <script>
                alert('登録が完了しました')
            </script>
        <?php elseif ($ok_code === 2) : ?>
            <script>
                alert('商品が存在しません')
            </script>
        <?php endif; ?>
        <?php unset($_SESSION['result']); ?>

        <?php if ($auth['auth'] === "1" || $auth['auth'] === "2") : ?>
            <p class="admintab">
                <a href="ins_product.php">新規追加</a>
                <a href="download.php">CSVダウンロード</a>
            </p>
        <?php endif; ?>

        <table border="3" style="font-size: 24px;">

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
                        <td style="text-align: center;">

                            <a href="upd_product.php?item_id=<?php print(htmlspecialchars($item['item_id'], ENT_QUOTES)); ?>">編集</a>
                            |<input type="hidden" name="item_id" class="item_id" value="<?php print(htmlspecialchars($item['item_id'], ENT_QUOTES)); ?>" />
                            <a class="push" href="">削除</a>

                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>

            </p>


        </table>
        <br>

        <p style="margin-left: 70%; text-align: right; background-color:whitesmoke; width: 250px;">

            <?php if ($page > 1) : ?>
                <a href="stock.php?page=1">《</a>
                <a href="stock.php?page=<?php print(htmlspecialchars(($page - 1), ENT_QUOTES)); ?>">〈</a>
            <?php else : ?>
                《〈
            <?php endif; ?>

            <?php for ($pagecut = 1; $pagecut <= $maxPage; $pagecut++) : ?>
                <a href="stock.php?page=<?php print(htmlspecialchars(($pagecut), ENT_QUOTES)); ?>"><?php print(htmlspecialchars($pagecut, ENT_QUOTES)); ?></a>
            <?php endfor; ?>

            <?php if ($page < $maxPage) : ?>
                <a href="stock.php?page=<?php print(htmlspecialchars(($page + 1), ENT_QUOTES)); ?>">〉</a>
                <a href="stock.php?page=<?php print(htmlspecialchars($maxPage, ENT_QUOTES)); ?>">》</a>
            <?php else : ?>
                〉》
            <?php endif; ?>
        </p>

    </div>
    <a href="menu.php">
        <p class="backtab">≪ 戻る</p>
    </a>
</body>

</html>
