<?php

// 共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行メール送信ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証なし

// SESSIONに認証キーがあるか確認、なければリダイレクト
if(empty($_SESSION['auth_key'])){
  header("Location:passRemindSend.php"); //認証キー送信ページへ
}

//================================
// 画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
  debug('post送信があります。');
  debug('$_POST情報：'.print_r($_POST,true));

  // 変数にPOST情報を代入
  $auth_key = $_POST['token'];

  // 未入力チェック
  validRequired($auth_key, 'token');
  
  if(empty($err_msg)){
    debug('未入力チェックOK。');

    // 固定長チェック
    validLength($auth_key, 'token');
    // 半角英数字チェック
    validHalf($auth_key, 'token');

    if(empty($err_msg)){
      debug('バリデーションOK。');

      // 認証キーのチェック
      if($auth_key !== $_SESSION['auth_key']){
        $err_msg['common'] = MSG15;
      }
      // 有効期限のチェック
      if(time() > $_SESSION['auth_key_limit']){
        $err_msg['common'] = MSG16;
      }

      if(empty($err_msg)){
        debug('認証OK。');

        $pass = makeRandKey(); //パスワード生成

        // 例外処理
        try {
          // DB接続
          $dbh = dbConnect();
          // SQL文作成
          $sql = 'UPDATE users SET pass = :pass WHERE email = :email AND delete_flg = 0';
          $data = array(':pass' => password_hash($pass, PASSWORD_DEFAULT), ':email' => $_SESSION['auth_email']);
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);
          
          if($stmt){
            debug('パスワードを変更しました。');

            // メール送信
            $from = 'info@yamagatabi@gmail.com';
            $to = $_SESSION['auth_email'];
            $subject = '【パスワード再発行通知】｜yamagatabi';
            $comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行のパスワードをご入力頂き、ログインください。

ログインページ：http://localhost/yamagatabi/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願い致します

////////////////////////////////////////
ウェブカツマーケットカスタマーセンター
URL  http://webukatu.com/
E-mail info@webukatu.com
////////////////////////////////////////
EOT;
            sendMail($from, $to, $subject, $comment);

            // セッション削除
            $_SESSION = array();
            $_SESSION['msg_success'] = SUC03;
            debug('セッション削除後のセッション変数の中身：'.print_r($_SESSION,true));
            
            header("Location:login.php"); //ログインページへ
          }else{
            debug('クエリに失敗しました。');
            $err_msg['common'] = MSG07;
          }

        } catch (Exception $e){
          error_log('エラー発生：'.getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}
debug('パスワード再発行認証画面処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'パスワード再発行認証';
require('head.php');
?>
<!-- body -->
<body>
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <p id="js-show-msg" class="msg-slide" style="display:none;">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!-- メインコンテンツ -->
    <main>

      <div class="form-container wrap">
        <form action="" method="post" class="form">
          <h2 class="title">パスワード再発行認証</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <p>ご登録のメールアドレス宛に送信したメール内にある
            認証キーを入力し、パスワードを再発行してください。</p>
          <label class="<?php if(!empty($err_msg)) echo 'err'; ?>">
            <p>認証キー</p>
            <input type="text" name="token" value="<?php echo getFormData('token'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('token'); ?>
          </div>
          <input type="submit" class="btn" value="再発行する">
        </form>
      </div>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>