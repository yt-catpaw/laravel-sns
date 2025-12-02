@extends('layouts.base')

@section('title', 'タイムライン（ダミー）')

@section('content')
  <style>
    html, body { height: 100%; }
    body {
        background: #f5f7fa;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .tl-body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    .tl-header {
      background: #1da1f2;
      color: white;
      padding: 15px;
      font-size: 20px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .tl-container {
      max-width: 600px;
      margin: 20px auto;
      padding: 0 15px;
    }

    .tl-post {
      background: white;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.07);
    }

    .tl-user {
      font-weight: bold;
    }

    .tl-time {
      color: #777;
      font-size: 12px;
      margin-bottom: 8px;
    }

    .tl-bodytext {
      margin-bottom: 10px;
      white-space: pre-line;
    }

    .tl-actions {
      font-size: 12px;
      color: #777;
    }

    .tl-actions span {
      margin-right: 15px;
      cursor: pointer;
    }

    .tl-logout-btn {
      background: white;
      color: #1da1f2;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
  </style>

  <div class="tl-body">
    <div class="tl-header">
      <div>タイムライン（ダミー）</div>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="tl-logout-btn">ログアウト</button>
      </form>
    </div>

    <div class="tl-container">
      <div class="tl-post">
        <div class="tl-user">田島</div>
        <div class="tl-time">1分前</div>
        <div class="tl-bodytext">これは最初のダミー投稿です。</div>
        <div class="tl-actions">
          <span>❤️ いいね</span>
          <span>💬 リプライ</span>
        </div>
      </div>

      <div class="tl-post">
        <div class="tl-user">山田</div>
        <div class="tl-time">5分前</div>
        <div class="tl-bodytext">Laravelで自作SNSの基礎を作っています！</div>
        <div class="tl-actions">
          <span>❤️ いいね</span>
          <span>💬 リプライ</span>
        </div>
      </div>

      <div class="tl-post">
        <div class="tl-user">佐藤</div>
        <div class="tl-time">10分前</div>
        <div class="tl-bodytext">今日は眠い…でも頑張る。</div>
        <div class="tl-actions">
          <span>❤️ いいね</span>
          <span>💬 リプライ</span>
        </div>
      </div>
    </div>
  </div>
@endsection
