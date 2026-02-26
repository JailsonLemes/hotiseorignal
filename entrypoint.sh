#!/bin/sh
set -e

TARGET_DIR="/var/www/html/onboarding/printScreen/Files"

mkdir -p "$TARGET_DIR/queue" "$TARGET_DIR/status" "$TARGET_DIR/jobs"
chown -R www-data:www-data "$TARGET_DIR"
chmod -R 775 "$TARGET_DIR"

exec "$@"
