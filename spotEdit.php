<?php

// 共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　スポット登録編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// GETデータを格納
$s_id = (!empty($_GET['s_id']))? $_GET['s_id'] : '';
// DBからスポットデータを取得
$dbFormData = (!empty($s_id))? getSpot($_SESSION['user_id'],$s_id) : '';
// 新規登録か編集画面か判別用フラグ
$edit_flg = (empty($dbFormData))? false : true;
// DBからカテゴリデータ取得
$dbCategoryData = getCategory();

debug('$_GETされたスポットID：'.$s_id);
debug('$dbFormData：'.print_r($dbFormData,true));
// debug('$dbCategoryData：'.print_r($dbCategoryData,true));

// パラメータ改ざんチェック
//================================
// GETパラメータはあるが、改ざんされている（URLをいじくった）場合、正しい商品データが取れないのでマイページへ遷移させる
if(!empty($s_id) && empty($dbFormData)){
  debug('GETパラメータの商品IDが違います。マイページへ遷移します。');
  header("Location:mypage.php");
}

// POST送信時処理
//================================
if(!empty($_POST)){
  debug('post送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  // 変数にスポット情報を代入
  $s_name = $_POST['spot_name'];
  $addr = $_POST['addr'];
  $tel = $_POST['tel'];
  $cate_id = $_POST['cate_id'];
  $comment = $_POST['comment'];
  $pic1 = (!empty($_FILES['pic1']['name']))? uploadImg($_FILES['pic1'],'pic1') : '';
  // 画像をPOSTしてない（登録してない）が既にDBに登録されている場合、DBのパスを入れる（POSTに反映されないので）
  $pic1 = (empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;
  $pic2 = (!empty($_FILES['pic2']['name']))? uploadImg($_FILES['pic2'],'pic2') : '';
  $pic2 = (empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic2;
  $pic3 = (!empty($_FILES['pic3']['name']))? uploadImg($_FILES['pic3'],'pic3') : '';
  $pic3 = (empty($pic3) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic3;

  // 更新の場合はDB情報と入力情報が違う場合にバリデーションを行う
  if(empty($dbFormData)){
    // 名前の未入力チェック
    validRequired($s_name, 'spot_name');
    // 名前の最大文字数チェック
    validMaxLen($s_name, 'spot_name');
    // 住所の未入力チェック
    validRequired($addr, 'addr');
    // 住所の最大文字数チェック
    validMaxLen($addr, 'addr');
    // 電話番号の形式チェック
    validTel($tel, 'tel');
    // セレクトボックスチェック
    validSelect($cate_id, 'cate_id');
    // 説明の最大文字数チェック
    validMaxLen($comment, 'comment', 500);
  }else{
    if($dbFormData['spot_name'] !== $s_name){
      // 名前の未入力チェック
      validRequired($s_name, 'spot_name');
      // 名前の最大文字数チェック
      validMaxLen($s_name, 'spot_name');
    }
    if($dbFormData['addr'] !== $addr){
      // 住所の未入力チェック
      validRequired($addr, 'addr');
      // 住所の最大文字数チェック
      validMaxLen($addr, 'addr');
    }
    if($dbFormData['tel'] !== $tel){
      // 電話番号の形式チェック
      validTel($tel, 'tel');
    }
    if($dbFormData['cate_id'] !== $cate_id){
      // セレクトボックスチェック
      validSelect($cate_id, 'cate_id');
    }
    if($dbFormData['comment'] !== $comment){
      // 説明の最大文字数チェック
      validMaxLen($comment, 'comment', 500);
    }
  }

  if(empty($err_msg)){
    debug('バリデーションチェックOKです。');

    // 例外処理
    try {
      // DB接続
      $dbh = dbConnect();
      // SQL文作成
      if($edit_flg){
        debug('DB更新です');
        $sql = 'UPDATE spot SET spot_name = :s_name, addr = :addr, tel = :tel, cate_id = :cate_id, comment = :comment, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3 WHERE spot_id = :s_id AND user_id = :u_id AND delete_flg = 0';
        $data = array(':s_name' => $s_name, ':addr' => $addr, ':tel' => $tel, ':cate_id' => $cate_id, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':s_id' => $s_id, ':u_id' => $_SESSION['user_id']);
      }else{
        debug('DB新規登録です');
        $sql = 'INSERT INTO spot (spot_name, addr, tel, cate_id, comment, user_id, pic1, pic2, pic3, create_date) VALUES (:s_name, :addr, :tel, :cate_id, :comment, :u_id, :pic1, :pic2, :pic3, :date)';
        $data = array(':s_name' => $s_name, ':addr' => $addr, ':tel' => $tel, ':cate_id' => $cate_id, ':comment' => $comment, 'u_id' => $_SESSION['user_id'], ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':date' => date("Y-m-d H:i:s"));
      }
      debug('SQL:'.$sql);
      debug('流し込みデータ：'.print_r($data,true));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = MSG04;
        debug('マイページへ遷移します');
        header("Location:mypage.php");
      }

    } catch (Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('スポット登録編集画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = ($edit_flg)? 'スポット編集' : 'スポット新規登録';
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
        <form action="" method="post" class="form spot-form" enctype="multipart/form-data">
          <h2 class="title"><?php echo ($edit_flg)? 'スポット編集' : 'スポット新規登録';?></h2>
          <div class="area-msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['spot_name'])) echo 'err'; ?>">
            <p class="require">名称</p>
            <input type="text" name="spot_name" value="<?php echo getFormData('spot_name'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('spot_name'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['addr'])) echo 'err'; ?>">
            <p class="require">住所</p>
            <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('addr'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
            <p>電話番号</p>
            <input type="text" name="tel" value="<?php echo (empty(getFormData('tel')))? '' : getFormData('tel'); ?>">
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('tel'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['cate_id'])) echo 'err'; ?>">
            <p class="require">カテゴリー</p>
            <select name="cate_id">
              <option value="0" <?php if(getFormData('cate_id') === 0) echo 'selected'; ?>>選択してください</option>

              <?php
                foreach($dbCategoryData as $key => $val):
              ?>

              <option value="<?php echo $val['cate_id']; ?>" <?php if(getFormData('cate_id') == $val['cate_id']) echo 'selected'; ?>><?php echo $val['category_name'];?></option>

              <?php endforeach; ?>

            </select>
          </label>
          <div class="area-msg">
            <?php echo getErrMsg('cate_id'); ?>
          </div>
          <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
            <p class="">説明</p>
            <textarea name="comment" rows="8" cols="30" id="js-count"><?php echo getFormData('comment');?></textarea>
          </label>
          <p class="counter-text" style="text-align:right;"><span id="js-count-view">0</span>/500文字</p>
          <div class="area-msg">
            <?php echo getErrMsg('comment'); ?>
          </div>
          <div class="area-pic">
            <div class="imgDrop-container">
              画像1
              <label class="area-drop" class="<?php if(!empty($err_msg['pic1'])) echo 'err'; ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic1" class="input-file">
                <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;' ?>">
                ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php echo getErrMsg('pic1'); ?>
              </div>
            </div>
            <div class="imgDrop-container">
              画像2
              <label class="area-drop" class="<?php if(!empty($err_msg['pic2'])) echo 'err'; ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic2" class="input-file">
                <img src="<?php echo getFormData('pic2');?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;' ?>">
                ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php echo getErrMsg('pic2'); ?>
              </div>
            </div>
            <div class="imgDrop-container">
              画像3
              <label class="area-drop" class="<?php if(!empty($err_msg['pic3'])) echo 'err';?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic3" class="input-file">
                <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;'?>">
                ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php echo getErrMsg('pic3'); ?>
              </div>
            </div>
          </div>
          <input type="submit" class="btn" value="<?php echo ($edit_flg)? '更新する' : '登録する'; ?>">
        </form>
      </div>

    </main>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>