<?php
session_start();
// DB接続
require('Common.php');

//削除フラグが「1」のモノは取得しない
$items = $db->query('SELECT item_id, item_name, item_desc, item_comp, country, price, w_price, stock, day FROM t_inventories WHERE NOT del_flg = 1');
$items->fetch();
$items->execute();

$address = 'C:\Users\3CA\Desktop\phpSystem.csv';

$csv = file_get_contents($address);
$csv .= '"商品番号","商品名","商品説明","仕入先","生産国","価格","仕入れ価格","在庫数","入荷日"' . "\r\n";

foreach ($items as $item) {
    $csv .= '"' . $item['item_id'] . '","' . $item['item_name'] . '","' . $item['item_desc'] . '","' . $item['item_comp'] . '","' . $item['country'] . '","' . $item['price'] . '","' . $item['w_price'] . '","' . $item['stock'] . '","' . $item['day'] . '"' . "\r\n";
}
$csv = str_replace('-', '/', $csv);
file_put_contents($address, $csv);
// header('Location: stock.php');
?>
<!DOCTYPE html>
<html>

<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【CSVダウンロード】</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        html,
        body {
            height: 100;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div style="margin-top: 15%">
        <p>
            <h2>
                CSVダウンロードが完了しました。<br>
                一覧画面に戻ります。
            </h2>
        </p>

        <form action="stock.php">
            <div>
                <input type="submit" value="OK"" style=" font-size: 30px; width: 150px; height: 50px;">
            </div>
        </form>
    </div>
</body>

</html>
