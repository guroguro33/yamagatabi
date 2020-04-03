<?php
// 共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　スポット詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//==================================
// 画面処理
//==================================

// 画面表示用データ取得
//==================================
// スポットIDのGETパラメータを取得
$s_id = (!empty($_GET['s_id']))? $_GET['s_id'] : '';
// DBからスポットデータを取得
$spotData = getSpotOne($s_id);
// DBから口コミデータを取得
$reviewData = getMsg($s_id);
// パラメータに不正な値が入っていないかチェック
if(empty($spotData)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:Index.php"); // トップページへ
}
debug('取得したスポット情報$spotData:'.print_r($spotData,true));
debug('取得した口コミ情報：'.print_r($reviewData,true));

// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');

  // ログイン認証
  require('auth.php');

  // バリデーションチェック
  $msg = (isset($_POST['msg']))? $_POST['msg'] : '';
  // 最大文字数チェック
  validMaxLen($msg, 'msg', 500);
  // 未入力チェック
  validRequired($msg, 'msg');

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    // 例外処理
    try {
      // DB接続
      $dbh = dbConnect();
      // SQL文作成
      $sql1 = 'INSERT INTO review (send_date, user_id, msg, spot_id, create_date) VALUES (:send_date, :user_id, :msg, :spot_id, :date)';
      $sql2 ='UPDATE spot SET view_count = view_count + 1 WHERE spot_id = :s_id';
      $data1 = array(':send_date' => date('Y-m-d H:i:s'), ':user_id' => $_SESSION['user_id'], ':msg' => $msg, ':spot_id' => $s_id, ':date' => date('Y-m-d H:i:s'));
      $data2 = array(':s_id' => $s_id);
      // クエリ実行
      $stmt1 = queryPost($dbh, $sql1, $data1);
      $stmt2 = queryPost($dbh, $sql2, $data2);
  
      // クエリ成功の場合
      if($stmt1 && $stmt2){
        $_POST = array(); //postをクリア
        debug('口コミ追加のため、再読み込みします。');
        header("Location:".$_SERVER['PHP_SELF'].'?s_id='.$s_id); //自分自身に遷移
      }

    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('スポット詳細画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'スポット詳細';
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
      <section class="top-img">
        <h1><span>山形</span>で<span>旅</span>しよう。</h1>
      </section>
      <div class="prev wrap">
        <a href="<?php echo (basename($_SERVER["HTTP_REFERER"]) == 'mypage.php')? 'mypage.php' : 'index.php'.appendGetParam(array('s_id')); ?>" class="prev-item">&lt;　一覧にもどる</a>
      </div>
      <div class="wrap spot-content">
        <section class="content">
          <div class="spot-name">
            <p class="category"><?php echo sanitize($spotData['category_name']); ?></p>
            <h2 class="name"><?php echo sanitize($spotData['spot_name']); ?></h2>
            <i class="fa fa-heart icn-favorite js-click-like <?php if(isLike($_SESSION['user_id'], $spotData['spot_id'])){echo 'active';} ?>" aria-hidden="true" data-spotid="<?php echo sanitize($spotData['spot_id']); ?>"></i>
          </div>
          <div class="picture">
            <div class="pic-wrap">
              <img src="<?php echo showImg(sanitize($spotData['pic1'])); ?>" alt="画像1:<?php echo sanitize($spotData['spot_name']);?>" class="pic1">
            </div>
            <div class="pic-wrap">
              <img src="<?php echo showImg(sanitize($spotData['pic2'])); ?>" alt="画像2:<?php echo sanitize($spotData['spot_name']);?>" class="pic2">
            </div>
            <div class="pic-wrap">
              <img src="<?php echo showImg(sanitize($spotData['pic3'])); ?>" alt="画像3:<?php echo sanitize($spotData['spot_name']);?>" class="pic3">
            </div>
          </div>
          <address>
            <div class="addr addr-container">
              <p class="addr-item">住所</p>
              <p><?php echo sanitize($spotData['addr']);?></p>
            </div>
            <div class="tel addr-container">
              <p class="addr-item">電話番号</p>
              <p><?php echo (empty($spotData['tel']))? '未登録' : sanitize('0'.$spotData['tel']); ?></p>
            </div>
            <div class="comment addr-container">
              <p class="addr-item">説明</p>
              <p class="comment-txt"><?php echo sanitize($spotData['comment']); ?></p>
            </div>
          </address>
          <p class="review-count">口コミ数：<?php echo sanitize($spotData['view_count']);?>件</p>
          <?php foreach($reviewData as $key => $val): ?>
            <div class="review">
              <p class="review-name"><?php echo sanitize($val['user_name']);?><br><?php echo date('Y/m/d', strtotime(sanitize($val['send_date']))); ?></p>
              <p class="review-txt">
                <span class="triangle"></span>
                <?php echo sanitize($val['msg']);?>
              </p>
            </div>
          <?php endforeach; ?>
          <div class="review-post">
            <p>口コミ投稿</p>
            <form action="" method="post">
              <textarea name="msg" id="review" cols="20" rows="4"></textarea>
              <div class="review-area-msg">
                <?php echo getErrMsg('msg'); ?>
              </div>
              <input type="submit" class="btn" value="投稿する">
            </form>
          </div>
        </section>

      </div>


    </main>

    <!-- フッター -->
    <?php
      require('footer.php');
    ?>