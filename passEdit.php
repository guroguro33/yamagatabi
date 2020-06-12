<?php

// 共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード変更ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($userData,true));

// post送信されていた場合
if(!empty($_POST)){
  debug('post送信があります。');
  debug('post情報：'.print_r($_POST,true));

  // 変数にユーザー情報を代入
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  // 未入力チェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  if(empty($err_msg)){
    debug('未入力チェックOK。');

    // 古いパスワードのチェック
    validPass($pass_old, 'pass_old');
    // 新しいパスワードのチェック
    validPass($pass_new, 'pass_new');

    // 古いパスワードとDBのパスワードを照合（DBに入っているデータと同じであれば、半角英数字チェックや最大文字チェックは行わなくても問題ない）
    if(!password_verify($pass_old,$userData['pass'])){
      $err_msg['pass_old'] = MSG12;
    }

    // 新しいパスワードと古いパスワードが同じかチェック
    if($pass_new === $pass_old){
      $err_msg['pass_new'] = MSG13;
    }

    // 新しいパスワードと再入力が一致するかチェック
    validMatch($pass_new, $pass_new_re, 'pass_new_re');
    
    if(empty($err_msg)){
      debug('バリデーションチェックOK。');

      // 例外処理
      try {
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'UPDATE users SET pass = :pass WHERE id = :u_id AND delete_flg = 0';
        $data = array(':pass' => password_hash($pass_new, PASSWORD_DEFAULT), ':u_id' => $userData['id']);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // クエリ成功の場合
        if($stmt){
          $_SESSION['msg_success'] = SUC02;

          // メールを送信
          $username = $userData['user_name'];
          $from = 'info@yamagatabi.com';
          $to = $userData['email'];
          $subject = 'パスワード変更通知｜やまがたび';
          $comment = <<<EOT
{$username}　さん
パスワードが変更されました。

////////////////////////////////////////
やまがたびカスタマーセンター
URL  https://yamagatabi.com/
E-mail info@yamagatabi.com
////////////////////////////////////////
EOT;
          sendMail($from, $to, $subject, $comment);

          header("Location:mypage.php");
        }

      } catch (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
debug('パスワード変更画面処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'パスワード変更';
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
          <h2 class="title">パスワード変更</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
            <p>古いパスワード</p>
            <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass_old');  ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
            <p>新しいパスワード<br><span style="font-size: 12px;">※英数字６文字以上</span></p>
            <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass_new');  ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
            <p>新しいパスワード（再入力）</p>
            <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass_new_re');  ?>
          </div>
          <input type="submit" class="btn" value="変更する">
        </form>
      </div>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>