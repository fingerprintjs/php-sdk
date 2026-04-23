# Fingerprint\ServerSdk\Api\FingerprintApi

Using the Server API you can retrieve information about individual analysis events or event history of individual visitors.

All URIs are relative to *https://api.fpjs.io/v4*

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**deleteVisitorData()**](FingerprintApi.md#deleteVisitorData) | **DELETE** /visitors/{visitor_id} | Delete data by visitor ID |
| [**getEvent()**](FingerprintApi.md#getEvent) | **GET** /events/{event_id} | Get an event by event ID |
| [**searchEvents()**](FingerprintApi.md#searchEvents) | **GET** /events | Search events |
| [**updateEvent()**](FingerprintApi.md#updateEvent) | **PATCH** /events/{event_id} | Update an event |


# **deleteVisitorData**
> deleteVisitorData($visitor_id)

Delete data by visitor ID

Request deleting all data associated with the specified visitor ID. This API is useful for compliance with privacy regulations.

### Which data is deleted?
- Browser (or device) properties
- Identification requests made from this browser (or device)

#### Browser (or device) properties
- Represents the data that Fingerprint collected from this specific browser (or device) and everything inferred and derived from it.
- Upon request to delete, this data is deleted asynchronously (typically within a few minutes) and it will no longer be used to identify this browser (or device) for your [Fingerprint Workspace](https://docs.fingerprint.com/docs/glossary#fingerprint-workspace).

#### Identification requests made from this browser (or device)
- Fingerprint stores the identification requests made from a browser (or device) for up to 30 (or 90) days depending on your plan. To learn more, see [Data Retention](https://docs.fingerprint.com/docs/regions#data-retention).
- Upon request to delete, the identification requests that were made by this browser
  - Within the past 10 days are deleted within 24 hrs.
  - Outside of 10 days are allowed to purge as per your data retention period.

### Corollary
After requesting to delete a visitor ID,
- If the same browser (or device) requests to identify, it will receive a different visitor ID.
- If you request [`/v4/events` API](https://docs.fingerprint.com/reference/server-api-v4-get-event) with an `event_id` that was made outside of the 10 days, you will still receive a valid response.

### Interested?
Please [contact our support team](https://fingerprint.com/support/) to enable it for you. Otherwise, you will receive a 403.


### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Fingerprint\ServerSdk\Api\FingerprintApi;
use Fingerprint\ServerSdk\Configuration;
use Fingerprint\ServerSdk\ApiException;

const API_SECRET = "Your Fingerprint Secret API Key"; // Fingerprint Secret API Key

// Create new Configuration instance with API Secret and the Region
$config = new Configuration(API_SECRET, Configuration::REGION_EUROPE);
$apiInstance = new FingerprintApi(
    $config,
    // If you want to use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

$visitor_id = 'visitor_id_example'; // string | The [visitor ID](https://docs.fingerprint.com/reference/js-agent-v4-get-function#visitor_id) you want to delete.

try {
    $apiInstance->deleteVisitorData($visitor_id);
} catch (ApiException $e) {
    $errorDetails = $e->getErrorDetails()->getErrorDetails();
    echo "[{$errorDetails->getCode()}] Exception when calling FingerprintApi->deleteVisitorData: {$errorDetails->getMessage()}" . PHP_EOL;
} catch (Exception $e) {
    echo 'Exception when calling FingerprintApi->deleteVisitorData: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **visitor_id** | **string** | The [visitor ID](https://docs.fingerprint.com/reference/js-agent-v4-get-function#visitor_id) you want to delete. | |

### Return type

void (empty response body)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)

# **getEvent**
>  \Fingerprint\ServerSdk\Model\Event getEvent($event_id, $ruleset_id)

Get an event by event ID

Get a detailed analysis of an individual identification event, including Smart Signals.

Use `event_id` as the URL path parameter. This API method is scoped to a request, i.e. all returned information is by `event_id`.


### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Fingerprint\ServerSdk\Api\FingerprintApi;
use Fingerprint\ServerSdk\Configuration;
use Fingerprint\ServerSdk\ApiException;

const API_SECRET = "Your Fingerprint Secret API Key"; // Fingerprint Secret API Key

// Create new Configuration instance with API Secret and the Region
$config = new Configuration(API_SECRET, Configuration::REGION_EUROPE);
$apiInstance = new FingerprintApi(
    $config,
    // If you want to use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

$event_id = 'event_id_example'; // string | The unique [identifier](https://docs.fingerprint.com/reference/js-agent-v4-get-function#event_id) of each identification request (`requestId` can be used in its place).
$ruleset_id = 'ruleset_id_example'; // string | The ID of the ruleset to evaluate against the event, producing the action to take for this event. The resulting action is returned in the `rule_action` attribute of the response.

try {
    $result = $apiInstance->getEvent($event_id, $ruleset_id);
    var_dump($result);
} catch (ApiException $e) {
    $errorDetails = $e->getErrorDetails()->getErrorDetails();
    echo "[{$errorDetails->getCode()}] Exception when calling FingerprintApi->getEvent: {$errorDetails->getMessage()}" . PHP_EOL;
} catch (Exception $e) {
    echo 'Exception when calling FingerprintApi->getEvent: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **event_id** | **string** | The unique [identifier](https://docs.fingerprint.com/reference/js-agent-v4-get-function#event_id) of each identification request (`requestId` can be used in its place). | |
| **ruleset_id** | **string** | The ID of the ruleset to evaluate against the event, producing the action to take for this event. The resulting action is returned in the `rule_action` attribute of the response. | [optional] |

### Return type

[**\Fingerprint\ServerSdk\Model\Event**](../Model/Event.md)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)

# **searchEvents**
>  \Fingerprint\ServerSdk\Model\EventSearch searchEvents($limit, $pagination_key, $visitor_id, $high_recall_id, $bot, $ip_address, $asn, $linked_id, $url, $bundle_id, $package_name, $origin, $start, $end, $reverse, $suspect, $vpn, $virtual_machine, $tampering, $anti_detect_browser, $incognito, $privacy_settings, $jailbroken, $frida, $factory_reset, $cloned_app, $emulator, $root_apps, $vpn_confidence, $min_suspect_score, $developer_tools, $location_spoofing, $mitm_attack, $rare_device, $rare_device_percentile_bucket, $proxy, $sdk_version, $sdk_platform, $environment, $proximity_id, $total_hits, $tor_node, $incremental_identification_status, $simulator)

Search events

## Search

The `/v4/events` endpoint provides a convenient way to search for past events based on specific parameters. Typical use cases and queries include:

- Searching for events associated with a single `visitor_id` within a time range to get historical behavior of a visitor.
- Searching for events associated with a single `linked_id` within a time range to get all events associated with your internal account identifier.
- Excluding all bot traffic from the query (`good` and `bad` bots)

By default, the API searches events from the last 7 days, sorts them by newest first and returns the last 10 events.

- Use `start` and `end` to specify the time range of the search.
- Use `reverse=true` to sort the results oldest first.
- Use `limit` to specify the number of events to return.
- Use `pagination_key` to get the next page of results if there are more than `limit` events.

### Filtering events with the `suspect` flag

The `/v4/events` endpoint unlocks a powerful method for fraud protection analytics. The `suspect` flag is exposed in all events where it was previously set by the update API.

You can also apply the `suspect` query parameter as a filter to find all potentially fraudulent activity that you previously marked as `suspect`. This helps identify patterns of fraudulent behavior.

### Environment scoping

If you use a secret key that is scoped to an environment, you will only get events associated with the same environment. With a workspace-scoped environment, you will get events from all environments.

Smart Signals not activated for your workspace or are not included in the response.


### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Fingerprint\ServerSdk\Api\FingerprintApi;
use Fingerprint\ServerSdk\Configuration;
use Fingerprint\ServerSdk\ApiException;

const API_SECRET = "Your Fingerprint Secret API Key"; // Fingerprint Secret API Key

// Create new Configuration instance with API Secret and the Region
$config = new Configuration(API_SECRET, Configuration::REGION_EUROPE);
$apiInstance = new FingerprintApi(
    $config,
    // If you want to use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

$limit = 10; // int | Maximum number of events to return. Results are selected from the time range (`start`, `end`), ordered by `reverse`, then truncated to provided `limit` size. So `reverse=true` returns the oldest N=`limit` events, otherwise the newest N=`limit` events.
$pagination_key = 'pagination_key_example'; // string | Use `pagination_key` to get the next page of results.  When more results are available (e.g., you requested up to 100 results for your query using `limit`, but there are more than 100 events total matching your request), the `pagination_key` field is added to the response. The pagination key is an arbitrary string that should not be interpreted in any way and should be passed as-is. In the following request, use that value in the `pagination_key` parameter to get the next page of results:  1. First request, returning most recent 200 events: `GET api-base-url/events?limit=100` 2. Use `response.pagination_key` to get the next page of results: `GET api-base-url/events?limit=100&pagination_key=1740815825085`
$visitor_id = 'visitor_id_example'; // string | Unique [visitor identifier](https://docs.fingerprint.com/reference/js-agent-v4-get-function#visitor_id) issued by Fingerprint Identification and all active Smart Signals.  Filter events by matching Visitor ID (`identification.visitor_id` property).
$high_recall_id = 'high_recall_id_example'; // string | The High Recall ID is a supplementary browser identifier designed for use cases that require wider coverage over precision. Compared to the standard visitor ID, the High Recall ID strives to match incoming browsers more generously (rather than precisely) with existing browsers and thus identifies fewer browsers as new. The High Recall ID is best suited for use cases that are sensitive to browsers being identified as new and where mismatched browsers are not detrimental.  Filter events by matching High Recall ID (`supplementary_id_high_recall.visitor_id` property).
$bot = \Fingerprint\ServerSdk\Model\SearchEventsBot::GOOD; // \Fingerprint\ServerSdk\Model\SearchEventsBot | Filter events by the Bot Detection result, specifically:   `all` - events where any kind of bot was detected.   `good` - events where a good bot was detected.   `bad` - events where a bad bot was detected.   `none` - events where no bot was detected. > Note: When using this parameter, only events with the `bot` property set to a valid value are returned. Events without a `bot` Smart Signal result are left out of the response.
$ip_address = 'ip_address_example'; // string | Filter events by IP address or IP range (if CIDR notation is used). If CIDR notation is not used, a /32 for IPv4 or /128 for IPv6 is assumed. Examples of range based queries: 10.0.0.0/24, 192.168.0.1/32
$asn = 'asn_example'; // string | Filter events by the ASN associated with the event's IP address. This corresponds to the `ip_info.(v4|v6).asn` property in the response.
$linked_id = 'linked_id_example'; // string | Filter events by your custom identifier.  You can use [linked Ids](https://docs.fingerprint.com/reference/js-agent-v4-get-function#linkedid) to associate identification requests with your own identifier, for example, session Id, purchase Id, or transaction Id. You can then use this `linked_id` parameter to retrieve all events associated with your custom identifier.
$url = 'url_example'; // string | Filter events by the URL (`url` property) associated with the event.
$bundle_id = 'bundle_id_example'; // string | Filter events by the Bundle ID (iOS) associated with the event.
$package_name = 'package_name_example'; // string | Filter events by the Package Name (Android) associated with the event.
$origin = 'origin_example'; // string | Filter events by the origin field of the event. This is applicable to web events only (e.g., https://example.com)
$start = 1767225600000; // int | Include events that happened after this point (with timestamp greater than or equal the provided `start` Unix milliseconds value). Defaults to 7 days ago. Setting `start` does not change `end`'s default of `now` — adjust it separately if needed.
$end = 1769903999000; // int | Include events that happened before this point (with timestamp less than or equal the provided `end` Unix milliseconds value). Defaults to now. Setting `end` does not change `start`'s default of `7 days ago` — adjust it separately if needed.
$reverse = false; // bool | When `true`, sort events oldest first (ascending timestamp order). Default is newest first (descending timestamp order).
$suspect = True; // bool | Filter events previously tagged as suspicious via the [Update API](https://docs.fingerprint.com/reference/server-api-v4-update-event). > Note: When using this parameter, only events with the `suspect` property explicitly set to `true` or `false` are returned. Events with undefined `suspect` property are left out of the response.
$vpn = True; // bool | Filter events by VPN Detection result. > Note: When using this parameter, only events with the `vpn` property set to `true` or `false` are returned. Events without a `vpn` Smart Signal result are left out of the response.
$virtual_machine = True; // bool | Filter events by Virtual Machine Detection result. > Note: When using this parameter, only events with the `virtual_machine` property set to `true` or `false` are returned. Events without a `virtual_machine` Smart Signal result are left out of the response.
$tampering = True; // bool | Filter events by Browser Tampering Detection result. > Note: When using this parameter, only events with the `tampering.result` property set to `true` or `false` are returned. Events without a `tampering` Smart Signal result are left out of the response.
$anti_detect_browser = True; // bool | Filter events by Anti-detect Browser Detection result. > Note: When using this parameter, only events with the `tampering.anti_detect_browser` property set to `true` or `false` are returned. Events without a `tampering` Smart Signal result are left out of the response.
$incognito = True; // bool | Filter events by Browser Incognito Detection result. > Note: When using this parameter, only events with the `incognito` property set to `true` or `false` are returned. Events without an `incognito` Smart Signal result are left out of the response.
$privacy_settings = True; // bool | Filter events by Privacy Settings Detection result. > Note: When using this parameter, only events with the `privacy_settings` property set to `true` or `false` are returned. Events without a `privacy_settings` Smart Signal result are left out of the response.
$jailbroken = True; // bool | Filter events by Jailbroken Device Detection result. > Note: When using this parameter, only events with the `jailbroken` property set to `true` or `false` are returned. Events without a `jailbroken` Smart Signal result are left out of the response.
$frida = True; // bool | Filter events by Frida Detection result. > Note: When using this parameter, only events with the `frida` property set to `true` or `false` are returned. Events without a `frida` Smart Signal result are left out of the response.
$factory_reset = True; // bool | Filter events by Factory Reset Detection result. > Note: When using this parameter, only events with a `factory_reset` time. Events without a `factory_reset` Smart Signal result are left out of the response.
$cloned_app = True; // bool | Filter events by Cloned App Detection result. > Note: When using this parameter, only events with the `cloned_app` property set to `true` or `false` are returned. Events without a `cloned_app` Smart Signal result are left out of the response.
$emulator = True; // bool | Filter events by Android Emulator Detection result. > Note: When using this parameter, only events with the `emulator` property set to `true` or `false` are returned. Events without an `emulator` Smart Signal result are left out of the response.
$root_apps = True; // bool | Filter events by Rooted Device Detection result. > Note: When using this parameter, only events with the `root_apps` property set to `true` or `false` are returned. Events without a `root_apps` Smart Signal result are left out of the response.
$vpn_confidence = \Fingerprint\ServerSdk\Model\SearchEventsVpnConfidence::MEDIUM; // \Fingerprint\ServerSdk\Model\SearchEventsVpnConfidence | Filter events by VPN Detection result confidence level. `high` - events with high VPN Detection confidence. `medium` - events with medium VPN Detection confidence. `low` - events with low VPN Detection confidence. > Note: When using this parameter, only events with the `vpn.confidence` property set to a valid value are returned. Events without a `vpn` Smart Signal result are left out of the response.
$min_suspect_score = 3.4; // float | Filter events with Suspect Score result above a provided minimum threshold. > Note: When using this parameter, only events where the `suspect_score` property set to a value exceeding your threshold are returned. Events without a `suspect_score` Smart Signal result are left out of the response.
$developer_tools = True; // bool | Filter events by Developer Tools detection result. > Note: When using this parameter, only events with the `developer_tools` property set to `true` or `false` are returned. Events without a `developer_tools` Smart Signal result are left out of the response.
$location_spoofing = True; // bool | Filter events by Location Spoofing detection result. > Note: When using this parameter, only events with the `location_spoofing` property set to `true` or `false` are returned. Events without a `location_spoofing` Smart Signal result are left out of the response.
$mitm_attack = True; // bool | Filter events by MITM (Man-in-the-Middle) Attack detection result. > Note: When using this parameter, only events with the `mitm_attack` property set to `true` or `false` are returned. Events without a `mitm_attack` Smart Signal result are left out of the response.
$rare_device = True; // bool | Filter events by Device Rarity detection result. > Note: When using this parameter, only events with the `rare_device` property set to `true` or `false` are returned. Events without a Device Rarity Smart Signal result are left out of the response.
$rare_device_percentile_bucket = new \Fingerprint\ServerSdk\Model\\Fingerprint\ServerSdk\Model\SearchEventsRareDevicePercentileBucket(); // \Fingerprint\ServerSdk\Model\SearchEventsRareDevicePercentileBucket | Filter events by Device Rarity percentile bucket. `<p95` - device configuration is in the bottom 95% (most common). `p95-p99` - device is in the 95th to 99th percentile. `p99-p99.5` - device is in the 99th to 99.5th percentile. `p99.5-p99.9` - device is in the 99.5th to 99.9th percentile. `p99.9+` - device is in the top 0.1% (rarest). `not_seen` - device configuration has never been observed before.
$proxy = True; // bool | Filter events by Proxy detection result. > Note: When using this parameter, only events with the `proxy` property set to `true` or `false` are returned. Events without a `proxy` Smart Signal result are left out of the response.
$sdk_version = 'sdk_version_example'; // string | Filter events by a specific SDK version associated with the identification event (`sdk.version` property). Example: `3.11.14`
$sdk_platform = \Fingerprint\ServerSdk\Model\SearchEventsSdkPlatform::JS; // \Fingerprint\ServerSdk\Model\SearchEventsSdkPlatform | Filter events by the SDK Platform associated with the identification event (`sdk.platform` property) . `js` - Javascript agent (Web). `ios` - Apple iOS based devices. `android` - Android based devices.
$environment = array('environment_example'); // string[] | Filter for events by providing one or more environment IDs (`environment_id` property).  ### Array syntax To provide multiple environment IDs, use the repeated keys syntax (`environment=env1&environment=env2`). Other notations like comma-separated (`environment=env1,env2`) or bracket notation (`environment[]=env1&environment[]=env2`) are not supported.
$proximity_id = 'proximity_id_example'; // string | Filter events by the most precise Proximity ID provided by default. > Note: When using this parameter, only events with the `proximity.id` property matching the provided ID are returned. Events without a `proximity` result are left out of the response.
$total_hits = 56; // int | When set, the response will include a `total_hits` property with a count of total query matches across all pages, up to the specified limit.
$tor_node = True; // bool | Filter events by Tor Node detection result. > Note: When using this parameter, only events with the `tor_node` property set to `true` or `false` are returned. Events without a `tor_node` detection result are left out of the response.
$incremental_identification_status = \Fingerprint\ServerSdk\Model\SearchEventsIncrementalIdentificationStatus::COMPLETED(); // \Fingerprint\ServerSdk\Model\SearchEventsIncrementalIdentificationStatus | Filter events by their incremental identification status (`incremental_identification_status` property). Non incremental identification events are left out of the response.
$simulator = True; // bool | Filter events by iOS Simulator Detection result.  > Note: When using this parameter, only events with the `simulator` property set to `true` or `false` are returned. Events without a `simulator` Smart Signal result are left out of the response.

try {
    $result = $apiInstance->searchEvents($limit, $pagination_key, $visitor_id, $high_recall_id, $bot, $ip_address, $asn, $linked_id, $url, $bundle_id, $package_name, $origin, $start, $end, $reverse, $suspect, $vpn, $virtual_machine, $tampering, $anti_detect_browser, $incognito, $privacy_settings, $jailbroken, $frida, $factory_reset, $cloned_app, $emulator, $root_apps, $vpn_confidence, $min_suspect_score, $developer_tools, $location_spoofing, $mitm_attack, $rare_device, $rare_device_percentile_bucket, $proxy, $sdk_version, $sdk_platform, $environment, $proximity_id, $total_hits, $tor_node, $incremental_identification_status, $simulator);
    var_dump($result);
} catch (ApiException $e) {
    $errorDetails = $e->getErrorDetails()->getErrorDetails();
    echo "[{$errorDetails->getCode()}] Exception when calling FingerprintApi->searchEvents: {$errorDetails->getMessage()}" . PHP_EOL;
} catch (Exception $e) {
    echo 'Exception when calling FingerprintApi->searchEvents: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **limit** | **int** | Maximum number of events to return. Results are selected from the time range (`start`, `end`), ordered by `reverse`, then truncated to provided `limit` size. So `reverse=true` returns the oldest N=`limit` events, otherwise the newest N=`limit` events. | [optional] [default to 10] |
| **pagination_key** | **string** | Use `pagination_key` to get the next page of results.  When more results are available (e.g., you requested up to 100 results for your query using `limit`, but there are more than 100 events total matching your request), the `pagination_key` field is added to the response. The pagination key is an arbitrary string that should not be interpreted in any way and should be passed as-is. In the following request, use that value in the `pagination_key` parameter to get the next page of results:  1. First request, returning most recent 200 events: `GET api-base-url/events?limit=100` 2. Use `response.pagination_key` to get the next page of results: `GET api-base-url/events?limit=100&pagination_key=1740815825085` | [optional] |
| **visitor_id** | **string** | Unique [visitor identifier](https://docs.fingerprint.com/reference/js-agent-v4-get-function#visitor_id) issued by Fingerprint Identification and all active Smart Signals.  Filter events by matching Visitor ID (`identification.visitor_id` property). | [optional] |
| **high_recall_id** | **string** | The High Recall ID is a supplementary browser identifier designed for use cases that require wider coverage over precision. Compared to the standard visitor ID, the High Recall ID strives to match incoming browsers more generously (rather than precisely) with existing browsers and thus identifies fewer browsers as new. The High Recall ID is best suited for use cases that are sensitive to browsers being identified as new and where mismatched browsers are not detrimental.  Filter events by matching High Recall ID (`supplementary_id_high_recall.visitor_id` property). | [optional] |
| **bot** | [**\Fingerprint\ServerSdk\Model\SearchEventsBot**](../Model/.md) | Filter events by the Bot Detection result, specifically:   `all` - events where any kind of bot was detected.   `good` - events where a good bot was detected.   `bad` - events where a bad bot was detected.   `none` - events where no bot was detected. > Note: When using this parameter, only events with the `bot` property set to a valid value are returned. Events without a `bot` Smart Signal result are left out of the response. | [optional] |
| **ip_address** | **string** | Filter events by IP address or IP range (if CIDR notation is used). If CIDR notation is not used, a /32 for IPv4 or /128 for IPv6 is assumed. Examples of range based queries: 10.0.0.0/24, 192.168.0.1/32 | [optional] |
| **asn** | **string** | Filter events by the ASN associated with the event's IP address. This corresponds to the `ip_info.(v4|v6).asn` property in the response. | [optional] |
| **linked_id** | **string** | Filter events by your custom identifier.  You can use [linked Ids](https://docs.fingerprint.com/reference/js-agent-v4-get-function#linkedid) to associate identification requests with your own identifier, for example, session Id, purchase Id, or transaction Id. You can then use this `linked_id` parameter to retrieve all events associated with your custom identifier. | [optional] |
| **url** | **string** | Filter events by the URL (`url` property) associated with the event. | [optional] |
| **bundle_id** | **string** | Filter events by the Bundle ID (iOS) associated with the event. | [optional] |
| **package_name** | **string** | Filter events by the Package Name (Android) associated with the event. | [optional] |
| **origin** | **string** | Filter events by the origin field of the event. This is applicable to web events only (e.g., https://example.com) | [optional] |
| **start** | **int** | Include events that happened after this point (with timestamp greater than or equal the provided `start` Unix milliseconds value). Defaults to 7 days ago. Setting `start` does not change `end`'s default of `now` — adjust it separately if needed. | [optional] |
| **end** | **int** | Include events that happened before this point (with timestamp less than or equal the provided `end` Unix milliseconds value). Defaults to now. Setting `end` does not change `start`'s default of `7 days ago` — adjust it separately if needed. | [optional] |
| **reverse** | **bool** | When `true`, sort events oldest first (ascending timestamp order). Default is newest first (descending timestamp order). | [optional] [default to false] |
| **suspect** | **bool** | Filter events previously tagged as suspicious via the [Update API](https://docs.fingerprint.com/reference/server-api-v4-update-event). > Note: When using this parameter, only events with the `suspect` property explicitly set to `true` or `false` are returned. Events with undefined `suspect` property are left out of the response. | [optional] |
| **vpn** | **bool** | Filter events by VPN Detection result. > Note: When using this parameter, only events with the `vpn` property set to `true` or `false` are returned. Events without a `vpn` Smart Signal result are left out of the response. | [optional] |
| **virtual_machine** | **bool** | Filter events by Virtual Machine Detection result. > Note: When using this parameter, only events with the `virtual_machine` property set to `true` or `false` are returned. Events without a `virtual_machine` Smart Signal result are left out of the response. | [optional] |
| **tampering** | **bool** | Filter events by Browser Tampering Detection result. > Note: When using this parameter, only events with the `tampering.result` property set to `true` or `false` are returned. Events without a `tampering` Smart Signal result are left out of the response. | [optional] |
| **anti_detect_browser** | **bool** | Filter events by Anti-detect Browser Detection result. > Note: When using this parameter, only events with the `tampering.anti_detect_browser` property set to `true` or `false` are returned. Events without a `tampering` Smart Signal result are left out of the response. | [optional] |
| **incognito** | **bool** | Filter events by Browser Incognito Detection result. > Note: When using this parameter, only events with the `incognito` property set to `true` or `false` are returned. Events without an `incognito` Smart Signal result are left out of the response. | [optional] |
| **privacy_settings** | **bool** | Filter events by Privacy Settings Detection result. > Note: When using this parameter, only events with the `privacy_settings` property set to `true` or `false` are returned. Events without a `privacy_settings` Smart Signal result are left out of the response. | [optional] |
| **jailbroken** | **bool** | Filter events by Jailbroken Device Detection result. > Note: When using this parameter, only events with the `jailbroken` property set to `true` or `false` are returned. Events without a `jailbroken` Smart Signal result are left out of the response. | [optional] |
| **frida** | **bool** | Filter events by Frida Detection result. > Note: When using this parameter, only events with the `frida` property set to `true` or `false` are returned. Events without a `frida` Smart Signal result are left out of the response. | [optional] |
| **factory_reset** | **bool** | Filter events by Factory Reset Detection result. > Note: When using this parameter, only events with a `factory_reset` time. Events without a `factory_reset` Smart Signal result are left out of the response. | [optional] |
| **cloned_app** | **bool** | Filter events by Cloned App Detection result. > Note: When using this parameter, only events with the `cloned_app` property set to `true` or `false` are returned. Events without a `cloned_app` Smart Signal result are left out of the response. | [optional] |
| **emulator** | **bool** | Filter events by Android Emulator Detection result. > Note: When using this parameter, only events with the `emulator` property set to `true` or `false` are returned. Events without an `emulator` Smart Signal result are left out of the response. | [optional] |
| **root_apps** | **bool** | Filter events by Rooted Device Detection result. > Note: When using this parameter, only events with the `root_apps` property set to `true` or `false` are returned. Events without a `root_apps` Smart Signal result are left out of the response. | [optional] |
| **vpn_confidence** | [**\Fingerprint\ServerSdk\Model\SearchEventsVpnConfidence**](../Model/.md) | Filter events by VPN Detection result confidence level. `high` - events with high VPN Detection confidence. `medium` - events with medium VPN Detection confidence. `low` - events with low VPN Detection confidence. > Note: When using this parameter, only events with the `vpn.confidence` property set to a valid value are returned. Events without a `vpn` Smart Signal result are left out of the response. | [optional] |
| **min_suspect_score** | **float** | Filter events with Suspect Score result above a provided minimum threshold. > Note: When using this parameter, only events where the `suspect_score` property set to a value exceeding your threshold are returned. Events without a `suspect_score` Smart Signal result are left out of the response. | [optional] |
| **developer_tools** | **bool** | Filter events by Developer Tools detection result. > Note: When using this parameter, only events with the `developer_tools` property set to `true` or `false` are returned. Events without a `developer_tools` Smart Signal result are left out of the response. | [optional] |
| **location_spoofing** | **bool** | Filter events by Location Spoofing detection result. > Note: When using this parameter, only events with the `location_spoofing` property set to `true` or `false` are returned. Events without a `location_spoofing` Smart Signal result are left out of the response. | [optional] |
| **mitm_attack** | **bool** | Filter events by MITM (Man-in-the-Middle) Attack detection result. > Note: When using this parameter, only events with the `mitm_attack` property set to `true` or `false` are returned. Events without a `mitm_attack` Smart Signal result are left out of the response. | [optional] |
| **rare_device** | **bool** | Filter events by Device Rarity detection result. > Note: When using this parameter, only events with the `rare_device` property set to `true` or `false` are returned. Events without a Device Rarity Smart Signal result are left out of the response. | [optional] |
| **rare_device_percentile_bucket** | [**\Fingerprint\ServerSdk\Model\SearchEventsRareDevicePercentileBucket**](../Model/.md) | Filter events by Device Rarity percentile bucket. `<p95` - device configuration is in the bottom 95% (most common). `p95-p99` - device is in the 95th to 99th percentile. `p99-p99.5` - device is in the 99th to 99.5th percentile. `p99.5-p99.9` - device is in the 99.5th to 99.9th percentile. `p99.9+` - device is in the top 0.1% (rarest). `not_seen` - device configuration has never been observed before. | [optional] |
| **proxy** | **bool** | Filter events by Proxy detection result. > Note: When using this parameter, only events with the `proxy` property set to `true` or `false` are returned. Events without a `proxy` Smart Signal result are left out of the response. | [optional] |
| **sdk_version** | **string** | Filter events by a specific SDK version associated with the identification event (`sdk.version` property). Example: `3.11.14` | [optional] |
| **sdk_platform** | [**\Fingerprint\ServerSdk\Model\SearchEventsSdkPlatform**](../Model/.md) | Filter events by the SDK Platform associated with the identification event (`sdk.platform` property) . `js` - Javascript agent (Web). `ios` - Apple iOS based devices. `android` - Android based devices. | [optional] |
| **environment** | [**string[]**](../Model/string.md) | Filter for events by providing one or more environment IDs (`environment_id` property).  ### Array syntax To provide multiple environment IDs, use the repeated keys syntax (`environment=env1&environment=env2`). Other notations like comma-separated (`environment=env1,env2`) or bracket notation (`environment[]=env1&environment[]=env2`) are not supported. | [optional] |
| **proximity_id** | **string** | Filter events by the most precise Proximity ID provided by default. > Note: When using this parameter, only events with the `proximity.id` property matching the provided ID are returned. Events without a `proximity` result are left out of the response. | [optional] |
| **total_hits** | **int** | When set, the response will include a `total_hits` property with a count of total query matches across all pages, up to the specified limit. | [optional] |
| **tor_node** | **bool** | Filter events by Tor Node detection result. > Note: When using this parameter, only events with the `tor_node` property set to `true` or `false` are returned. Events without a `tor_node` detection result are left out of the response. | [optional] |
| **incremental_identification_status** | [**\Fingerprint\ServerSdk\Model\SearchEventsIncrementalIdentificationStatus**](../Model/.md) | Filter events by their incremental identification status (`incremental_identification_status` property). Non incremental identification events are left out of the response. | [optional] |
| **simulator** | **bool** | Filter events by iOS Simulator Detection result.  > Note: When using this parameter, only events with the `simulator` property set to `true` or `false` are returned. Events without a `simulator` Smart Signal result are left out of the response. | [optional] |

### Return type

[**\Fingerprint\ServerSdk\Model\EventSearch**](../Model/EventSearch.md)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)

# **updateEvent**
> updateEvent($event_id, $event_update)

Update an event

Change information in existing events specified by `event_id` or *flag suspicious events*.

When an event is created, it can be assigned `linked_id` and `tags` submitted through the JS agent parameters. 
This information might not have been available on the client initially, so the Server API permits updating these attributes after the fact.

**Warning** It's not possible to update events older than one month. 

**Warning** Trying to update an event immediately after creation may temporarily result in an 
error (HTTP 409 Conflict. The event is not mutable yet.) as the event is fully propagated across our systems. In such a case, simply retry the request.


### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Fingerprint\ServerSdk\Api\FingerprintApi;
use Fingerprint\ServerSdk\Configuration;
use Fingerprint\ServerSdk\ApiException;

const API_SECRET = "Your Fingerprint Secret API Key"; // Fingerprint Secret API Key

// Create new Configuration instance with API Secret and the Region
$config = new Configuration(API_SECRET, Configuration::REGION_EUROPE);
$apiInstance = new FingerprintApi(
    $config,
    // If you want to use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

$event_id = 'event_id_example'; // string | The unique event [identifier](https://docs.fingerprint.com/reference/js-agent-v4-get-function#event_id).
$event_update = new \Fingerprint\ServerSdk\Model\EventUpdate(); // \Fingerprint\ServerSdk\Model\EventUpdate

try {
    $apiInstance->updateEvent($event_id, $event_update);
} catch (ApiException $e) {
    $errorDetails = $e->getErrorDetails()->getErrorDetails();
    echo "[{$errorDetails->getCode()}] Exception when calling FingerprintApi->updateEvent: {$errorDetails->getMessage()}" . PHP_EOL;
} catch (Exception $e) {
    echo 'Exception when calling FingerprintApi->updateEvent: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **event_id** | **string** | The unique event [identifier](https://docs.fingerprint.com/reference/js-agent-v4-get-function#event_id). | |
| **event_update** | [**\Fingerprint\ServerSdk\Model\EventUpdate**](../Model/EventUpdate.md) |  | |

### Return type

void (empty response body)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#documentation-for-models)
[[Back to README]](../../README.md)