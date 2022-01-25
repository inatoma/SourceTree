<!-- This works !! -->
<?php
$msg = "";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Composer オートローダーのの読み込み
require 'vendor/autoload.php';

if (isset($_POST['submit'])) {
    $subject = $_POST['subject'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != "") {
        $file = "attachment/" . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $file);
    } else
        $file = "";

    // CSVに出力
    $msg = array("メールアドレス" => $email, "お問い合わせ内容" => $message);

    $ShiftJIS = $msg;
    mb_convert_variables('Shift_JIS', 'UTF-8', $ShiftJIS);
    $csv = fopen('test.csv', 'a');
    fputcsv($csv, $ShiftJIS);
    fclose($csv);

    //言語、内部エンコーディング指定
    mb_language("ja");
    mb_internal_encoding("UTF-8");

    $mail = new PHPMailer();

    //if we want to send via SMTP
    $mail->Host = "smtp.mailtrap.io";
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Username = "d5dfdc9b3caf59";
    $mail->Password = "8e97ebe9a92014";
    // $mail->SMTPSecure = "ssl"; //TLS
    $mail->Port = 2525; //587

    $mail->CharSet = 'UTF-8'; //文字化け防止
    $mail->addAddress($email);
    $mail->setFrom('k.inada2929@gmail.com', '太犬');
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = $message;
    $mail->addAttachment($file);

    if ($mail->send())
        $msg = "Your email has been sent, thank you!";
    else
        $msg = "Please try again!";

    //添付ファイル削除
    // unlink($file);
    if (!empty($file)) {
        unlink($file);
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>

<body>
    <div class="container" style="margin-top: 100px">
        <div class="row justify-content-center">
            <div class="col-md-6 col-md-offset-3" align="center">
                <img src="images/logo.png"><br><br>

                <?php if ($msg != "") echo "$msg<br><br>"; ?>

                <form method="post" action="attachemnt.php" enctype="multipart/form-data">
                    <input class="form-control" name="subject" placeholder="Subject..."><br>
                    <input class="form-control" name="email" type="email" placeholder="Email..."><br>
                    <textarea placeholder="Message..." class="form-control" name="message"></textarea><br>
                    <input class="form-control" type="file" name="attachment"><br>
                    <input class="btn btn-primary" name="submit" type="submit" value="Send Email">
                </form>
            </div>
        </div>
    </div>
</body>

</html>