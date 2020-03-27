<?php

//================================
// ログイン認証・自動ログアウト
//================================
// ログインしたことがある場合
if(!empty($_SESSION['login_date'])){
  debug('ログイン済みユーザーです。');

  // 現在日時が最終ログイン日時＋有効期限を超えていた場合
  if(time() > $_SESSION['login_date'] + $_SESSION['login_limit']){
    debug('ログイン有効期限オーバーです。');

    // セッションを破棄
    session_destroy();
    // ログインページへ遷移
    header("Location:login.php");
    exit();

  }else{
    debug('ログイン有効期限以内です。');
    // 最終ログイン日時を現在時刻に更新
    $_SESSION['login_date'] = date();

    // loginページだった場合はマイページへ遷移する
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      debug('マイぺージへ遷移します。');

      header("Location:mypage.php");
      exit();
    } 
  }

}else{
  debug('未ログインユーザーです。');
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    header("Location:login.php");
    exit();
  }
}

?>