<?php
// 共通変数・関数ファイルの読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;
  debug('$_POSTの中身：'.print_r($_POST,true));

  // バリデーションチェック
  
  // emailの形式チェック
  validEmail($email, 'email');
  // emailの最大文字数チェック
  validMaxLen($email, 'email');
  
  // パスワードの半角英数字チェック
  validHalf($pass, 'pass');
  // パスワードの最大文字数チェック
  validMaxLen($pass, 'pass');
  // パスワードの最小文字数チェック
  validMinLen($pass, 'pass');
  
  // 未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  if(empty($err_msg)){
    debug('バリデーションチェックOK。');

    // 例外処理
    try{
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'SELECT pass,id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      // クエリ結果の値を取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身：'.print_r($result, true));

      // パスワード照合
      // password_verify — パスワードがハッシュにマッチするかどうかを調べる
      if(!empty($result) && password_verify($pass, array_shift($result))){
        debug('パスワードがマッチしました。');

        // ログイン有効期限（デフォルトを1時間とする）
        $sesLimit = 60 * 60;
        // 最終ログイン日時を現在日時に
        $_SESSION['login_date'] = time();

        // ログイン保持にチェックがある場合
        if($pass_save){
          debug('ログイン保持にチェックがあります。');
          // ログイン有効期限を30日にしてセット
          $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        }else{
          debug('ログイン保持にチェックがありません。');
          // 次回のログイン保持をしないので、ログイン有効期限を1時間にセット
          $_SESSION['login_limit'] = $sesLimit;
        }
        // ユーザーIDを格納
        $_SESSION['user_id'] = $result['id'];

        debug('セッション変数の中身：'.print_r($_SESSION,true));
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
      
      }else{
        debug('パスワードがアンマッチです。');
        $err_msg['common'] = MSG08;
      }

    } catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<')
?>
<?php
$siteTitle = 'ログイン';
require('head.php');
?>
<!-- body -->
<body>

  <!-- ヘッダー -->
  <?php
  require('header.php')
  ?>

    <!-- メインコンテンツ -->
    <main>

      <div class="form-container wrap">
        <form action="" method="post" class="form">
          <h2 class="title">ログイン</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            <p>メールアドレス</p>
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('email'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
            <p>パスワード</p>
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('pass'); ?>
          </div>
          <label class="pass_save">
            <input type="checkbox" name="pass_save">次回ログインを省略する
          </label>
          <input type="submit" class="btn" value="ログイン">
          <p class="passRemind">パスワードを忘れた方は<a href="passRemindSend.html">こちら</a>
          </p>
        </form>
      </div>

    </main>

  <!-- フッター -->
  <?php
  require('footer.php')
  ?>