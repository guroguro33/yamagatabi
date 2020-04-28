<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//==================================
// 画面処理
//==================================

// 画面表示用データ取得
//==================================
// GETパラメータを取得
// $_GET['p']のpの値はページネーションのaタグないで送信している
// カレントページ
$currentPageNum = (!empty($_GET['p']))? $_GET['p'] : 1; //デフォルトは１ページ目
// カテゴリー
$category = (!empty($_GET['cate_id']))? $_GET['cate_id'] : '';
// ソート順
$sort = (!empty($_GET['sort']))? $_GET['sort'] : '';

// パラメータに不正な値が入っているかチェック
// フォームの値である$_GET['p']は文字列のためis_intはfalseになる
debug('$currentPageNumの値：'.print_r($currentPageNum,true));
if(!is_int((int)$currentPageNum)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}

// 表示件数
$listSpan = 6;

// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum - 1) * 6);

// DBから商品データを取得
$dbSpotData = getSpotList($currentMinNum, $category, $sort);
debug('取得した商品データ：'.print_r($dbSpotData,true));

// DBからカテゴリーデータを取得
$dbCategoryData = getCategory();

debug('トップページ画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
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
    <main class="index">
      <section class="top-img">
        <h1><span>山形</span>で<span>旅</span>しよう。</h1>
      </section>
      <div class="wrap main-content">
        <section class="content">
          <?php foreach($dbSpotData['data'] as $key => $val): ?>
            <!-- ページ戻り用に&p=?を追加 -->
            <a href="spotDetail.php<?php echo (!empty(appendGetParam()))? appendGetParam().'&s_id='.$val['spot_id'].'&p='.$currentPageNum : '?s_id='.$val['spot_id'].'&p='.$currentPageNum;?>" class="card">
              <div class="spot-img">
                <img src="<?php echo sanitize($val['pic1']);?>" alt="<?php echo sanitize($val['spot_name']);?>">
              </div>
              <div class="desc">
                <p class="category"><?php echo sanitize($val['category_name']);?></p>
                <p class="spot-name"><?php echo sanitize($val['spot_name']);?></p>
                <p class="review-count">口コミ数：<?php echo sanitize($val['view_count']);?>件</p>
              </div>
            </a>
          <?php endforeach; ?>
        </section>

        <!-- サイドバー -->
        <section class="sidebar">
          <div class="count">
            <p class="count-total"><?php echo sanitize($dbSpotData['total']);?>件のスポットがあります</p>
            <p class="count-page"> <?php echo (!empty($dbSpotData['data']))? $currentMinNum+1 : 0;?> - <?php echo $currentMinNum+count($dbSpotData['data']);?>件 / <?php echo sanitize($dbSpotData['total']); ?>件中</p>
          </div>
          <div class="sort">
            <form action="" method="GET">
              <label class="category-sort">
                <p>カテゴリー</p>
                <select name="cate_id" id="">
                  <option value="0" <?php if(getFormData('cate_id', true) == 0){ echo 'selected';} ?>>選択してください</option>
                  <?php foreach($dbCategoryData as $key => $val): ?>
                    <option value="<?php echo $val['cate_id'];?>" <?php if(getFormData('cate_id',true) == $val['cate_id']) echo 'selected'; ?>><?php echo $val['category_name']; ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <label class="review-sort">
                <p>口コミ順</p>
                <select name="sort" id="">
                  <option value="0" <?php if(getFormData('sort', true) == 0) echo 'selected';?>>選択してください</option>
                  <option value="1" <?php if(getFormData('sort', true) == 1) echo 'selected';?>>口コミが多い順</option>
                  <option value="2" <?php if(getFormData('sort', true) == 2) echo 'selected';?>>口コミが少ない順</option>
                </select>
              </label>
              <input type="submit" value="検索">
            </form>
          </div>    
        </section>
      </div>

      <!-- ページネーション -->
      <?php
        pagination($currentPageNum, $dbSpotData['total_page'], '&cate_id='.$category.'&sort='.$sort);
      ?>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>