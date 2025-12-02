<header class="site-header" role="banner">
    <div class="site-header__left">
        <a class="site-header__brand" href="/">
            <span class="site-header__logo">ロゴ</span>
        </a>
    </div>

    <div class="site-header__center">
        <form class="search search--header" action="" method="GET" role="search">
            <input class="search__input" type="search" name="q" placeholder="検索" aria-label="検索" />
        </form>
    </div>

    <button
        class="site-header__menu-btn"
        type="button"
        aria-label="メニュー"
        aria-expanded="false"
        data-header-toggle
    >☰</button>

    <div class="site-header__right" data-header-menu>
        <nav class="site-header__nav" aria-label="メインナビゲーション">
            <a class="site-header__nav-link" href="#">ホーム</a>
            <a class="site-header__nav-link" href="#">通知</a>
            <a class="site-header__nav-link" href="#">自分</a>
        </nav>

        <form class="site-header__logout" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="button button--outline site-header__logout-btn">ログアウト</button>
        </form>
    </div>
</header>
