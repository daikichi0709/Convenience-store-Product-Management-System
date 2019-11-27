<?php
session_start();
// DB接続
require('Common.php');

if (isset($_REQUEST['item_id']) && is_numeric($_REQUEST['item_id'])) {
    $id = $_REQUEST['item_id'];

    $items = $db->prepare('SELECT item_name, item_desc, item_comp, country, price, w_price, stock FROM t_inventories WHERE item_id=?');
    $items->execute(array($id));
    $item = $items->fetch();

    if (empty($item)) {
        $_SESSION['result'] = 2; //不在フラグ
        header('Location: stock.php');
        exit();
    }
}

if (!empty($_POST)) {
    $_SESSION['item'] = $_POST;
    $errormessage  = '';

    // 商品名
    if (empty($_SESSION['item']['item_name'])) {
        $errormessage .= "商品名が未入力です" . "<br />";
    } else {
        if (mb_strlen($_SESSION['item']['item_name']) > 30) {
            $errormessage .= "商品名に入力できる文字数を超えています" . "<br>";
        }
    }

    // 商品説明
    if (empty($_SESSION['item']['item_desc'])) {
        $errormessage .= "商品説明が未入力です" . "<br>";
    } else {
        if (mb_strlen($_SESSION['item']['item_desc']) > 100) {
            $errormessage .= "商品説明に入力できる文字数を超えています" . "<br>";
        }
    }

    // 仕入先
    if (empty($_SESSION['item']['item_comp'])) {
        $errormessage .= "仕入先が未入力です" . "<br>";
    } else {
        if (mb_strlen($_SESSION['item']['item_comp']) > 20) {
            $errormessage .= "仕入先に入力できる文字数を超えています" . "<br>";
        }
    }

    // 生産国
    if (empty($_SESSION['item']['country'])) {
        $errormessage .= "生産国が未入力です<br>";
    } else {
        if (mb_strlen($_SESSION['item']['country']) > 10) {
            $errormessage .= "生産国に入力できる文字数を超えています" . "<br>";
        }
    }

    // 価格
    if (preg_match("/^[0-9]+$/", $_SESSION['item']['price'])) {
        if (strlen($_SESSION['item']['price']) > 6) {
            $errormessage .= "価格に設定できる金額を超えています" . "<br>";
        }
    } elseif (empty($_SESSION['item']['price'])) {
        $errormessage .= "価格が未入力です" . "<br>";
    } else {
        $errormessage .= "価格は半角数字で入力してください" . "<br>";
    }

    // 仕入れ価格
    if (preg_match("/^[0-9]+$/", $_SESSION['item']['w_price'])) {
        if (strlen($_SESSION['item']['w_price']) > 6) {
            $errormessage .= "仕入れ価格に設定できる金額を超えています" . "<br>";
        }
    } elseif (empty($_SESSION['item']['w_price'])) {
        $errormessage .= '仕入れ価格が未入力です<br>';
    } else {
        $errormessage .= "仕入れ価格は半角数字で入力してください" . "<br>";
    }

    // 在庫数
    if (preg_match("/^[0-9]+$/", $_SESSION['item']['stock'])) {
        if (strlen($_SESSION['item']['stock']) > 3) {
            $errormessage .= "在庫数に設定できる数量を超えています" . "<br>";
        }
    } elseif (empty($_SESSION['item']['stock'])) {
        $errormessage .= "在庫数が未入力です" . "<br>";
    } else {
        $errormessage .= "在庫数は半角数字で入力してください" . "<br>";
    }

    // 追加(登録)処理
    if (empty($errormessage)) {
        $statement = $db->prepare('UPDATE t_inventories SET item_name=?, item_desc=?, item_comp=?, country=?, price=?, w_price=?, stock=?, del_flg=0, ins_date=?, ins_user_id=?, upd_date=?, upd_user_id=? WHERE item_id=?');

        //日時設定
        $today = new DateTime();
        $instime = $today->format('Y-m-d H:i:s');

        $statement->execute(array($_SESSION['item']['item_name'], $_SESSION['item']['item_desc'], $_SESSION['item']['item_comp'],  $_SESSION['item']['country'],  $_SESSION['item']['price'], $_SESSION['item']['w_price'], $_SESSION['item']['stock'], $instime, $_SESSION['login']['user_id'], $instime,  $_SESSION['login']['user_id'], $id));

        unset($_SESSION['item']);
        if (empty($_SESSION['item'])) {
            $_SESSION['result'] = 1; //更新完了フラグ
            header('Location: stock.php');
            exit();
        }
    }
} else {
    unset($_SESSION['item']);
}
?>


<!-- 一覧から編集したい商品の情報を持ってくる -->
<!DOCTYPE html>
<html>

<head>
    <meta>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width,">
    <title>商品管理システム【商品更新画面】</title>
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
        <h1>商品編集</h1>
        <hr>
        <!-- エラーメッセージ -->
        <?php if (!empty($errormessage)) : ?>
            <p style="color: red; font-size: 20px;"><?php echo $errormessage; ?></p>
        <?php endif; ?>

        <form method="post">
            <div style="font-size: 24px">
                <!-- 商品名 -->
                <strong style="width: 200px;">商品名　　　　　</strong>
                <input type="text" placeholder="商品名を入力して下さい" name="item_name" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['item'])) {
                                                                                                                                                print(htmlspecialchars($item['item_name'], ENT_QUOTES));
                                                                                                                                            } else {
                                                                                                                                                print(htmlspecialchars($_SESSION['item']['item_name'], ENT_QUOTES));
                                                                                                                                            }
                                                                                                                                            ?>">

                <br><br>
                <!-- 商品説明 -->
                <strong style="width: 200px;">商品説明　　　　</strong>
                <input type="text" placeholder="商品説明を入力して下さい" name="item_desc" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['item'])) {
                                                                                                                                                print(htmlspecialchars($item['item_desc'], ENT_QUOTES));
                                                                                                                                            } else {
                                                                                                                                                print(htmlspecialchars($_SESSION['item']['item_desc'], ENT_QUOTES));
                                                                                                                                            } ?>">

                <br><br>
                <!-- 仕入先 -->
                <strong style="width: 200px;">仕入先　　　　　</strong>
                <input type="text" placeholder="仕入先を入力して下さい" name="item_comp" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['item'])) {
                                                                                                                                                print(htmlspecialchars($item['item_comp'], ENT_QUOTES));
                                                                                                                                            } else {
                                                                                                                                                print(htmlspecialchars($_SESSION['item']['item_comp'], ENT_QUOTES));
                                                                                                                                            } ?>">

                <br><br>
                <!-- 生産国 -->
                <strong style="width: 200px;">生産国　　　　　</strong>
                <input type="text" placeholder="生産国を入力して下さい" name="country" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['item'])) {
                                                                                                                                                print(htmlspecialchars($item['country'], ENT_QUOTES));
                                                                                                                                            } else {
                                                                                                                                                print(htmlspecialchars($_SESSION['item']['country'], ENT_QUOTES));
                                                                                                                                            }
                                                                                                                                            ?>">

                <br><br>
                <!-- 価格 -->
                <strong style="width: 200px;">価格　　　　　　</strong>
                <input type="text" placeholder="価格を入力して下さい" name="price" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['item'])) {
                                                                                                                                            print(htmlspecialchars($item['price'], ENT_QUOTES));
                                                                                                                                        } else {
                                                                                                                                            print(htmlspecialchars($_SESSION['item']['price'], ENT_QUOTES));
                                                                                                                                        }
                                                                                                                                        ?>">

                <br><br>
                <!-- 仕入れ価格 -->
                <strong style="width: 200px;">仕入れ価格　　　</strong>
                <input type="text" placeholder="仕入れ価格を入力して下さい" name="w_price" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['item'])) {
                                                                                                                                                print(htmlspecialchars($item['w_price'], ENT_QUOTES));
                                                                                                                                            } else {
                                                                                                                                                print(htmlspecialchars($_SESSION['item']['w_price'], ENT_QUOTES));
                                                                                                                                            }
                                                                                                                                            ?>">

                <br><br>
                <!-- 在庫数 -->
                <strong style="width: 200px;">在庫数　　　　　</strong>
                <input type="text" placeholder="在庫数を入力して下さい" name="stock" maxlength="255" style="font-size: 18px; width: 500px" value="<?php if (empty($_SESSION['item'])) {
                                                                                                                                            print(htmlspecialchars($item['stock'], ENT_QUOTES));
                                                                                                                                        } else {
                                                                                                                                            print(htmlspecialchars($_SESSION['item']['stock'], ENT_QUOTES));
                                                                                                                                        }
                                                                                                                                        ?>">

            </div>

            <br><br>

            <div>
                <!-- 更新ボタン -->
                <input type="submit" value="更新" style="font-size: 30px; width: 150px; height: 50px;">
            </div>
        </form>
    </div>
    <a href="stock.php">
        <p style="margin-left: 20%; text-align: left;">≪ 戻る</p>
    </a>
</body>

</html>
