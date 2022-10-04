<?php

// === エラー表示
ini_set("display_errors", "On");

// セッションハイジャック対策 セッションID を更新して変更
// session_regenerate_id(TRUE);

// === functions.php 読み込み

define("DB_HOST", "192.168.254.17");
define("DB_PORT", "1521");
define("DB_USERNAME", "ZNATU");
define("DB_PASSWORD", "ZNATU");
define("DB_SID", "orcl.world");


//================= フォーム処理 =================
$err = "";

if (isset($_POST['name']) && isset($_POST['password'])) {

    $conn = oci_connect(DB_USERNAME, DB_PASSWORD, DB_HOST . ":" . DB_PORT . "/" . DB_SID, 'AL32UTF8');

    if (!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    $stid = oci_parse($conn, "SELECT ＩＤ, ＰＷ FROM ユーザー情報 WHERE ＩＤ = :u_id");

    $u_id = "";
    if (isset($_POST['name'])) {
        $u_id = $_POST['name'];
    }

    //=== バインド
    oci_bind_by_name($stid, ":u_id", $u_id);

    oci_define_by_name($stid, 'ＩＤ', $user_id);
    oci_define_by_name($stid, 'ＰＷ', $pw);

    //====== 実行
    oci_execute($stid);

    $select_u_id = "";
    $select_pw = "";

    // === フォームに入力された、ユーザー番号から、「pw」 取得
    while (oci_fetch($stid)) {
        $select_u_id = $user_id;
        $select_pw = $pw;
    }


    //=== 数字 桁数 チェック  OK
    if (strlen($_POST['name']) == 3) {
    } else {
        // === NG
        $err = "アカウント番号を４桁でご入力してください。";
        //   header('Location: ./index.php');
    }

    //=== 数値チェック
    if (!is_numeric($_POST['name'])) {
        // === NG
        //    header('Location: ./index.php');
        $err = "数値で入力してください。";
    } else {
        // === ok

    }

    //====== ログイン　判定
    if (strcmp($_POST['name'], $select_u_id) == 0 && strcmp($_POST['password'], $select_pw) == 0) {
        //=== $_SESSION へ　格納
        session_start();
        $_SESSION['u_name'] = $select_u_id;
        $_SESSION['password'] = $select_pw;
        header('Location: ../docs/index.php');
    } else {
        $err = "ログイン情報が間違っています";
        //    header('Location: ./index.php');
    }
} else {
    // ====== POST 送信がされていなかった場合　エラー処理
    $err = null;
}


?>


<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WEb ブラウザ　QR テスト</title>

    <!-- Bootstrap core CSS -->

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

    <!-- Custom styles for this template -->
    <link href="./css/signin.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <main class="form-signin">
            <form method="POST" action="./index.php">
                <h1 class="h3 mb-3 fw-normal">ログインする</h1>
                <?php
                if (!is_null($err)) {
                    echo '<div class="alert alert-danger">' . $err . '</div>';
                }
                ?>
                <label class="visually-hidden">ユーザ名</label>
                <input type="number" maxlength="4" name="name" class="form-control" placeholder="ユーザ番号" required autofocus>
                <label class="visually-hidden">パスワード</label>
                <input type="password" name="password" class="form-control" placeholder="パスワード" required>
                <button class="w-100 btn btn-lg btn-primary" type="submit">ログインする</button>
                <p class="mt-5 mb-3 text-muted">&copy; 2022 QRテスト</p>
            </form>
        </main>
    </div> <!-- END container -->



    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

</body>

</html>