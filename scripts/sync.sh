#!/usr/bin/env bash

source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

defaultBaseUrl="https://fingerprintjs.github.io/openapi"
schemaUrl="${1:-$defaultBaseUrl/schemas/fingerprint-server-api-v4.yaml}"
examplesBaseUrl="${2:-$defaultBaseUrl/examples}"

CURL_OPTS=(-fSL --retry 3 --proto-redir '=https' --connect-timeout 10 --max-time 300)
if [[ "${TRACE:-}" != "true" && "${ACTIONS_STEP_DEBUG:-}" != "true" ]]; then
  CURL_OPTS+=(-s)
fi

schemaDestination="./res/fingerprint-server-api.yaml"
baseDestination="./test/mocks"

mkdir -p "$(dirname "$schemaDestination")"

require_cmd curl

echo "Downloading $schemaUrl to $schemaDestination"
curl "${CURL_OPTS[@]}" -o "$schemaDestination" "$schemaUrl"

examples=(
  'events/get_event_200.json'
  'events/get_event_ruleset_200.json'
  'events/search/get_event_search_200.json'
  'webhook/webhook_event.json'
  'errors/400_event_id_invalid.json'
  'errors/400_request_body_invalid.json'
  'errors/400_ruleset_not_found.json'
  'errors/400_visitor_id_invalid.json'
  'errors/400_visitor_id_required.json'
  'errors/403_feature_not_enabled.json'
  'errors/403_secret_api_key_not_found.json'
  'errors/403_secret_api_key_required.json'
  'errors/403_subscription_not_active.json'
  'errors/403_wrong_region.json'
  'errors/404_event_not_found.json'
  'errors/404_visitor_not_found.json'
  'errors/409_state_not_ready.json'
  'errors/429_too_many_requests.json'
  'errors/500_internal_server_error.json'
)

for example in "${examples[@]}"; do
  destinationPath="$baseDestination/$example"
  mkdir -p "$(dirname "$destinationPath")"

  exampleUrl="$examplesBaseUrl/$example"
  echo "Downloading $exampleUrl to $destinationPath"
  curl "${CURL_OPTS[@]}" -o "$destinationPath" "$exampleUrl"
done

echo "All OpenAPI schema downloads complete."

./scripts/generate.sh
