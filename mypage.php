<?php

// 共通変数・関数ファイルの読込み
require('function.php');



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
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
        </section>
      </div>

      <div class="wrap mypage-content">
        <h4>お気に入り</h4>
        <section class="content">
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
          <a href="" class="card">
            <div class="spot-img">
              <img src="./img/yamadera.jpg" alt="山寺">
            </div>
            <div class="desc">
              <p class="category">景勝地</p>
              <p class="spot-name">山寺</p>
              <p class="review-count">口コミ数：３件</p>
            </div>
          </a>
        </section>
      </div>
    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>