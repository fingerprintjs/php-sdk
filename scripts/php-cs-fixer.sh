#!/bin/bash

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

PHP_CS_FIXER_IMAGE_VERSION="3.95-php8.4@sha256:57bd222a8032b764b7bffda03e42fddb567731178e07004b98af8ef328efd49d"

require_cmd docker

if [ ! -f .php-cs-fixer.php ]; then
  echo ".php-cs-fixer.php configuration file not found!"
  exit 1
fi

docker run --rm -u "$(id -u):$(id -g)" -v "${PWD}:/code" \
  "ghcr.io/php-cs-fixer/php-cs-fixer:${PHP_CS_FIXER_IMAGE_VERSION}" fix "$@" \
  --config=/code/.php-cs-fixer.php
