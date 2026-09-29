#!/usr/bin/env bash

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

schemaUrl="${1:-https://fingerprintjs.github.io/openapi/schemas/fingerprint-server-api-compact.yaml}"

CURL_OPTS=(-fSL --retry 3 --proto-redir '=https' --connect-timeout 10 --max-time 300)
if [[ "${TRACE:-}" != "true" && "${ACTIONS_STEP_DEBUG:-}" != "true" ]]; then
  CURL_OPTS+=(-s)
fi

mkdir -p ./res

require_cmd curl

echo "Downloading $schemaUrl"
curl "${CURL_OPTS[@]}" -o ./res/fingerprint-server-api.yaml "$schemaUrl"

echo "OpenAPI schema download complete."

# Add `deprecated: true` for component schemas
echo "Adding deprecation for component schemas..."
docker run --rm -v "${PWD}:/work" python:3-alpine sh -c \
  "pip install ruamel.yaml -q && python3 /work/scripts/deprecate.py"

sed_in_place '/IpInfoResult:/,/IpBlockListResult:/ { /dataCenter:/ { N; d; }; }' ./res/fingerprint-server-api.yaml

./scripts/generate.sh
