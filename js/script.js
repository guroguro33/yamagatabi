jQuery(document).ready(function(){
  //フッターの位置調整
  var $ftr = $('#footer');
  if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
    $ftr.attr({'style':'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;'});
  } 

  //メッセージ表示
  var $jsShowImg = $('#js-show-img');
  var msg = $jsShowImg.text();
  if(msg.replace(/^[\s　]+[\s　]+$/g,"").length){
    $jsShowMsg.fadeToggle('slow');
    setTimeout(function(){ $jsShowImg.slideToggle('slow'); }, 5000);
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

});
