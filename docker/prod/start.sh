#!/usr/bin/env bash
set -e

cd /var/www/html

# Render は $PORT を渡す。ローカル実行時は 80。
export APACHE_PORT="${PORT:-80}"

# Render が払い出す公開 URL を APP_URL に反映（未設定時のみ）
if [ -z "${APP_URL}" ] && [ -n "${RENDER_EXTERNAL_URL}" ]; then
    export APP_URL="${RENDER_EXTERNAL_URL}"
fi

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    # ---- デモモード: 起動ごとに SQLite を作り直す（データは毎回リセット）----
    rm -f database/database.sqlite
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    php artisan migrate --force --seed --no-interaction
else
    # ---- 本番モード: 外部 DB（Postgres 等）のデータを保持したままマイグレーション ----
    php artisan migrate --force --no-interaction
    php artisan db:seed --class=ProductionSeeder --force --no-interaction
fi

# ローカルディスクに画像を置く場合のみシンボリックリンクが必要
if [ "${MEDIA_DISK_DRIVER:-local}" != "s3" ]; then
    php artisan storage:link || true
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
