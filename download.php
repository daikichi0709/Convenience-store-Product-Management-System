<?php
session_start();
// DB接続
require('Common.php');

// 出力情報の設定
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=DownloadProduct.csv");
header("Content-Transfer-Encoding: binary");

// 変数の初期化
$items = array();
$csv = null;

//削除フラグが「1」のモノは取得しない
$items = $db->query('SELECT item_id, item_name, item_desc, item_comp, country, price, w_price, stock, day FROM t_inventories WHERE NOT del_flg = 1');
$items->fetch();
$items->execute();

$csv .= '"商品番号","商品名","商品説明","仕入先","生産国","価格","仕入れ価格","在庫数","入荷日"' . "\r\n";

foreach ($items as $item) {
    $item['day'] = str_replace('-', '/', $item['day']);
    $csv .= '"' . $item['item_id'] . '","' . $item['item_name'] . '","' . $item['item_desc'] . '","' . $item['item_comp'] . '","' . $item['country'] . '","' . $item['price'] . '","' . $item['w_price'] . '","' . $item['stock'] . '","' . $item['day'] . '"' . "\r\n";
}
// CSVファイル出力
echo $csv;
return;
