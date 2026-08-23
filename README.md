# official-shop

Laravel + Docker で構築するECサイト（醤油製造所の公式ショップ）。

## 技術スタック

- PHP 8.2 / Laravel 12
- MySQL 8.0
- Vite 7 + Tailwind CSS 4
- Docker / Docker Compose

## 構成

```
.
├── backend/          # Laravelアプリケーション本体
├── docker/            # Dockerfile・PHP/Apache/MySQL設定
├── mysql/             # MySQLデータ永続化用（Git管理外）
└── docker-compose.yml
```

コンテナ構成:

| サービス   | 役割                  | ポート |
|------------|-----------------------|--------|
| app        | PHP 8.2 + Apache       | 80     |
| db         | MySQL 8.0              | 3306   |
| phpmyadmin | DB管理用GUI            | 8080   |

## セットアップ

### 前提

- Git がインストールされていること
- Docker / Docker Compose がインストールされていること
- ホスト側で 80 / 3306 / 8080 番ポートが空いていること

### 手順

```bash
git clone <このリポジトリのURL>
cd official-shop

# .env を作成
cp backend/.env.example backend/.env

# コンテナ起動
docker compose up -d --build

# コンテナに入る
docker compose exec app bash

# ここからコンテナ内で実行
composer install
php artisan key:generate
npm install
npm run build
php artisan migrate --seed
php artisan storage:link
```

### `.env` の主な設定値（docker-compose.yml と対応）

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=official_shop
DB_USERNAME=laravel
DB_PASSWORD=password
```

`backend/.env.example` はLaravel標準のsqlite設定のままになっているため、`.env`作成後に上記の値へ書き換えること。

## アクセス先

- アプリ本体: http://localhost
- phpMyAdmin: http://localhost:8080 （ユーザー: root / パスワード: root）

## よく使うコマンド

```bash
# コンテナ起動・停止
docker compose up -d
docker compose down

# コンテナ内でartisanコマンド
docker compose exec app php artisan <command>

# フロントエンド開発サーバー（HMR）
docker compose exec app npm run dev

# テスト実行
docker compose exec app php artisan test
```

## 開発者アカウント（初期データ）

- 管理者: admin@example.com / password（`/admin/login`）

## 注意事項

- `backend/.env` と `mysql/` ディレクトリの中身はGit管理対象外。各自の環境で作成すること。
- DBの初期データはマイグレーション・シーダーで再現する（`mysql/`の実データはリポジトリで共有しない）。
