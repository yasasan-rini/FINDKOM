#!/usr/bin/env bash
set -euo pipefail

TARGET="${1:-findkom-app}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEST="$(dirname "$SRC")/$TARGET"

echo "1/7 Membuat skeleton Laravel 12 di $DEST"
composer create-project laravel/laravel "$DEST" "^12.0"

echo "2/7 Menyalin berkas starter"
for d in app bootstrap config database docs public resources routes tests; do
  [ -d "$SRC/$d" ] && mkdir -p "$DEST/$d" && cp -R "$SRC/$d/." "$DEST/$d/"
done
for f in .env.example phpunit.xml README.md; do
  [ -f "$SRC/$f" ] && cp "$SRC/$f" "$DEST/$f"
done

echo "3/7 Menghapus berkas bawaan skeleton yang bentrok"
rm -f "$DEST"/database/migrations/0001_01_01_* \
      "$DEST"/tests/Feature/ExampleTest.php \
      "$DEST"/tests/Unit/ExampleTest.php

cd "$DEST"
echo "4/7 Menyiapkan .env"
cp .env.example .env
php artisan key:generate --force

echo "5/7 Membuat database (bila MySQL ada di PATH)"
export MYSQL_PWD="$DB_PASS"
for db in findkom findkom_test; do
  if command -v mysql >/dev/null 2>&1; then
    mysql -u"$DB_USER" -e "CREATE DATABASE IF NOT EXISTS \`$db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" \
      && echo "   database $db siap" || echo "   ! buat database '$db' manual"
  else
    echo "   ! mysql tidak ditemukan — buat database '$db' manual"
  fi
done

echo "6/7 Migrasi + seeder + storage link"
php artisan migrate --seed --force
php artisan storage:link
php artisan config:clear

echo "7/7 Selesai. Jalankan: cd $TARGET && php artisan serve"
echo "   Login demo: rina.wulandari@student.ub.ac.id / budi.santoso@ub.ac.id (findkom123)"
echo "   Audit: php artisan findkom:audit   |   Test: php artisan test"