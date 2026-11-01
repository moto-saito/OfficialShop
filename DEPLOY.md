# 本番デプロイ手順（Render 無料プラン + 外部無料サービス）

Render の無料 Web サービスは**ディスクが永続化されない**ため、データと画像は外部に置く。

| 役割 | サービス | 無料枠 |
|---|---|---|
| アプリ | Render（Docker / Free） | 15 分アクセスがないとスリープ。復帰の初回は 30〜60 秒 |
| DB | Neon（Postgres） | 0.5GB。アイドル時は自動停止→アクセスで自動復帰 |
| 画像 | Supabase Storage（S3 互換） | 1GB |

> Supabase の無料プロジェクトは **7 日間操作がないと一時停止**される（ダッシュボードから再開可）。
> 心配なら Cloudflare R2（10GB 無料・停止なし・カード登録必須）でも同じ設定項目で使える。

## 1. Neon で DB を作る

1. https://neon.tech でサインアップ → プロジェクト作成（Region: **AWS Asia Pacific (Singapore)**）
2. Dashboard → **Connect** で接続文字列をコピー
   `postgresql://<user>:<pass>@ep-xxxx.ap-southeast-1.aws.neon.tech/neondb?sslmode=require`
   → これが `DB_URL`

## 2. Supabase Storage で画像置き場を作る

1. https://supabase.com でプロジェクト作成（Region: Singapore）
2. **Storage → New bucket** → 名前 `media`、**Public bucket を ON**
3. **Project Settings → Storage → S3 Connection**
   - Endpoint（例 `https://<ref>.supabase.co/storage/v1/s3`）→ `MEDIA_S3_ENDPOINT`
   - Region（例 `ap-southeast-1`）→ `MEDIA_S3_REGION`
   - **New access key** で発行 → `MEDIA_S3_KEY` / `MEDIA_S3_SECRET`
4. 公開 URL のベース → `MEDIA_S3_URL`
   `https://<ref>.supabase.co/storage/v1/object/public/media`
5. `MEDIA_S3_BUCKET` は `media`

## 3. Render にデプロイ

1. https://dashboard.render.com → **New → Blueprint** → このリポジトリを選択（`main` ブランチ）
2. `render.yaml` が読み込まれ `official-shop`（Docker / Free）が作成される
3. `sync: false` の項目を入力する

   | キー | 値 |
   |---|---|
   | `APP_KEY` | ローカルで `echo "base64:$(openssl rand -base64 32)"` の出力 |
   | `DB_URL` | 手順 1 の接続文字列 |
   | `MEDIA_S3_*` | 手順 2 の値 |
   | `ADMIN_EMAIL` / `ADMIN_PASSWORD` | 初期管理者（**推測されにくいパスワード**にする） |

4. **Apply** → ビルド完了後 `https://official-shop-xxxx.onrender.com` で公開
   （`APP_URL` は `RENDER_EXTERNAL_URL` から自動設定。独自ドメインを使う場合は `APP_URL` を設定）

以降は `main` に push するたび自動デプロイ。起動時に `migrate --force` が走り、データは保持される。

## 起動時の動作（`docker/prod/start.sh`）

| `DB_CONNECTION` | 動作 |
|---|---|
| `pgsql` など | **本番モード**: `migrate --force` → `ProductionSeeder`（管理者が無ければ作成）。データは保持 |
| `sqlite`（未設定時） | **デモモード**: 起動ごとに SQLite を作り直し `migrate --seed`（テストユーザー入り、毎回リセット） |

`MEDIA_DISK_DRIVER=s3` のとき画像は S3 互換ストレージへ、未設定ならローカル（`storage/app/public`）へ保存。

## ローカルで本番イメージを確認

```
docker build -t shop-prod .
# デモモード（SQLite）
docker run --rm -p 8000:8000 -e PORT=8000 \
  -e APP_KEY="base64:..." -e APP_URL="http://localhost:8000" shop-prod
```

## 注意 / 既知の制限

- メールは `MAIL_MAILER=log`（送信されない）。注文確認メール等を送るなら Resend / Brevo 等の SMTP を設定する。
- 決済は未連携。実際に代金を受け取る前に、特定商取引法に基づく表記・プライバシーポリシーを用意する。
- 無料プランのスリープ復帰で初回表示が遅い。気になる場合は Starter（$7/月）へ。
