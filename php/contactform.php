<?php
//セッションの開始
session_start();

// クロスサイトスクリプティング（XSS）対策
function hsc($s)
{
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

$mode = 'input';

// 配列（エラーメッセージ）の初期化
$errmessage = array();

if (isset($_POST['back']) && $_POST['back']) {
    // backの時は何もしない
} else if (isset($_POST['check']) && $_POST['check']) {
    // checkの時は確認画面へ

    //---------- 名前チェック ----------//
    // 未入力チェック
    if (empty($_POST['name'])) {
        $errmessage[] = "お名前を入力してください";
    }
    $_SESSION['name'] = hsc($_POST['name']);
    //---------- ふりがなチェック ----------//
    // 未入力チェック
    if (empty($_POST['yomi'])) {
        $errmessage[] = "ふりがなを入力してください";
        // ひらがなチェック
    } else if (!mb_ereg("^[ぁ-ん]+$", $_POST['yomi'])) {
        $errmessage[] = 'ひらがなで入力して下さい';
    }
    $_SESSION['yomi'] = hsc($_POST['yomi']);
    //---------- メールアドレスチェック ----------//
    // 未入力チェック
    if (empty($_POST['email'])) {
        $errmessage[] = "メールアドレスを入力してください";
    } else {
        // 全角英数字を半角に変換
        $_POST['email'] = mb_convert_kana($_POST['email'], "a");
        // メールアドレスの形式チェック
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errmessage[] = 'メールアドレスを正確に入力して下さい';
        }
    }
    $_SESSION['email'] = hsc($_POST['email']);
    //---------- 電話番号チェック ----------//
    if (!empty($_POST['number'])) {
        // 全角数字とハイフンを半角に変換
        $_POST['number'] = mb_convert_kana($_POST['number'], "na");
        //半角のハイフンを取り除く
        $_POST['number'] = mb_ereg_replace("-", "", $_POST['number']);
        // 数値チェック
        if (!ctype_digit($_POST['number'])) {
            $errmessage[] = '数値を入れて下さい';
        }
    }
    $_SESSION['number'] = hsc($_POST['number']);
    //---------- 住所 ----------//
    $_SESSION['address'] = hsc($_POST['address']);
    //---------- お問い合わせ内容チェック ----------//
    // 未入力チェック
    if (empty($_POST['message'])) {
        $errmessage[] = "お問い合わせ内容を入力してください";
        // 文字数チェック
    } else if (mb_strlen($_POST['message']) > 500) {
        $errmessage[] = "お問い合わせ内容は500文字以内にしてください";
    }
    $_SESSION['message'] = hsc($_POST['message']);

    //添付ファイルバリデーション 
    function fileValidatorSize($data)
    {
        //ファイルサイズの上限を5MB単位で指定
        $allowMaxSize = 2;
        if ($data['size'] < $allowMaxSize * 5242880) {  // 5242880 byte = 5MB
            return false;
        } else {
            return true;
        }
    }
    function fileValidatorType($data)
    {
        //許可するファイルのMIMEタイプを指定
        $allowFileType = array(
            'image/jpeg',
            'image/png',
            'image/gif',
            'text/plain',
            'text/csv',
            'application/pdf',
            'application/zip'
        );
        if (in_array($data['type'], $allowFileType)) {
            return false;
        } else {
            return true;
        }
    }
    $isErrorFileSize = fileValidatorSize($_FILES['attach']);
    $isErrorFileType = fileValidatorType($_FILES['attach']);

    // //添付ファイルアップロード
    $fileTempName = $_FILES['attach']['tmp_name'];
    $fileName = $_FILES['attach']['name'];
    $attachedFile = "";
    $fileType = "";
    if (!$isErrorFileSize && !$isErrorFileType) {
        if (!empty($fileTempName)) {
            $isUploaded = move_uploaded_file($fileTempName, 'attachment/' . $fileName);
            if ($isUploaded) {
                $attachedFile = $fileName;
                // if (strpos($_FILES['attach']['type'], 'image') !== false) {
                //     $fileType = 'image';
                // } else {
                //     $fileType = 'other';
                // }
                // $uploadError = false;
            } else {
                $uploadError = true;
            }
        }
    } else {
        $uploadError = true;
    }

    //SESSIONへ受け渡し
    if (!$uploadError) {
        $_SESSION['attach'] = hsc($attachedFile);
    }

    if ($errmessage) {
        // エラーがある場合はお問い合わせフォーム画面に戻る
        $mode = 'input';
    } else {
        // クロスサイトリクエストフォージェリ（CSRF）対策
        // トークンの生成（php7以降）
        $token = bin2hex(random_bytes(32));
        $_SESSION['token'] = $token;
        $mode = 'check';
    }
} else if (isset($_POST['send']) && $_POST['send']) {
    // sendの時は送信
    // トークン情報が渡ってこなかった、セッションに入っていなかった、メールアドレスがセッションになかった時
    if (!$_POST['token'] || !$_SESSION['token'] || !$_SESSION['email']) {
        $errmessage[] = '不正な処理が行われました';
        $_SESSION = array();
        $mode = 'input';
        // トークンのチェック
    } else if ($_POST['token'] != $_SESSION['token']) {
        $errmessage[] = '不正な処理が行われました';
        $_SESSION = array();
        $mode = 'input';
    } else {
        $body = "お問い合わせを受け付けました \r\n"
            . "お名前: " . $_SESSION['name'] . "\r\n"
            . "ふりがな: " . $_SESSION['yomi'] . "\r\n"
            . "メールアドレス: " . $_SESSION['email'] . "\r\n"
            . "電話番号: " . $_SESSION['number'] . "\r\n"
            . "住所: " . $_SESSION['address'] . "\r\n"
            . "お問い合わせ内容:\r\n"
            // 改行コードを揃える
            . preg_replace("/\r\n|\r|\n/", "\r\n", $_SESSION['message']);

        // ファイルデータ添付
        // if (is_uploaded_file($_FILES["attach"]["tmp_name"])) {
        //     // 有効なファイルかどうかを検証し、問題なければ名前を変更しアップロード完了
        //     if (move_uploaded_file($_FILES["attach"]["tmp_name"], "files/" . $_FILES["attach"]["name"])) {
        //         chmod("files/" . $_FILES["attach"]["name"], 0644); // パーミッション設定
        if (isset($_FILES['attach']['name']) && $_FILES['attach']['name'] != "") {
            $file = "attachment/" . basename($_FILES['attach']['name']);
            move_uploaded_file($_FILES['attach']['tmp_name'], $file);
            echo $_FILES["attach"]["name"] . "をアップロードしました。";
        } else {
            echo "ファイルをアップロードできません。";
            //     }
            // } else {
            //     echo "ファイルが選択されていません。";
        }

        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        // ヘッダ情報設定
        $headers = [
            'MIME-Version' => '1.0',
            'Content-Transfer-Encoding' => '7bit',
            // 'Content-Transfer-Encoding' => '7bit',
            // 'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Type' => 'multipart/mixed;boundary=\"__BOUNDARY__\"\n',
            'Return-Path' => 'admin@test.com',
            'From' => '管理者 <admin@test.com>',
            'Sender' => '管理者 <admin@test.com>',
            'Reply-To' => 'admin@test.com',
            'Organization' => 'LifeIsBeautiful',
            'X-Sender' => 'admin@test.com',
            'X-Mailer' => 'Postfix/2.10.1',
            'X-Priority' => '3',
        ];
        array_walk($headers, function ($_val, $_key) use (&$header_str) {
            $header_str .= sprintf("%s: %s \r\n", trim($_key), trim($_val));
        });
        // mb_send_mail( $send_to, $subject, $body, $header_str );
        mb_send_mail($_SESSION['email'], 'お問い合わせありがとうございます', $body, $header_str);
        // $mail_header    = "Content-Type: text/plain; charset=UTF-8\r\nfrom:".$mailaddress;
        // $headers = "MIME-Version: 1.0\r\n"
        // . "Content-Transfer-Encoding: 7bit\r\n"
        // . "Content-Type: text/plain; charset=ISO-2022-JP\r\n";

        // メール送信
        // mb_send_mail( 'to', 'subject', 'body', $headers );
        // mb_send_mail($_SESSION['email'], 'お問い合わせありがとうございます', $body, $mail_header);
        // mb_send_mail('info@inatoma.com', 'お問い合わせありがとうございます', $message);

        // CSVに出力
        $msg = array("お名前" => $_SESSION['name'], "ふりがな" => $_SESSION['yomi'], "メールアドレス" => $_SESSION['email'], "電話番号" => $_SESSION['number'], "住所" => $_SESSION['address'], "お問い合わせ内容" => $_SESSION['message']);

        $ShiftJIS = $msg;
        mb_convert_variables('SJIS-win', 'UTF-8', $ShiftJIS);
        // $ShiftJIS = mb_convert_encoding($ShiftJIS, "SJIS-win", "UTF-8");
        $csv = fopen('test.csv', 'a');
        fputcsv($csv, $ShiftJIS);
        fclose($csv);

        // 違う方法（1行目がCSVヘッダー、2行目からデータ）
        // $msg = [
        //     ["お名前", "ふりがな", "メールアドレス", "電話番号", "住所", "お問い合わせ内容"],
        //     [$_SESSION['name'], $_SESSION['yomi'], $_SESSION['email'], $_SESSION['number'], $_SESSION['address'], $_SESSION['message']]
        // ];

        // $outputs = '';
        // foreach ($ShiftJIS as $ShiftJISs) {
        //     foreach ($ShiftJISs as $recordValue) {
        //         $recordValue = mb_convert_encoding($recordValue, "SJIS-win", "UTF-8");
        //         $recordValue = "=\"$recordValue\"";
        //         $outputs .= $recordValue . ',';
        //     }
        //     $outputs = rtrim($outputs, ',') . "\n";
        // }

        // file_put_contents("test.csv", $outputs);

        // 違う方法
        // $csv = fopen('php://output', 'w');
        // // $csv = fopen('test.csv', 'a');
        // stream_filter_prepend($csv, 'convert.iconv.utf-8/cp932');
        // fputcsv($csv, $ShiftJIS);
        // // foreach ($rows as $row) {
        // //     fputcsv($csv, $row);
        // // }

        // if (isset($_FILES['attach']['name']) && $_FILES['attach']['name'] != "") {
        //     $file = "attachment/" . basename($_FILES['attach']['name']);
        //     move_uploaded_file($_FILES['attach']['tmp_name'], $file);
        // } else
        //     $file = "";

        // セッションの初期化
        $_SESSION = array();
        // session_destroy();
        $mode = 'send';
    }
} else {
    // 初回表示（GET）の時、セッションを初期化
    // $_SESSION = array();
    $_SESSION['name'] = "";
    $_SESSION['yomi'] = "";
    $_SESSION['email'] = "";
    $_SESSION['number'] = "";
    $_SESSION['address'] = "";
    $_SESSION['message'] = "";
    $_SESSION['attach'] = "";
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>お問い合わせフォーム</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <style>
        body {
            padding: 10px;
            max-width: 600px;
            margin: 0px auto;
        }

        div.button {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php if ($mode == 'input') { ?>
        <!-- 入力画面 -->
        <?php
        // エラーメッセージの表示
        if ($errmessage) {
            echo '<div class="alert alert-danger" role="alert">';
            // 配列内の文字列を連結、改行して表示
            echo implode('<br>', $errmessage);
            echo '</div>';
        }
        ?>
        <h3>お問い合わせフォーム</h3>
        <br>
        <form action="./contactform.php" method="post" enctype="multipart/form-data">
            <label for="name">お名前 <span>必須</spna></label>
            <input type="text" name="name" value="<?php echo $_SESSION['name'] ?>" class="form-control">
            <br>
            <label for="yomi">ふりがな <span>必須</spna></label>
            <input type="text" name="yomi" value="<?php echo $_SESSION['yomi'] ?>" class="form-control">
            <br>
            <label for="email">メールアドレス <span>必須</spna></label>
            <input type="text" name="email" value="<?php echo $_SESSION['email'] ?>" class="form-control">
            <br>
            <label for="number">電話番号</label>
            <input type="text" name="number" value="<?php echo $_SESSION['number'] ?>" class="form-control">
            <br>
            <label for="address">住所</label>
            <input type="text" name="address" value="<?php echo $_SESSION['address'] ?>" class="form-control">
            <br>
            <label for="message">お問い合わせ内容 <span>必須</spna></label>
            <br>
            <textarea cols="40" rows="8" name="message" class="form-control"><?php echo $_SESSION['message'] ?></textarea>
            <br>
            <label for="attach">添付ファイル </label>
            <input type="file" name="attach" value="<?php echo $_SESSION['attach'] ?>">
            <br>
            <div class="button">
                <input type="submit" name="check" value="入力内容の確認" class="btn btn-primary btn-lg" />
            </div>
        </form>
    <?php } else if ($mode == 'check') { ?>
        <!-- 確認画面 -->
        <h3>入力した内容を確認してください。</h3>
        <form action="./contactform.php" method="post" enctype="multipart/form-data">
            <!-- トークンも一緒に送る -->
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <label for="name">お名前 </label><?php echo $_SESSION['name'] ?>
            <br>
            <label for="yomi">ふりがな </label><?php echo $_SESSION['yomi'] ?>
            <br>
            <label for="email">メールアドレス </label><?php echo $_SESSION['email'] ?>
            <br>
            <label for="number">電話番号 </label><?php echo $_SESSION['number'] ?>
            <br>
            <label for="address">住所 </label><?php echo $_SESSION['address'] ?>
            <br>
            <label for="message">お問い合わせ内容 </label>
            <br>
            <?php echo nl2br($_SESSION['message']) ?>
            <br>
            <label for="attach">添付ファイル </label><?php echo $_SESSION['attach'] ?>
            <br>
            <input type="submit" name="back" value="修正する" class="btn btn-primary btn-lg" />
            <input type="submit" name="send" value="送信する" class="btn btn-primary btn-lg" />
        </form>
    <?php } else { ?>
        <!-- 完了画面 -->
        <h3>お問い合わせありがとうございます。</h3>
        <p>自動返信メールをお送りしましたのでご確認ください。</p>
        <br>
        <a href="contactform.php">
            <button type="button">戻る<?php $mode = 'input'; ?></button>
        </a>
    <?php } ?>
</body>

</html>