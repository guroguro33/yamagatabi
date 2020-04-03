<?php

// 共通変数・関数ファイルを読込み
require('function.php');


debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行メール送信ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証なし

//================================
// 画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
  debug('post送信があります。');
  debug('$_POST情報：'.print_r($_POST,true));

  // 変数にPOST情報を代入
  $email = $_POST['email'];

  // 未入力チェック
  validRequired($email, 'email');

  if(empty($err_msg)){
    debug('未入力チェックOK。');

    // emailの形式チェック
    validEmail($email, 'email');
    // emailの最大文字数チェック
    validMaxLen($email, 'email');

    if(empty($err_msg)){
      debug('バリデーションチェックOK。');

      // 例外処理
      try {
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        debug('取得した$result：'.print_r($result,true));

        // EmailがDBに登録されていた場合
        if($stmt && array_shift($result)){
          debug('クエリ成功。DB登録あり。');
          $_SESSION['msg_success'] = SUC03;

          // 認証キー生成
          $auth_key = makeRandKey();

          // メール送信
          $from = 'info@yamagatabi.com';
          $to = $email;
          $subject = '【パスワード再発行認証】｜やまがたび';
          $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost/yamagatabi/passRemindRecieve.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります。

認証キーを再発行されたい場合は下記ページより再度発行をお願い致します。
http://localhost/yamagatabi/passRemindSend.php

////////////////////////////////////////
やまがたびカスタマーセンター
URL  http://yamagatabi.com/
E-mail info@yamagatabi.com
////////////////////////////////////////
EOT;
          sendMail($from, $to ,$subject, $comment);

          // 認証に必要な情報をセッションへ保存
          $_SESSION['auth_key'] = $auth_key;
          $_SESSION['auth_email'] = $email;
          $_SESSION['auth_key_limit'] = time()+(60 * 30);
          debug('セッション変数の中身：'.print_r($_SESSION,true));

          header("Location:passRemindRecieve.php"); //認証キー入力ページへ
          exit();

        }else{
          debug('クエリに失敗したかDBに登録がないemailが入力されました。');
          $err_msg['common'] = MSG07;
        }

      } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}
debug('パスワード再発行メール送信画面処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'パスワード再発行メール送信';
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
          <h2 class="title">パスワード再発行</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <p>ご登録のメールアドレス宛にパスワード再発行用の
            URLと認証キーをお送りします。</p>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            <p>メールアドレス</p>
            <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('email'); ?>
          </div>
          <input type="submit" class="btn" value="送信する">
        </form>
      </div>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>