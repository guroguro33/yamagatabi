<?php
// 共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

// post送信があり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['spotId']) && isset($_SESSION['user_id']) && isLogin()){
  
  // POSTやSESSIONを変数に代入
  debug('POST送信があります。');
  debug('POSTの中身：'.print_r($_POST,true));
  $s_id = $_POST['spotId'];
  debug('スポットID：'.$s_id);
  $u_id = $_SESSION['user_id'];

  // お気に入りがあるか確認
  $result = isLike($u_id, $s_id);

  // 例外処理
  try {
    // DB接続
    $dbh = dbConnect();

    // お気に入りがある場合
    if($result){
      // 削除のSQL文作成
      $sql = 'DELETE FROM favorite WHERE spot_id = :s_id AND user_id = :u_id';
      $data = array(':s_id' => $s_id, ':u_id' => $u_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      debug('お気に入りから削除しました。');
    }else{
      // 挿入のSQL文作成
      $sql = 'INSERT INTO favorite (spot_id, user_id, create_date) VALUES (:s_id, :u_id, :date)';
      $data = array(':s_id' => $s_id, ':u_id' => $u_id, ':date' => date('Y-m-d H:i:s')); 
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      debug('お気に入りに追加しました。');
    }

  } catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
debug('Ajax処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<')
?>