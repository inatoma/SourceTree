<?php
//セッションの開始
session_start();

//クリックジャッキングへの対策
header("X-Frame-Options: DENY");

//フォームを経ずにこのページに直接アクセスした場合は拒否する
if (!isset($_POST["token"])) {
    echo "不正なアクセスの可能性があります";
    exit;
}

//フォームに入力された値のエスケープ処理
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, "UTF-8");
}

//入力内容を$_SESSIONに格納する
$_SESSION["name"] = e($_POST["name"]);
$_SESSION["email"] = e($_POST["email"]);
$_SESSION["number"] = e($_POST["number"]);
$_SESSION["remarks"] = e($_POST["remarks"]);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認してー</title>
</head>

<body>
    <h2>入力内容の確認画面</h2>
    <form action="complete.php" method="post">
        <label for="name">お名前</label>
        <input type="text" name="name" value="<?php echo $_SESSION["name"] ?>">
        <br>
        <label for="email">メールアドレス</label>
        <input type="email" name="email" value="<?php echo $_SESSION["email"] ?>">
        <br>
        <label for="number">連絡先番号</label>
        <input type="number" name="number" value="<?php echo $_SESSION["number"] ?>">
        <br>
        <label for="remarks">ひとこと</label>
        <textarea rows="6" cols="50" name="remarks"><?php echo $_SESSION["remarks"] ?></textarea>
        <br>
        <!-- 入力フォームから送られてきたトークンを次のページに引き継ぐ -->
        <input type="hidden" name="token" value="<?= $_POST['token'] ?>">
        <br>
        <input type="button" name="back" value="戻って修正" onclick="history.back(-1)">
        <input type="submit" name="send" value="送信">
    </form>

</body>

</html>