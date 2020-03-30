<?php

// 共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// プロフィール編集画面処理
//================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));

  // 変数にプロフィール情報を代入
  $username = $_POST['username'];
  $tel = $_POST['tel'];
  $zip = (!empty($_POST['zip'])) ? $_POST['zip']: 0;
  $addr = $_POST['addr'];
  $email = $_POST['email'];

  // DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['user_name'] !== $username){
    // 名前の未入力チェック
    validRequired($username, 'username');
    // 名前の最大文字数チェック
    validMaxLen($username, 'username');
  }
  if($dbFormData['tel'] !== $tel){
    if(!empty($tel)){
      // tel形式チェック
      validTel($tel, 'tel');
    }
  }
  if($dbFormData['zip'] !== $zip){
    if(!empty($zip)){
      // 郵便番号形式チェック
      validZip($zip, 'zip');
    }
  }
  if($dbFormData['addr'] !== $addr){
    // 住所の最大文字数チェック
    validMaxLen($addr, 'addr');
  }
  if($dbFormData['email'] !== $email){
    // emailの未入力チェック
    validRequired($email, 'email');
    // emailの最大文字数チェック
    validMaxLen($email, 'email');
    // emailの形式チェック
    validEmail($email, 'email');
    
    if(empty($err_msg)){
      // emailの重複チェック
      validEmailDup($email);
    }
  }
  if(empty($err_msg)){
    debug('バリデーションOKです。');

    // 例外処理
    try {
      // DB接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users SET user_name = :u_name, tel = :tel, zip = :zip, addr = :addr, email = :email WHERE id = :id';
      $data = array(':u_name' => $username, ':tel' => $tel, ':zip' => $zip, ':addr' => $addr, ':email' => $email, ':id' => $dbFormData['id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_SESSION['msg_success'] = SUC01;
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
        exit();
      }

    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('ユーザー登録画面処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<')
?>
<?php
$siteTitle = 'プロフィール編集';
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
          <h2 class="title">プロフィール編集</h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
            <p class="require">ニックネーム</p>
            <input type="text" name="username" value="<?php echo getFormData('user_name'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('username'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
            <p>電話番号<span style="font-size:12px;margin-left:5px;">※ハイフン無しでご入力ください</span></p>
            <input type="text" name="tel" value="<?php echo getFormData('tel'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('tel'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['zip'])) echo 'err'; ?>">
            <p>郵便番号<span style="font-size:12px;margin-left:5px;">※ハイフン無しでご入力ください</span></p>
            <input type="text" name="zip" value="<?php echo (empty(getFormData('zip'))) ? '': getFormData('zip'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('zip'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['addr'])) echo 'err'; ?>">
            <p>住所</p>
            <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('addr'); ?>
          </div>
          <label  lass="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            <p class="require">メールアドレス</p>
            <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('email'); ?>
          </div>
          <input type="submit" class="btn" value="変更する">
        </form>
      </div>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>