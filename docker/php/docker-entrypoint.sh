#!/bin/sh

set -e

if [ ! -d vendor ]; then
  composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-scripts
fi

exec "$@"
