<?php
session_start();
// DB接続
require('Common.php');

if (isset($_REQUEST['item_id']) && is_numeric($_REQUEST['item_id'])) {
    $id = $_REQUEST['item_id'];

    $today = new DateTime();
    $deltime = $today->format('Y-m-d H:i:s');

    $items = $db->prepare('UPDATE t_inventories SET del_flg=1, upd_date=?, upd_user_id=? WHERE item_id=?');
    $items->execute(array($deltime, $_SESSION['login']['user_id'], $id));
    header('Location: stock.php');
} else {
    ?>
    <!-- PHPでアラートを表示する -->
    <script>
        alert('データ不整合です')
    </script>
<?php }
