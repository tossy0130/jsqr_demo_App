<?php

//===セッションが無い場合は、リダイレクトで返す
session_start();
if (!isset($_SESSION['u_name']) && !isset($_SESSION['password'])) {
    header('Location: /../jsqr_test/login/index.php');
}
