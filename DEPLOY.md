# デモ公開手順（Render 無料プラン）

アイドル 15 分で自動スリープ → アクセスで自動復帰するため、
「月 10 時間程度アクセス」という使い方なら実質無料で運用できる。

- DB は **SQLite**。コンテナ起動 / 再デプロイのたびに作り直され、データはリセットされる。
- ログインアカウント（シーダーで毎回作成）
  - 管理者: `admin@example.com` / `password` （`/admin/login`）
  - 一般ユーザー: `test@example.com` / `password`
- アップロード画像はコンテナのローカルに保存され、再デプロイで消える。

## 構成ファイル

| ファイル | 役割 |
|---|---|
| `Dockerfile` | 本番イメージ（Node で資産ビルド → PHP/Apache）。ビルドコンテキストはリポジトリ直下 |
| `render.yaml` | Render の Blueprint（サービス定義・環境変数） |
| `docker/prod/start.sh` | 起動時に SQLite 再作成 → `migrate --seed` → 設定キャッシュ → Apache |
| `docker/prod/vhost.conf` `ports.conf` `php.ini` | Apache / PHP 設定（`$PORT` で待受） |
| `.dockerignore` | `vendor/` `node_modules/` `mysql/` などを除外 |

## デプロイ

1. このブランチを GitHub に push する。
2. https://dashboard.render.com → **New +** → **Blueprint** → リポジトリを選択。
   `render.yaml` が読み込まれ、`official-shop-demo`（Docker / Free）が作成される。
3. `APP_KEY` を設定する（`sync: false` のため手入力）。ローカルで:
   ```
   docker run --rm $(docker build -q .) php artisan key:generate --show
   ```
   出力された `base64:...` を Render の Environment に貼り付け。
4. 初回デプロイ完了後、`https://official-shop-demo.onrender.com` で公開。
   （`APP_URL` は起動時に `RENDER_EXTERNAL_URL` から自動設定される）

以降は push するたび自動デプロイ（`autoDeploy: true`）。

## ローカルで本番イメージを確認

```
docker build -t shop-prod .
docker run --rm -p 8000:8000 -e PORT=8000 \
  -e APP_KEY="base64:..." -e APP_URL="http://localhost:8000" shop-prod
# http://localhost:8000
```

## 注意 / 既知の制限

- 無料プランは**永続ディスクなし**。データを残したい場合は外部 DB（Aiven の無料 MySQL 等）へ切替が必要。
- スリープ復帰の初回アクセスは 20〜40 秒かかる。
- 商品・レシピ等の初期データを入れるシーダーは未整備。デモ用データが必要なら
  `database/seeders/` に専用シーダーを追加して `DatabaseSeeder` から呼ぶ。
