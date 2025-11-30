<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タイムライン（ダミー）</title>

    <style>
        body {
            background: #f5f7fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background: #1da1f2;
            color: white;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 0 15px;
        }

        .post {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.07);
        }

        .user {
            font-weight: bold;
        }

        .time {
            color: #777;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .body {
            margin-bottom: 10px;
            white-space: pre-line;
        }

        .actions {
            font-size: 12px;
            color: #777;
        }

        .actions span {
            margin-right: 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="header">
        タイムライン（ダミー）
    </div>

    <div class="container">

        <div class="post">
            <div class="user">田島</div>
            <div class="time">1分前</div>
            <div class="body">これは最初のダミー投稿です。</div>
            <div class="actions">
                <span>❤️ いいね</span>
                <span>💬 リプライ</span>
            </div>
        </div>

        <div class="post">
            <div class="user">山田</div>
            <div class="time">5分前</div>
            <div class="body">Laravelで自作SNSの基礎を作っています！</div>
            <div class="actions">
                <span>❤️ いいね</span>
                <span>💬 リプライ</span>
            </div>
        </div>

        <div class="post">
            <div class="user">佐藤</div>
            <div class="time">10分前</div>
            <div class="body">今日は眠い…でも頑張る。</div>
            <div class="actions">
                <span>❤️ いいね</span>
                <span>💬 リプライ</span>
            </div>
        </div>

    </div>

</body>
</html>
