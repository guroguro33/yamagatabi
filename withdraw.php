<?php

// 共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 退会画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');

  // 例外処理
  try {
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
    $sql2 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = :u_id';
    // データ流し込み
    $data = array(':u_id' => $_SESSION['user_id']);
    // クエリ実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);

    // クエリ実行成功の場合（userテーブルのみ削除成功していれば良しとする）
    if($stmt1){
      // debug('クエリ成功:アカウントを論理削除しました。');
      
      // セッションを削除
      session_destroy();
      debug('退会成功後のセッション変数の中身：'.print_r($_SESSION,true));
      debug('トップページへ遷移します。');
      header("Location:index.php"); //トップページへ
      exit();
    }else{
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG07;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'. $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('退会画面処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = "退会";
require('head.php');
?>
<!-- body -->
<body>
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <main>

      <!-- マイページメニュー -->
      <section class="top-img">
        <h3>マイページメニュー</h3>
        <div class="mypage-menu">
          <ul class="menu-list">
            <li class="list-item"><a href="spotEdit.php">おすすめスポット投稿</a></li>
            <li class="list-item"><a href="profEdit.php">プロフィール編集</a></li>
            <li class="list-item"><a href="passEdit.php">パスワード変更</a></li>
            <li class="list-item"><a href="withdraw.php">退会</a></li>
          </ul>
        </div>
      </section>

      <div class="form-container wrap">
        <form action="" method="post" class="form">
          <h2 class="title">退会</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <p class="withdraw-msg">本当に退会しますか？</p>
          <input type="submit" class="btn" value="退会する" name="submit">
        </form>
      </div>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>
