<?php
// 共通変数・関数ファイルの読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//==================================
// 画面処理
//==================================
// ログイン認証
require('auth.php');

// 画面表示用データ取得
// =================================
// ユーザーID
$u_id = $_SESSION['user_id'];
// DBから自分が投稿したスポット情報を取得
$spotData = getMySpots($u_id);
// DBから自分のお気に入りスポットを取得
$favoriteData = getMyFavorite($u_id);
debug('$favoriteData:'.print_r($favoriteData,true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = "マイページ";
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

      <div class="wrap mypage-content">
        <h4>投稿したおすすめスポット</h4>
        <section class="content">
          <?php foreach($spotData as $key => $val):  ?>
            <a href="spotDetail.php<?php echo '?s_id='.$val['spot_id']; ?>" class="card">
              <div class="spot-img">
                <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="<?php echo sanitize($val['spot_name']);?>">
              </div>
              <div class="desc">
                <p class="category"><?php echo sanitize($val['category_name']); ?></p>
                <p class="spot-name"><?php echo sanitize($val['spot_name']); ?></p>
                <p class="review-count">口コミ数：<?php echo sanitize($val['view_count']); ?>件</p>
              </div>
            </a>
          <?php endforeach; ?>
        </section>
      </div>

      <div class="wrap mypage-content">
        <h4>お気に入り</h4>
        <section class="content">
          <?php foreach($favoriteData as $key => $val):  ?>
            <a href="spotDetail.php<?php echo '?s_id='.$val['spot_id']; ?>" class="card">
              <div class="spot-img">
                <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="<?php echo sanitize($val['spot_name']); ?>">
              </div>
              <div class="desc">
                <p class="category"><?php echo sanitize($val['category_name']); ?></p>
                <p class="spot-name"><?php echo sanitize($val['spot_name']); ?></p>
                <p class="review-count">口コミ数：<?php echo sanitize($val['view_count']); ?>件</p>
              </div>
            </a>
          <?php endforeach; ?>
        </section>
      </div>
    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>