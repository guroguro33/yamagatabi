jQuery(document).ready(function(){
  //フッターの位置調整
  var $ftr = $('#footer');
  if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
    $ftr.attr({'style':'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;'});
  } 

  //メッセージ表示
  var $jsShowMsg = $('#js-show-msg');
  var msg = $jsShowMsg.text();
  if(msg.replace(/^[\s　]+[\s　]+$/g,"").length){
    $jsShowMsg.fadeToggle('slow');
    setTimeout(function(){ $jsShowMsg.fadeToggle('slow'); }, 5000);
  }

  //画像ライブプレビュー
  var $dropArea = $('.area-drop');
  var $fileInput = $('.input-file');
  $dropArea.on('dragover', function(e){
    e.stopPropagation(); //親要素(.area-drop)の親への伝播を止める
    e.preventDefault(); //リンク遷移などのイベント無効
    $(this).css('border', '1px dashed #707070');
  });
  $dropArea.on('dragleave', function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '0.5px solid #707070');
  });
  $fileInput.on('change', function(e){
    $dropArea.css('border', 'none');
    var file = this.files[0], //files配列にファイルが入っている
        $img = $(this).siblings('.prev-img'), //siblingsメソッドで兄弟要素のimgを取得
        fileReader = new FileReader(); //ファイルを読み込むFileReaderオブジェクト
    // 読込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
    fileReader.onload = function(event){
      //読み込んだデータをimgに設定
      $img.attr('src', event.target.result).show();
    };
    // 画像読み込み
    fileReader.readAsDataURL(file);
  });

  //文字カウンター
  var $countUp = $('#js-count'),
      $countView = $('#js-count-view');
  $countUp.on('keyup', function(e){
    $countView.html($(this).val().length);
  });

  // お気に入り登録・削除
  var $like, 
      likeSpotId;
  // $('.js-click-like')の中身がなかった場合、nullを$likeに代入する
  $like = $('.js-click-like') || null;
  // $likeのDOM属性'data-spotid'の値を取得
  // 注意！！data属性は大文字使えません！
  likeSpotId = $like.data('spotid') || null;
  // 数値の0はfalseと判定されてしまう。spot_idが0の場合もありえるので、0もtrueとする場合にはundefinedとnullを判定する
  console.log('likeSpotId:'+likeSpotId);
  if(likeSpotId !== undefined && likeSpotId !== null){
    $like.on('click', function(){
      var $this = $(this);
      $.ajax({
        type: "POST",
        url: "ajaxLike.php",
        data: { spotId : likeSpotId}
      }).done(function( data ){
        console.log('Ajax Success');
        // クラス属性をtoggleでつけ外しする
        $this.toggleClass('active');
      }).fail(function( msg ){
        console.log('Ajax Error');
      });
    });
  }

});
