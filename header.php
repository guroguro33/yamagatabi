    <header>
      <div class="wrap">
        <div class="logo">
          <a href="index.php">
            <img src="./img/logo.svg" alt="ヤマガタビのロゴ">
            <p class="logo-sub">- 山形のおすすめ旅スポット -</p>
          </a>
        </div>
        <nav class="nav-menu">
          <ul>
          <?php
           if(empty($_SESSION['user_id'])):
          ?>
            <li><a href="login.php">ログイン</a></li>
            <li><a href="signup.php">新規登録</a></li>
          <?php else: ?>
            <li><a href="logout.php">ログアウト</a></li>
            <li><a href="mypage.php">マイページ</a></li>
          <?php endif; ?>
          </ul>
        </nav>
      </div>
    </header>