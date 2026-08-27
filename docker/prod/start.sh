#!/usr/bin/env bash
set -e

cd /var/www/html

# Render は $PORT を渡す。ローカル実行時は 80。
export APACHE_PORT="${PORT:-80}"

# Render が払い出す公開 URL を APP_URL に反映（未設定時のみ）
if [ -n "${RENDER_EXTERNAL_URL}" ]; then
    export APP_URL="${RENDER_EXTERNAL_URL}"
fi

# ---- デプロイ/起動ごとに SQLite を作り直す（データは毎回リセット）----
rm -f database/database.sqlite
touch database/database.sqlite
chown www-data:www-data database/database.sqlite

php artisan migrate --force --seed --no-interaction
php artisan storage:link || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
