<?php
//メール設定
//PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Composer オートローダーのの読み込み
require 'vendor/autoload.php';

//セッションの開始
session_start();

//クリックジャッキングへの対策
header("X-Frame-Options: DENY");

//フォームを経ずにこのページに直接アクセスした場合は拒否する
if (!isset($_POST["token"])) {
    echo "不正なアクセスの可能性があります";
    exit;
}

//キーとトークンが一致したら管理者に入力内容がメールで送られる
if ($_SESSION["key"] === $_POST["token"]) {
    $name = $_SESSION["name"];
    $email = $_SESSION["email"];
    $number = $_SESSION["number"];
    $remarks = $_SESSION["remarks"];
    //メールの送り先
    $to = "送信先のメールアドレス";

    //メールの件名
    $subject = $name . "さんへの自動返信メールっす";

    //メール本文
    $mail_body = "<h3>" . $name . "さん</ｈ3><br>
                <p>入力された内容は以下の通りのハズです。</P>
                <p>メールアドレス: " . $email . "</p>
                <p>連絡先番号: " . $number . "</p>
                <p>ひとこと: " . $remarks . "</P>";

    //メールヘッダー
    // $header = "From: " . mb_encode_mimeheader($name) . ' <" . $email . ">';

    //言語、内部エンコーディング指定
    mb_language("ja");
    mb_internal_encoding("UTF-8");

    //インスタンスを生成
    $mail = new PHPMailer(true);

    try {
        //サーバの設定
        $mail->SMTPDebug = 0;               //デバッグの出力（テスト用）
        $mail->isSMTP();                    //SMTPの使用
        $mail->Host = 'smtp.mailtrap.io';   // SMTPサーバー指定(mailtrap)
        $mail->SMTPAuth = true;             // SMTP authenticationの有効化
        $mail->Username = 'd5dfdc9b3caf59'; // SMTP ユーザ名
        $mail->Password = '8e97ebe9a92014'; // SMTP パスワード
        $mail->Port = 2525;                 // TCP ポートの指定
        //差出人メールアドレス、名前
        $mail->setFrom('k.inada2929@gmail.com', '太犬');
        //受信者メールアドレス、名前（入力フォームから）
        $mail->addAddress($email, $name);
        //コンテンツ設定
        $mail->isHTML(true);    // HTML形式を指定
        // メール内容
        $mail->CharSet = 'UTF-8'; //文字化け防止
        $mail->Subject = $subject;
        $mail->Body    = $mail_body;
        $mail->AltBody = strip_tags($mail_body);
        //送信
        $mail->send();
        session_destroy();
        // if (mb_send_mail($to, $subject, $comment, $header)) {

        //メールが送信出来たら$_SESSIONの値をクリア
        // $_SESSION = array();

        //メールが送信出来たらセッションを破棄
        // session_destroy();

        // $message = "送信しました";
        // } else {
        // $message = "送信に失敗しました";
        // }
    } catch (Exception $e) {
        //エラーが発生した場合
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>完</title>
</head>

<body>
    <h1>完。</h1>
    <p>指定のメールアドレスへ送信されてるハズです</p>
    <a href="index.php">
        <button type="button">に戻る</button>
    </a>
</body>

</html>