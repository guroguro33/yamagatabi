<?php

// 共通変数・関数ファイル読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログアウトページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログアウト処理
debug('ログアウトします。');
// セッション削除（ログアウトする）
session_destroy();
debug('session_destroy後のセッションID:'.session_id());
debug('session_destroy後の$_SESSIONの値：'.print_r($_SESSION,true));
debug('ログインページへ遷移します。');
header("Location:login.php");
exit();


?>