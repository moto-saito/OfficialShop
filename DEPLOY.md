# デプロイ手順

| 用途 | 環境 | データ |
|---|---|---|
| **本番** | さくらのクラウド（Docker） | 外部 DB に保持 |
| デモ | Render 無料プラン（`render.yaml`） | 起動ごとにリセット |

どちらも同じ `Dockerfile` を使い、環境変数で動作を切り替える。

## 起動時の動作（`docker/prod/start.sh`）

| 条件 | 動作 |
|---|---|
| `DB_CONNECTION` が `pgsql` / `mariadb` / `mysql` | **本番モード**: `migrate --force` → `ProductionSeeder`（`ADMIN_EMAIL` の管理者が無ければ作成）。データは保持 |
| `DB_CONNECTION=sqlite`（未設定時） | **デモモード**: SQLite を作り直して `migrate --seed`（テストユーザー入り） |
| `MEDIA_DISK_DRIVER=s3` | 画像を S3 互換オブジェクトストレージに保存 |
| `MEDIA_DISK_DRIVER=local`（未設定時） | 画像を `storage/app/public` に保存（`storage:link` を作成） |
| `TRUSTED_PROXIES` | 指定したプロキシからの `X-Forwarded-*` を信頼（LB 配下で HTTPS を検出するため） |

---

## 本番: さくらのクラウド

### 構成例（サーバー 1 台）

```
[インターネット] → [サーバー: nginx/Caddy で HTTPS 終端] → [app コンテナ :8000]
                                                         ├→ DB（データベースアプライアンス or 同サーバー）
                                                         └→ 画像（ホストのディレクトリ or オブジェクトストレージ）
```

- **DB**: データベースアプライアンス（MariaDB / PostgreSQL）を推奨。
  MariaDB なら `DB_CONNECTION=mariadb` / `DB_PORT=3306`、PostgreSQL なら `pgsql` / `5432`。
- **画像**: サーバー 1 台ならホストのディレクトリをボリュームでマウントすれば `local` のままでよい。
  AppRun（コンテナ実行基盤）や複数台構成など、ディスクが永続化されない場合はオブジェクトストレージ（`MEDIA_DISK_DRIVER=s3`）を使う。
  エンドポイント・リージョン・公開 URL はコントロールパネルの値で確認すること。

### 手順

1. サーバーに Docker をインストールし、リポジトリを取得してイメージをビルド
   ```
   git clone <repo> /opt/official-shop/src && cd /opt/official-shop/src
   docker build -t official-shop .
   ```
2. 環境変数ファイルを作成して値を埋める（テンプレート: `docker/prod/env.production.example`）
   ```
   cp docker/prod/env.production.example /opt/official-shop/.env.production
   chmod 600 /opt/official-shop/.env.production
   ```
3. 画像用ディレクトリを用意（`local` の場合。コンテナ内の www-data は UID 33）
   ```
   mkdir -p /opt/official-shop/media && chown 33:33 /opt/official-shop/media
   ```
4. 起動
   ```
   docker run -d --name official-shop --restart unless-stopped \
     -p 127.0.0.1:8000:8000 \
     --env-file /opt/official-shop/.env.production \
     -v /opt/official-shop/media:/var/www/html/storage/app/public \
     official-shop
   ```
5. nginx / Caddy などで HTTPS 化し `127.0.0.1:8000` へプロキシ。
   同一サーバーのリバースプロキシなら `TRUSTED_PROXIES=127.0.0.1,172.16.0.0/12`（Docker ネットワーク）を設定。
6. 動作確認: `curl -I https://<ドメイン>/up` が 200

### 更新

```
cd /opt/official-shop/src && git pull && docker build -t official-shop .
docker rm -f official-shop && <手順 4 の docker run>
```
起動時に `migrate --force` が走る。**更新前に DB のバックアップ**（アプライアンスのバックアップ機能等）を取ること。

---

## デモ: Render 無料プラン

1. https://dashboard.render.com → **New → Blueprint** → このリポジトリ（`main`）
2. `APP_KEY` に `echo "base64:$(openssl rand -base64 32)"` の出力を貼り付け → Apply
3. ログイン: 管理者 `admin@example.com` / `password`、一般 `test@example.com` / `password`

アイドル 15 分でスリープし、そのたびにデータ・画像はリセットされる。

---

## ローカルで本番イメージを確認

```
docker build -t shop-prod .
docker run --rm -p 8000:8000 -e PORT=8000 \
  -e APP_KEY="base64:..." -e APP_URL="http://localhost:8000" shop-prod
```

## 公開前のチェック

- メールは `MAIL_MAILER=log`（送信されない）。注文通知等を送るなら SMTP を設定する。
- オンライン決済は未連携。特定商取引法に基づく表記・プライバシーポリシーを用意する。
- `.env.production` は Git に入れない。`APP_KEY` は一度決めたら変えない（変えるとセッション等が無効になる）。
