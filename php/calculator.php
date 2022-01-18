<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>計算結果</title>
</head>

<body>
    <?php
    $num1 = $_GET["num1"];
    $num2 = $_GET["num2"];

    $operation = $_GET["rbutton"];

    $answer = "";

    switch ($operation) {
        case "add":
            $answer = $num1 + $num2;
            echo ("足し算の結果: " . $answer);
            break;
        case "sub":
            $answer = $num1 - $num2;
            echo ("引き算の結果: " . $answer);
            break;
        case "mul":
            $answer = $num1 * $num2;
            echo ("掛け算の結果: " . $answer);
            break;
        case "div":
            $answer = $num1 / $num2;
            echo ("割り算の結果: " . $answer);
            break;
    }
    ?>
</body>

</html>