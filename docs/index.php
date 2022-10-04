<?php

//=== エラー表示
ini_set("display_errors", "On");

//=== ログイン session が無い場合の リダイレクト処理
include dirname(__FILE__) . '/../login/lib/secure.php';

//=== 現在　日時 + 時刻
$objDateTime = new DateTime();
$now_date = $objDateTime->format('Y-m-d H:i:s');

//=== エラーメッセージ用
$err = null;
$err2 = null;

//=========
if (isset($_POST['f_1']) && isset($_POST['f_2']) && isset($_POST['f_3']) && isset($_POST['f_4'])) {

    if (!empty($_POST['f_1']) && !empty($_POST['f_2']) && !empty($_POST['f_3']) && !empty($_POST['f_4'])) {
        //=== OK

        define("DB_HOST", "192.168.254.17");
        define("DB_PORT", "1521");
        define("DB_USERNAME", "ZNATU");
        define("DB_PASSWORD", "ZNATU");
        define("DB_SID", "orcl.world");

        $conn = oci_connect(DB_USERNAME, DB_PASSWORD, DB_HOST . ":" . DB_PORT . "/" . DB_SID, 'AL32UTF8');

        $sql = "INSERT INTO 登録情報 (読込データ, 登録日時) VALUES (:t_data, TO_TIMESTAMP(:t_time))";
        $stid = oci_parse($conn, $sql);

        $t_data = "";

        if (isset($_POST['f_1'])) {
            $t_data = $_POST['f_1'];
        }

        if (isset($_POST['f_2'])) {
            $t_data = $_POST['f_2'];
        }

        if (isset($_POST['f_3'])) {
            $t_data = $_POST['f_3'];
        }

        if (isset($_POST['f_4'])) {
            $t_data = $_POST['f_4'];
        }

        oci_bind_by_name($stid, ":t_data", $t_data);
        oci_bind_by_name($stid, ":t_time", $now_date);

        //====== 実行
        $r = oci_execute($stid);
        if (!$r) {
            //=== NG
            $m = oci_error($stid);
        } else {
            //=== OK
            header('Location: ./send_ok.php');
        }
    } else {
        //=== NG
        $err2 = "値をセットして送信してください。";
        $err = "";
    }
} else {
    //=== NG
    $err = "値がセットされていません。";
}



?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JIM webQR demo</title>


    <link rel="stylesheet" href="./css/style.css" />
    <script src="./jsQR.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Ropa+Sans" rel="stylesheet">

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">

    <style>
        .flex {
            display: flex;
        }

        .form_content {
            width: 75%;
        }

        .text_box {
            width: 45%;
        }

        .btn_box {
            margin: 30px;
        }

        #test_btn {
            margin-right: 30px;
        }

        .input_text {
            width: 80%;

        }
    </style>

</head>

<body>


    <h1>JIM webQR demo</h1>

    <div id="loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
    <canvas id="canvas" hidden></canvas>

    <div id="output" hidden>
        <div id="outputMessage">No QR code detected.</div>
        <div hidden><b>Data:</b> <span id="outputData"></span></div>
    </div>

    <!-- ２次元バーコード取得結果  初期時 => 非表示, 結果取得時 => 表示 -->
    <div style="position: relative;left: 12%;top: 10px;">

        <form action="./index.php" method="POST">

            <div class="flex form_content">

                <p class="text_box">
                    <input type="text" name="f_1" value="" id="f_1" class="input_text">
                </p>

                <p class="text_box">
                    <input type="text" name="f_2" value="" id="f_2" class="input_text">
                </p>
            </div>

            <div class="flex form_content">
                <p class="text_box">
                    <input type="text" name="f_3" value="" id="f_3" class="input_text">
                </p>

                <p class="text_box">
                    <input type="text" name="f_4" value="" id="f_4" class="input_text">
                </p>
            </div>

            <div style="position: relative;left: 10%;">
                <button class="w-50 btn btn-primary" type="submit">データ送信</button>
            </div>
    </div>


    </form>

    </div> <!-- ２次元バーコード取得結果 END -->

    <p style="margin-top:10px;">
        <?php if (!is_null($err)) : ?>
            <?php print $err; ?>
        <?php endif; ?>
    </p>

    <p style="margin-top:10px;">
        <?php if (!is_null($err2)) : ?>
            <?php print '<div class="alert alert-danger">' . $err2 . '</div>'; ?>
        <?php endif; ?>
    </p>

    <div class="flex btn_box">
        <div>
            <button id="test_btn">
                QR読みとり
            </button>
        </div>

        <div>
            <button id="test_btn2">
                バーコード読みとり
            </button>
        </div>
    </div>

    <script>
        var video_Flg = true;

        var test_btn = document.getElementById('test_btn');

        var f_1 = document.getElementById('f_1');
        var f_2 = document.getElementById('f_2');
        var f_3 = document.getElementById('f_3');
        var f_4 = document.getElementById('f_4');



        test_btn.onclick = function() {

            var video = document.createElement("video");
            var canvasElement = document.getElementById("canvas");
            var canvas = canvasElement.getContext("2d");
            var loadingMessage = document.getElementById("loadingMessage");
            var outputContainer = document.getElementById("output");
            var outputMessage = document.getElementById("outputMessage");
            var outputData = document.getElementById("outputData");

            function drawLine(begin, end, color) {
                canvas.beginPath();
                canvas.moveTo(begin.x, begin.y);
                canvas.lineTo(end.x, end.y);
                canvas.lineWidth = 4;
                canvas.strokeStyle = color;
                canvas.stroke();
            }

            // ========= カメラ画像の取得
            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                }
            }).then(function(stream) {
                video.srcObject = stream;
                video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
                video.play();
                requestAnimationFrame(tick);
            });
            // ========= カメラ画像の取得 END

            function tick() {
                loadingMessage.innerText = "⌛ Loading video..."
                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    loadingMessage.hidden = true;
                    canvasElement.hidden = false;
                    outputContainer.hidden = false;

                    canvasElement.height = video.videoHeight;
                    canvasElement.width = video.videoWidth;

                    // ========= QRコードの検出・デコード
                    // カメラ画像をいったん Canvas に書きこみ、
                    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);

                    //  canvas.getImageData でそれを取得
                    var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);

                    // var code = jsQR...という部分でライブラリ内での処理に引き渡している
                    var code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert",
                    });
                    // ========= QRコードの検出・デコード END

                    if (code) {
                        drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                        drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                        drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                        drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
                        outputMessage.hidden = true;
                        outputData.parentElement.hidden = false;
                        outputData.innerText = code.data;

                        video.hidden = true;
                        canvasElement.hidden = true;
                        canvas.hidden = true;

                        var result_arr = code.data.split(",");
                        f_1.value = result_arr[0];
                        f_2.value = result_arr[1];
                        f_3.value = result_arr[2];
                        f_4.value = result_arr[3];

                        video.stop();

                    } else {
                        outputMessage.hidden = false;
                        outputData.parentElement.hidden = true;
                    }

                }
                requestAnimationFrame(tick);
            }

        };
    </script>

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>

</html>