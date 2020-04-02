<?php
//================================
// ログ
//================================
// ログをとるか
ini_set('log_errors','on');
// ログの出力ファイルを指定
ini_set('error_log','php.log');

//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
function debug($str){
  global $debug_flg;
  if($debug_flg){
    error_log('デバッグ：'.$str);
  }
}

//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp/");
//ガベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
// セッションを使う
session_start();
// 現在のセッションIDを新しく生成したものを置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身:'.print_r($_SESSION,true));
  debug('現在日時タイムスタンンプ'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug('ログイン期限日時スタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}

//================================
// 定数
//================================
//メッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','256文字以内で入力してください');
define('MSG04','6文字以上で入力してください');
define('MSG05','半角英数字のみご利用いただけます');
define('MSG06','すでに登録済みのメールアドレスです');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください');
define('MSG08','メールアドレスまたはパスワードが違います');
define('MSG09','パスワード（再入力）が一致しません');
define('MSG10','電話番号の形式が違います');
define('MSG11','郵便番号の形式が違います');
define('MSG12','古いパスワードが違います');
define('MSG13','古いパスワードと同じです');
define('MSG14','文字で入力してください');
define('MSG15','認証キーが正しくありません');
define('MSG16','有効期限が切れています');
define('MSG17','正しくありません');

define('SUC01','プロフィールを変更しました');
define('SUC02','パスワードを変更しました');
define('SUC03','メールを送信しました');
define('SUC04','登録しました');

//================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
$err_msg = array();

//================================
// バリデーション関数
//================================
// バリデーション関数（未入力チェック）
function validRequired($str, $key){
  if(empty($str)){
    global $err_msg;
    $err_msg[$key] = MSG01; 
  }
}
// バリデーション関数（Email形式チェック）
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
// バリデーション関数（Email重複チェック）
function validEmailDup($email){
  global $err_msg;
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    // クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    debug('email重複チェック$resultの値：'.print_r($result,true));

    if(empty(array_shift($result))){
      debug('emailの重複はありません。');
    }else{
      debug('emailの重複あり。');
      $err_msg['email'] = MSG06;
    }

  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
// バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 256){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
// バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
// バリデーション関数（半角英数字チェック）
function validHalf($str, $key){
  if(!preg_match(("/^[0-9a-zA-Z]+$/"), $str)){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
// バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key){
  if($str1 !==  $str2){
    global $err_msg;
    $err_msg[$key] = MSG09;
  }
}
// バリデーション関数（電話番号チェック）
function validTel($str, $key){
  if($str !== ''){
    if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
      global $err_msg;
      $err_msg[$key] = MSG10;
    }
  }
}
// バリデーション関数（郵便番号チェック）
function validZip($str, $key){
  if(!preg_match("/^\d{7}$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}
// バリデーション関数（固定長チェック）
function validLength($str, $key, $len = 8){
  if(mb_strlen($str) !== $len){
    global $err_msg;
    $err_msg[$key] = $len.MSG14;
  }
}

// パスワードチェック
function validPass($str, $key){
  // 半角英数字チェック
  validHalf($str, $key);
  // 最大文字数チェック
  validMaxLen($str, $key);
  // 最小文字数チェック
  validMinLen($str, $key);
}
// セレクトボックスチェック
function validSelect($str, $key){
  if(!preg_match("/^[1-9]+$/",$str)){ //０は禁止
    global $err_msg;
    $err_msg[$key] = MSG17;
  }
}

// エラーメッセージ表示
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}

//================================
// データベース
//================================
// DB接続関数
function dbConnect(){
  $dsn = 'mysql:dbname=yamagatabi;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    // SQL実行失敗時には例外を投げる設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファモードクエリを使う
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成（DB接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}
// SQL実行関数
function queryPost($dbh, $sql, $data){
  // クエリー作成
  $stmt = $dbh->prepare($sql);
  // プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    debug('失敗したSQL：'.print_r($stmt,true));
    $err_msg['common'] = MSG07;
    return 0;
  }else{
    debug('クエリに成功しました。');
    return $stmt;
  }
}
// ユーザーデータ取得
function getUser($u_id){
  debug('ユーザー情報を取得します。');
  // 例外処理
  try {
    // DB接続
    $dbh = dbConnect();
    // sql文作成
    $sql ='SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id'=> $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      debug('クエリ成功');
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      debug('クエリ失敗');
      return false;
    }
    
  } catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
// スポット情報取得
function getSpot($u_id, $s_id){
  debug('スポット情報を取得します。');
  debug('ユーザーID：'.$u_id);
  debug('スポットID：'.$s_id);
  // 例外処理
  try {
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * from spot WHERE user_id = :u_id AND spot_id = :s_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id, ':s_id' => $s_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    debug('$stmtの値：'.print_r($stmt,true));

    // クエリ結果のデータを１レコード返却
    if($stmt){
      debug('データ取得OK。');
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
// カテゴリー情報取得
function getCategory(){
  debug('カテゴリ情報を取得します。');
  // 例外処理
  try {
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM category WHERE delete_flg = 0';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->geMessage());
  }
}

//================================
// メール送信
//================================
function sendMail($from, $to, $subject, $comment){
  if(!empty($to) && !empty($subject) && !empty($comment)){
    //文字化けしないように設定（お決まりパターン）
    mb_language("japanese"); //現在使っている言語を設定する
    mb_internal_encoding("UTF-8"); //内部の日本語をどうエンコーディングするかを設定

    // メール送信（結果はboolean)
    $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
    // 送信結果を判定
    if($result){
      debug('メールを送信しました。');
    }else{
      debug('【エラー発生】メールの送信に失敗しました。');
    }
  }else{
    debug('【エラー発生】メールの送信の情報不足です。');
  }
}

//================================
// その他
//================================
// サニタイズ
function sanitize($str){
  return htmlspecialchars($str, ENT_QUOTES);
}
// フォーム入力保持
function getFormData($str, $flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }

  global $dbFormData;
  // ユーザーデータがある場合
  if(!empty($dbFormData)){
    // フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      // POSTデータがある場合
      if(isset($method[$str])){ //金額や郵便番号などのフォームで数字や数値の０がはいっている場合もあるので、issetを使う
        return sanitize($method[$str]);
      }else{ //POSTがない場合はDBの情報を表示
        return sanitize($dbFormData[$str]);
      }
    }else{ //フォームのエラーがない場合
      // POSTにデータがあり、DBの情報と違う場合（他のフォームでひっかかっている状態）
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{ //そもそも変更していない
        return sanitize($dbFormData[$str]);
      }
    }
  }else{
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
// sessionを1回だけ取得できる
function getSessionFlash($key){
  if(!empty($_session[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
// 認証キー生成
function makeRandKey($length = 8){
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $str = '';
  for ($i = 0; $i < $length; $i++){
    $str .= $chars[mt_rand(0, 61)];
  }
  return $str;
}
// 画像処理
function uploadImg($file,$key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));

  if(isset($file['error']) && is_int($file['error'])){
    try {
      // バリデーション
      // $file['error']の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
      // 「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
      switch($file['error']){
        case UPLOAD_ERR_OK: // OK
          break;
        case UPLOAD_ERR_NO_FILE: //ファイル未選択
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズ超過
          throw new RuntimeException('ファイルサイズが大きすぎます');
        case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズ超過
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default: //その他の場合
          throw new RuntimeException('その他のエラーが発生しました');
      }
      // file['mine']の値はブラウザ側で偽装可能なので、MINEタイプを自前でチェックする
      // exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
      $type = @exif_imagetype($file['tmp_name']);
      if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG],true)){
        throw new RuntimeException('画像形式が未対応です');
      }

      // ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する
      // ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまうと同じファイル名がアップロードされる可能性があり。
      // DBにパスを保存した場合、どっちの画像のパスなのか判断つかなくなってしまう
      // image_type_to_extension関数はファイルの拡張子を取得するもの
      $path = 'upload/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      if(!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
      // 保存したファイルパスのパーミッション（権限）を変更する
      chmod($path, 0644);

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;

    } catch (RuntimeException $e) {
      error_log('エラー発生：'.$e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
} 


?>