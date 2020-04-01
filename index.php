<?php
$siteTitle = 'やまがたび';
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
        <h1><span>山形</span>で<span>旅</span>する</h1>
      </section>
      <div class="wrap main-content">
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

        <!-- サイドバー -->
        <section class="sidebar">
          <div class="count">
            <p class="count-total">10件のスポットがあります</p>
            <p class="count-page"> 1 - 6件 / 10件中</p>
          </div>
          <div class="sort">
            <form action="" method="GET">
              <label class="category-sort">
                <p>カテゴリー</p>
                <select name="category" id="">
                  <option value="">選択してください</option>
                  <option value="nature">自然</option>
                  <option value="culture">文化</option>
                  <option value="spa">温泉</option>
                  <option value="food">食べ物</option>
                </select>
              </label>
              <label class="review-sort">
                <p>口コミ</p>
                <select name="review" id="">
                  <option value="">選択してください</option>
                  <option value="desc">多い順</option>
                  <option value="asc">少ない順</option>
                </select>
              </label>
              <input type="submit" value="検索">
            </form>
          </div>    
        </section>
      </div>

      <!-- ページネーション -->
      <div class="pagination">
        <ul class="pagination-list">
          <li class="list-item"><a href="">&lt;</a></li>
          <li class="list-item"><a href="">1</a></li>
          <li class="list-item"><a href="">2</a></li>
          <li class="list-item active"><a href="">3</a></li>
          <li class="list-item"><a href="">4</a></li>
          <li class="list-item"><a href="">5</a></li>
          <li class="list-item"><a href="">&gt;</a></li>
        </ul>
      </div>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>