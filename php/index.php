<?php
//セッションの開始
session_start();

//クリックジャッキングへの対策
header("X-Frame-Options: DENY");

//トークンの生成
$token = sha1(uniqid(rand(), true));

//トークンを$_SESSIONに格納し、それをキーとする
$_SESSION["key"] = $token;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP</title>
</head>

<body>
    <header>
        <?php include "header.php"; ?>
    </header>
    <h1>初めてのPHP XAMPPで</h1>
    <ol>
        <li>変数を設定し出力</li>
        <?php
        $hello = "こんにちは<br />";
        $msg =  "少女マンガ、万歳";
        echo $hello, $msg;
        ?>
        <!-- <li>変数を出力</li> -->
        <li>四則演算と出力</li>
        <?php
        $num = 3;
        echo "足し算&colon; $num + $num &equals; ", $num + $num, "<br />";
        echo "引き算&colon; $num - $num &equals; ", $num - $num, "<br />";
        echo "掛け算&colon; $num * $num &equals; ", $num * $num, "<br />";
        echo "割り算&colon; $num / $num &equals; ", $num / $num, "<br />";
        ?>
        <!-- <li>四則演算結果を出力</li> -->
        <li>ヘッダー、フッターなどの共通パーツを切り出して呼び出す</li>
        ヘッダーとフッターを共通にしています
        <li>HTMLと組み合わせて簡単な電卓</li>
        <h2>電卓&lpar;簡易版&rpar;</h2>
        <form action="calculator.php" method="get">
            <label for="num1"><b>数値1</b></label>
            <input type="text" name="num1">
            <br><br>
            <label for="num2"><b>数値2</b></label>
            <input type="text" name="num2">
            <br>
            <?php echo "&ast;数字のみ必ず入力で&excl;" ?>
            <br><br>

            <input type="radio" name="rbutton" value="add">&plus;
            <input type="radio" name="rbutton" value="sub">&ndash;
            <br>
            <input type="radio" name="rbutton" value="mul">&ast;
            <input type="radio" name="rbutton" value="div">&sol;
            <br><br>
            <input type="submit" value="計算する">
            <br><br>
        </form>
        <li>配列を呼び出す</li>
        <?php
        $data = array("晴れ", "曇り", "雨", "雪", "雷");
        echo $data[1], "、 ", $data[4];
        ?>
        <li>配列をループで呼び出す、forとwhileの双方を試す</li>
        <h3>forループで表示</h3>
        <?php
        $numbers = array(1, 20, 33, 400, -5);
        $total = 0;
        for ($i = 0; $i < count($numbers); $i++) {
            $number = $numbers[$i];
            echo "インデックス$i&colon; ", $number, "<br />";
            $total += $numbers[$i];
        }
        echo "合計は、", $total;
        ?>
        <br>
        <h3>whileループで表示</h3>
        <?php
        $animals = array("犬", "猫", "猿", "鳥", "馬");
        $counter = 0;
        while ($counter < count($animals)) {
            echo $animals[$counter], ", ";
            $counter++;
        }
        ?>
        <!-- <li>ループにおいて、Forとwhileの双方を試す</li> -->
        <li>フォームを利用してデータを送信し、別のページ（同じページでもOK）に送信したデータを表示しメールで送信</li>
        <form action="confirm.php" method="post">
            <h3>お問い合わせフォーム</h3>
            <label for="name">お名前</label>
            <input type="text" name="name">
            <br>
            <label for="email">メールアドレス</label>
            <input type="email" name="email">
            <br>
            <label for=" number">連絡先番号</label>
            <input type="number" name="number">
            <br>
            <label for="remarks">ひとこと</label>
            <textarea rows="6" cols="50" name="remarks"></textarea>
            <br>
            <!-- 作成したトークンを次のページに引き継ぐ-->
            <input type="hidden" name="token" value="<?= $token ?>">
            <br>
            <input type="submit" name="check" value="確認">
            <br><br>
        </form>
        <!-- <li>フォームに入力したデータをメールで送信</li> -->
        <li>上記までに作成したものを利用して、簡単な条件分岐を試す</li>
        <h3>ifで条件分岐</h3>
        <?php
        // $animals = array("犬", "猫", "猿", "鳥", "馬");
        if ($animals[1] == "犬") {
            echo "ワンコ";
        } else {
            echo "ワンコでない";
        }
        ?>
        <h3>switchで条件分岐</h3>
        <!-- $numbers = array(1, 20, 33, 400, -5); -->
        <?php
        for ($i = 0; $i < count($numbers); $i++) {
            switch ($numbers[$i]) {
                case -5:
                    echo " マイナス５ ";
                    break;
                case 1:
                case 33:
                case 400:
                    echo " 20以外 ";
                    break;
                case 20:
                    echo " 二十 ";
                    break;
                default:
                    echo " 何もない ";
                    break;
            }
        }
        ?>
        <!-- <li>条件分岐において、IFとswitchの両方を利用してみる</li> -->
        <li>上記をできるだけすべて取り込んだ関数（Function）を作成してみる</li>
        <?php
        function short($hello, $msg, $num)
        {
            echo "変数の設定: ";
            echo $hello . " " . $msg . "<br>";
            echo "四則演算: " . "<br>";
            echo "足し算&colon; $num + $num &equals; ", $num + $num, "<br />";
            echo "引き算&colon; $num - $num &equals; ", $num - $num, "<br />";
            echo "掛け算&colon; $num * $num &equals; ", $num * $num, "<br />";
            echo "割り算&colon; $num / $num &equals; ", $num / $num, "<br />";
        }

        echo short("おはようございます", "日本", "11");
        echo "<br>";
        function long($data, $numbers)
        {
            echo "配列のループ呼び: for" . "<br>";
            for ($i = 0; $i < count($data); $i++) {
                echo $data[$i] . ", ";
            }
            echo "<br>";
            echo "配列のループ呼び: while" . "<br>";
            $counter = 0;
            while ($counter < count($numbers)) {
                echo $numbers[$counter] . ", ";
                $counter++;
            }
        }

        $food = array("ウナギ", "寿司", "米", "パスタ", "ビール");
        $customer = array("女性", 90, "神奈川県", "男性", 60, "北海道", "女性", 30, "鹿児島");
        echo long($food, $customer);
        ?>
    </ol>
    <footer>
        <?php include "footer.php"; ?>
    </footer>
</body>

</html>