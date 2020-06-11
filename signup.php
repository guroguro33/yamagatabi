<?php

// 共通変数・関数ファイル読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　新規登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// post送信されていた場合
if(!empty($_POST)){
  debug('post送信があります。');

  // 変数にユーザー情報を代入
  $u_name = $_POST['user_name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  // 未入力チェック
  validRequired($u_name, 'u_name');
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  if(empty($err_msg)){

    // user_nameの最大文字数チェック
    validMaxLen($u_name, 'u_name');

    // emailの形式チェック
    validEmail($email, 'email');
    // emailの最大文字数チェック
    validMaxLen($email, 'email');
    // emailの重複チェック
    validEmailDup($email);

    // passの半角英数字チェック
    validHalf($pass, 'pass');
    // passの最大文字数チェック
    validMaxLen($pass, 'pass');
    // passの最小文字数チェック
    validMinLen($pass, 'pass');

    if(empty($err_msg)){

      // パスワードとパスワード再入力があっているかチェック
      validMatch($pass, $pass_re, 'pass_re');
      
      if(empty($err_msg)){
        debug('バリデーションチェックOK。');

        try {
          // DBへ接続
          $dbh = dbConnect();
          // sql文作成
          $sql = 'INSERT INTO users (user_name,email,pass,login_time,create_date) VALUES (:u_name, :email, :pass, :login_time, :create_date)';
          $data = array(':u_name' => $u_name, ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':login_time' => date("Y-m-d H:i:s"), 'create_date' => date("Y-m-d H:i:s"));
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);
  
          // クエリ成功の場合
          if($stmt){
            // ログイン有効期限（デフォルトを1時間とする）
            $sesLimit = 60 * 60;
            // 最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            // ユーザーIDを格納（最後に挿入したIDを取り出す）
            $_SESSION['user_id'] = $dbh->lastInsertId();
  
            debug('登録したセッション変数の中身：'.print_r($_SESSION,true));
            debug('ユーザー登録完了 <<<<<<<<<<<<<<<<<');
            header("Location:mypage.php");
            exit();
          }
        } catch (Exception $e){
          debug('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }

    }
  }
}
debug('ユーザー登録画面処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<')
?>
<?php
$siteTitle = '新規登録';
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

      <div class="form-container wrap">
        <form action="" method="post" class="form">
          <h2 class="title">ユーザーの新規登録</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['u_name'])) echo 'err'; ?>">
            <p class="require">ニックネーム</p>
            <input type="text" name="user_name" value="<?php if(!empty($_POST['user_name'])) echo $_POST['user_name']; ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('u_name'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            <p class="require">メールアドレス</p>
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('email'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
            <p class="require">パスワード<span style="font-size:12px">※英数字６文字以上</span></p>
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
            <p class="require">パスワード（再入力）</p>
            <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass_re'); ?>
          </div>
          <input type="submit" class="btn" value="登録する">
        </form>
      </div>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>

</body>
</html>