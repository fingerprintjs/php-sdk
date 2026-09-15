# Fingerprint Pro Server PHP SDK

## 6.13.1

### Patch Changes

- Fixed `request_id`/`visitor_id` values of exactly `.` or `..` being collapsed by curl's URL normalization before the request is sent, causing `getEvent`, `updateEvent`, `deleteVisitorData`, and `getVisits` to hit the wrong endpoint instead of the requested resource. ([8df3e94](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/8df3e94505ea6a19ba0a33af2f827e2d7f875d03))

## 6.13.0

### Minor Changes

- Handle additional error responses:

  - `getEvent`: `429` and `504`
  - `searchEvents`: `404`, `429`, and `504`
  - `getVisits`: `404` and `504`

  Add `REQUEST_READ_TIMEOUT` to the `ErrorCode` enum. ([a523516](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/a52351675be2c0060549a8eaa0403cebd594c256))

- Change the default value of the `reverse` parameter on `GET /events/search` to `false` and clarify its description. ([a523516](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/a52351675be2c0060549a8eaa0403cebd594c256))
- Limit `VisitorsGetResponse.visits` to a maximum of one item and clarify that `GET /visitors/{visitor_id}` is deprecated in favor of `GET /events/search`. ([a523516](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/a52351675be2c0060549a8eaa0403cebd594c256))
- Require the `label` field on entries in the `labels` Smart Signal response. ([a523516](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/a52351675be2c0060549a8eaa0403cebd594c256))
- Clarify the `ProxyDetails` `proxyType` description to list the `residential`, `data_center`, and `unknown` proxy types explicitly. ([a523516](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/a52351675be2c0060549a8eaa0403cebd594c256))

## 6.12.0

### Minor Changes

- Add the Labels Smart Signal. The `Products` response now exposes a `labels` field (`ProductLabels`) whose `data` is an array of label predictions (`label`, `prediction`, `mlScore`). ([7564735](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/756473574154d916cf9adf941cfb2460b71de8d4))
- Add machine learning-based Smart Signals from the Server API schema: `mlScore` on the `VPN` and `Proxy` results (and their webhook counterparts), and `mlPrediction` on `VPNMethods`. These signals are in beta and only available to select customers. ([9253a67](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9253a679d06a8564248d206a28809ef52750c801))
- Add the `unknown` value to the `ProxyDetails` `proxyType` field. ([c748eff](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/c748effe1d4cccdac672a1c273b4209da886dbab))
- Clarify the `DeveloperTools` and `WebhookDeveloperTools` `result` description to note that it also covers iOS devices with Developer Tools enabled. ([6216a26](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/6216a263c9adfb92effa46b4584626d5a593d654))

## 6.11.0

### Minor Changes

- **Deprecated:** `fingerprint/fingerprint-pro-server-api-sdk` uses Server API v3, which is deprecated. Please migrate to the new [`fingerprint/server-sdk`](https://github.com/fingerprintjs/php-sdk) package which uses Server API v4. ([941be0c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/941be0cbfc07a1879cd4aac93f7270db5a13352e))
- - Add `rareDevice` Smart Signal for detecting rare or previously unseen devices.
  - **events-search**: Add `rare_device` and `rare_device_percentile_bucket` filter parameters.
  - Add `type` property to `IPInfoASN`.
  - Add `integrations` property to the `SDK` model.
  - Add `confidence` and `mlScore` properties to the `Tampering` signal.
  - Add `mlScore` property to the `VirtualMachine` signal.
  - Add `WORKSPACE_SCOPED_SECRET_KEY_REQUIRED` to the `ErrorCode` enum.
  - **webhook**: Add `rareDevice` property to the Webhook schema.
  - **webhook**: Add `confidence` and `mlScore` properties to `WebhookTampering`.
  - **webhook**: Add `mlScore` property to `WebhookVirtualMachine`. ([0abc29f](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/0abc29fc4e8a2bfbcecb10b287367ae6250cc59f))

## 6.11.0-beta.0

### Minor Changes

- **Deprecated:** `fingerprint/fingerprint-pro-server-api-sdk` uses Server API v3, which is deprecated. Please migrate to the new [`fingerprint/server-sdk`](https://github.com/fingerprintjs/php-sdk) package which uses Server API v4. ([941be0c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/941be0cbfc07a1879cd4aac93f7270db5a13352e))
- - Add `rareDevice` Smart Signal for detecting rare or previously unseen devices.
  - **events-search**: Add `rare_device` and `rare_device_percentile_bucket` filter parameters.
  - Add `type` property to `IPInfoASN`.
  - Add `integrations` property to the `SDK` model.
  - Add `confidence` and `mlScore` properties to the `Tampering` signal.
  - Add `mlScore` property to the `VirtualMachine` signal.
  - Add `WORKSPACE_SCOPED_SECRET_KEY_REQUIRED` to the `ErrorCode` enum.
  - **webhook**: Add `rareDevice` property to the Webhook schema.
  - **webhook**: Add `confidence` and `mlScore` properties to `WebhookTampering`.
  - **webhook**: Add `mlScore` property to `WebhookVirtualMachine`. ([0abc29f](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/0abc29fc4e8a2bfbcecb10b287367ae6250cc59f))

## 6.10.0

### Minor Changes

- **events-search**: Event search now supports a new set of filter parameters: `developer_tools`, `location_spoofing`, `mitm_attack`, `proxy`, `sdk_version`, `sdk_platform`, `environment` ([f385bfa](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/f385bfa2a4423f7c665959a43e444a394d2a3883))
- **webhook**: Add `supplementaryIds` property to the Webhooks schema. ([f385bfa](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/f385bfa2a4423f7c665959a43e444a394d2a3883))
- Add `proximity` signal that represents a fixed geographical zone in a discrete global grid within which the device is observed. ([47a43dc](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/47a43dc76489ab186c0d10b3b85c8b840a6861c8))
- Add `environmentId` property to `identification` ([f385bfa](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/f385bfa2a4423f7c665959a43e444a394d2a3883))

## 6.10.0-develop.1

### Minor Changes

- Add `proximity` signal that represents a fixed geographical zone in a discrete global grid within which the device is observed. ([47a43dc](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/47a43dc76489ab186c0d10b3b85c8b840a6861c8))

## 6.10.0-develop.0

### Minor Changes

- **events-search**: Event search now supports a new set of filter parameters: `developer_tools`, `location_spoofing`, `mitm_attack`, `proxy`, `sdk_version`, `sdk_platform`, `environment` ([f385bfa](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/f385bfa2a4423f7c665959a43e444a394d2a3883))
- **webhook**: Add `supplementaryIds` property to the Webhooks schema. ([f385bfa](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/f385bfa2a4423f7c665959a43e444a394d2a3883))
- Add `environmentId` property to `identification` ([f385bfa](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/f385bfa2a4423f7c665959a43e444a394d2a3883))

## 6.9.0

### Minor Changes

- Add `details` object to the `proxy` signal. This field includes the `type` of the detected proxy (`residential` or `data_center`) and the `lastSeenAt` timestamp of when an IP was last observed to show proxy-like behavior. ([e7f492e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e7f492e1b5e13607f154300e0468c30a753f96c4))

## 6.8.0

### Minor Changes

- Mark `replayed` field required in the `identification` product schema. This field will always be present. ([b12c11e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/b12c11e1b2fb63cb474f8167c4478f3bda13257a))
- Add `sdk` field with platform metadata to `identification` ([b12c11e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/b12c11e1b2fb63cb474f8167c4478f3bda13257a))

### Patch Changes

- Deprecate the Remote Control Detection Smart Signal. This signal is no longer available. ([b12c11e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/b12c11e1b2fb63cb474f8167c4478f3bda13257a))

## 6.8.0-develop.0

### Minor Changes

- Mark `replayed` field required in the `identification` product schema. This field will always be present. ([b12c11e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/b12c11e1b2fb63cb474f8167c4478f3bda13257a))
- Add `sdk` field with platform metadata to `identification` ([b12c11e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/b12c11e1b2fb63cb474f8167c4478f3bda13257a))

### Patch Changes

- Deprecate the Remote Control Detection Smart Signal. This signal is no longer available. ([b12c11e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/b12c11e1b2fb63cb474f8167c4478f3bda13257a))

## 6.7.0

### Minor Changes

- add `replayed` field to `identification` in Events and Webhooks ([098df62](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/098df620ddab449988e3ff9e1b5fc787ba0d622f))

## 6.6.0

### Minor Changes

- Add `confidence` property to the Proxy detection Smart Signal, which now supports both residential and public web proxies. ([c370f3c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/c370f3c832e01b0b518cebc021f3d518c3d6fa13))

## 6.5.0

### Minor Changes

- **events-search**: Event search now supports a new set of filter parameters: `vpn`, `virtual_machine`, `tampering`, `anti_detect_browser`, `incognito`, `privacy_settings`, `jailbroken`, `frida`, `factory_reset`, `cloned_app`, `emulator`, `root_apps`, `vpn_confidence`, `min_suspect_score`. ([9ec63ea](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9ec63ea694ecb71ce002e9243513947e131bc27c))
- **events-search**: Event search now supports two new filter parameters: `ip_blocklist`, `datacenter` ([e2b6aba](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e2b6aba413642c18526a4f0cf06f07de38353c35))

### Patch Changes

- **webhook**: Apply x-flatten-optional-params: true in correct place in the webhook.yaml ([9ec63ea](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9ec63ea694ecb71ce002e9243513947e131bc27c))
- **events**: Update Tampering descriptions to reflect Android support. ([9ec63ea](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9ec63ea694ecb71ce002e9243513947e131bc27c))
- **webhook**: Add `environmentId` property ([9ec63ea](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9ec63ea694ecb71ce002e9243513947e131bc27c))

## 6.5.0-develop.0

### Minor Changes

- **events-search**: Event search now supports a new set of filter parameters: `vpn`, `virtual_machine`, `tampering`, `anti_detect_browser`, `incognito`, `privacy_settings`, `jailbroken`, `frida`, `factory_reset`, `cloned_app`, `emulator`, `root_apps`, `vpn_confidence`, `min_suspect_score`. ([9ec63ea](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9ec63ea694ecb71ce002e9243513947e131bc27c))
- **events-search**: Event search now supports two new filter parameters: `ip_blocklist`, `datacenter` ([e2b6aba](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e2b6aba413642c18526a4f0cf06f07de38353c35))

### Patch Changes

- **webhook**: Apply x-flatten-optional-params: true in correct place in the webhook.yaml ([9ec63ea](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9ec63ea694ecb71ce002e9243513947e131bc27c))
- **events**: Update Tampering descriptions to reflect Android support. ([9ec63ea](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9ec63ea694ecb71ce002e9243513947e131bc27c))
- **webhook**: Add `environmentId` property ([9ec63ea](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9ec63ea694ecb71ce002e9243513947e131bc27c))

## 6.4.0

### Minor Changes

- Add `mitmAttack` (man-in-the-middle attack) Smart Signal. ([ac79de9](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/ac79de9c5d4b521dc47bc2acb891d2fa8bce98bb))

## 6.3.0

### Minor Changes

- **events-search**: Add a new `events/search` API endpoint. Allow users to search for identification events matching one or more search criteria, for example, visitor ID, IP address, bot detection result, etc. ([3a45444](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/3a4544423f6307973560710181f0d47d4c9261f5))

## 6.3.0-develop.1

### Minor Changes

- **events-search**: Add 'pagination_key' parameter ([35d2807](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/35d28071197aad1ef5c3e53250350d70c1a8184b))

### Patch Changes

- **events-search**: Improve parameter descriptions for `bot`, `suspect` ([99481d8](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/99481d89e2eac9f840abff463785d6d1738d4e13))

## 6.3.0-develop.0

### Minor Changes

- **events-search**: Add a new `events/search` API endpoint. Allow users to search for identification events matching one or more search criteria, for example, visitor ID, IP address, bot detection result, etc. ([3a45444](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/3a4544423f6307973560710181f0d47d4c9261f5))

## 6.2.1

### Patch Changes

- Fix problem with empty `Sibdivisions` array in the `Geolocation` model ([ebd29b3](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/ebd29b384545d2261d0f6e7fa0eddd5689638ca2))
- Fix scalar values serialization (affects `EventsUpdateRequest`) ([123ca07](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/123ca07e6e8fc65b2a1bf7e8b736761fcb900e9e))

## 6.2.0

### Minor Changes

- Add Related Visitors API ([4f3030b](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/4f3030b9f0e13333279d4fe38469a5d4a0867654))

## 6.1.0

### Minor Changes

- Add `relay` detection method to the VPN Detection Smart Signal ([afcf2e9](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/afcf2e9c018490e35c249688d4784fa856853b88))
- **events**: Add a `suspect` field to the `identification` product schema ([afcf2e9](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/afcf2e9c018490e35c249688d4784fa856853b88))

## 6.0.0

The underlying Server API hasn’t changed, but we made SDK type and class generation more precise, resulting in small breaking changes for the SDK itself. This change should make the SDK API a lot more stable going forward

### Major Changes

- - Remove the `BrowserDetails` field `botProbability`.
  - Update the `IdentificationConfidence` field `score` type format: `float` -> `double`.
  - Make the `RawDeviceAttributeError` field `name` **optional** .
  - Make the `RawDeviceAttributeError` field `message` **optional** .
  - **events**: Remove the `EventsResponse` field `error`.
    - [note]: The errors are represented by `ErrorResponse` model.
  - **events**: Update the `HighActivity` field `dailyRequests` type format: `number` -> `int64`.
  - **events**: Specify the `Tampering` field `anomalyScore` type format: `double`.
  - **webhook**: Make the `Webhook` fields **optional**: `visitorId`, `visitorFound`, `firstSeenAt`, `lastSeenAt`, `browserDetails`, `incognito`.
  - **webhook**: Make the `WebhookClonedApp` field `result` **optional**.
  - **webhook**: Make the `WebhookDeveloperTools` field `result` **optional**.
  - **webhook**: Make the `WebhookEmulator` field `result` **optional**.
  - **webhook**: Make the `WebhookFactoryReset` fields `time` and `timestamp` **optional**.
  - **webhook**: Make the `WebhookFrida` field `result` **optional**.
  - **webhook**: Update the `WebhookHighActivity` field `dailyRequests` type format: `number` -> `int64`.
  - **webhook**: Make the `WebhookIPBlocklist` fields `result` and `details` **optional**.
  - **webhook**: Make the `WebhookJailbroken` field `result` **optional**.
  - **webhook**: Make the `WebhookLocationSpoofing` field `result` **optional**.
  - **webhook**: Make the `WebhookPrivacySettings` field `result` **optional**.
  - **webhook**: Make the `WebhookProxy` field `result` **optional**.
  - **webhook**: Make the `WebhookRemoteControl` field `result` **optional**.
  - **webhook**: Make the `WebhookRootApps` field `result` **optional**.
  - **webhook**: Make the `WebhookSuspectScore` field `result` **optional**.
  - **webhook**: Make the `WebhookTampering` fields `result`, `anomalyScore` and `antiDetectBrowser` **optional**.
  - **webhook**: Specify the `WebhookTampering` field `anomalyScore` type format: `double`.
  - **webhook**: Make the `WebhookTor` field `result` **optional**.
  - **webhook**: Make the `WebhookVelocity` fields **optional**: `distinctIp`, `distinctLinkedId`, `distinctCountry`, `events`, `ipEvents`, `distinctIpByLinkedId`, `distinctVisitorIdByLinkedId`.
  - **webhook**: Make the `WebhookVirtualMachine` field `result` **optional**.
  - **webhook**: Make the `WebhookVPN` fields **optional**: `result`, `confidence`, `originTimezone`, `methods`. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))
- - Rename `BotdResult` -> `Botd`.
  - Rename `BotdDetectionResult` -> `BotdBot`:
    - Extract `result` type as `BotdBotResult`.
  - Rename `ClonedAppResult` -> `ClonedApp`.
  - Rename `DeveloperToolsResult` -> `DeveloperTools`.
  - Rename `EmulatorResult` -> `Emulator`.
  - Refactor error models:
    - Remove `ErrorCommon403Response`, `ErrorCommon429Response`, `ErrorEvent404Response`, `TooManyRequestsResponse`, `ErrorVisits403`, `ErrorUpdateEvent400Response`, `ErrorUpdateEvent409Response`, `ErrorVisitor400Response`, `ErrorVisitor404Response`, `IdentificationError`, `ProductError`.
    - Introduce `ErrorResponse` and `ErrorPlainResponse`.
      - [note]: `ErrorPlainResponse` has a different format `{ "error": string }` and it is used only in `GET /visitors`.
    - Extract `error` type as `Error`.
    - Extract `error.code` type as `ErrorCode`.
  - Rename `EventResponse` -> `EventsGetResponse`.
  - Rename `EventUpdateRequest` -> `EventsUpdateRequest`.
  - Rename `FactoryResetResult` -> `FactoryReset`.
  - Rename `FridaResult` -> `Frida`.
  - Rename `IPLocation` -> `Geolocation`:
    - Rename `IPLocationCity` -> `GeolocationCity`.
    - Extract `subdivisions` type as `GeolocationSubdivisions`.
    - Rename `Location` -> `GeolocationContinent`:
    - Introduce a dedicated type `GeolocationCountry`.
    - Rename `Subdivision` -> `GeolocationSubdivision`.
  - Rename `HighActivityResult` -> `HighActivity`.
  - Rename `Confidence` -> `IdentificationConfidence`.
  - Rename `SeenAt` -> `IdentificationSeenAt`.
  - Rename `IncognitoResult` -> `Incognito`.
  - Rename `IpBlockListResult` -> `IPBlocklist`:
    - Extract `details` type as `IPBlocklistDetails`.
  - Rename `IpInfoResult` -> `IPInfo`:
    - Rename `IpInfoResultV4` -> `IPInfoV4`.
    - Rename `IpInfoResultV6` -> `IPInfoV6`.
    - Rename `ASN` -> `IPInfoASN`.
    - Rename `DataCenter` -> `IPInfoDataCenter`.
  - Rename `JailbrokenResult` -> `Jailbroken`.
  - Rename `LocationSpoofingResult` -> `LocationSpoofing`.
  - Rename `PrivacySettingsResult` -> `PrivacySettings`.
  - Rename `ProductsResponse` -> `Products`:
    - Rename inner types: `ProductsResponseIdentification` -> `ProductIdentification`, `ProductsResponseIdentificationData` -> `Identification`, `ProductsResponseBotd` -> `ProductBotd`, `SignalResponseRootApps` -> `ProductRootApps`, `SignalResponseEmulator` -> `ProductEmulator`, `SignalResponseIpInfo` -> `ProductIPInfo`, `SignalResponseIpBlocklist` -> `ProductIPBlocklist`, `SignalResponseTor` -> `ProductTor`, `SignalResponseVpn` -> `ProductVPN`, `SignalResponseProxy` -> `ProductProxy`, `ProxyResult` -> `Proxy`, `SignalResponseIncognito` -> `ProductIncognito`, `SignalResponseTampering` -> `ProductTampering`, `SignalResponseClonedApp` -> `ProductClonedApp`, `SignalResponseFactoryReset` -> `ProductFactoryReset`, `SignalResponseJailbroken` -> `ProductJailbroken`, `SignalResponseFrida` -> `ProductFrida`, `SignalResponsePrivacySettings` -> `ProductPrivacySettings`, `SignalResponseVirtualMachine` -> `ProductVirtualMachine`, `SignalResponseRawDeviceAttributes` -> `ProductRawDeviceAttributes`, `RawDeviceAttributesResultValue` -> `RawDeviceAttributes`, `SignalResponseHighActivity` -> `ProductHighActivity`, `SignalResponseLocationSpoofing` -> `ProductLocationSpoofing`, `SignalResponseSuspectScore` -> `ProductSuspectScore`, `SignalResponseRemoteControl` -> `ProductRemoteControl`, `SignalResponseVelocity` -> `ProductVelocity`, `SignalResponseDeveloperTools` -> `ProductDeveloperTools`.
    - Extract `identification.data` type as `Identification`.
  - Use PHP `array` instead of `RawDeviceAttributesResult`
  - Rename `RemoteControlResult` -> `RemoteControl`.
  - Rename `RootAppsResult` -> `RootApps`.
  - Rename `SuspectScoreResult` -> `SuspectScore`.
  - Rename `TamperingResult` -> `Tampering`.
  - Rename `TorResult` -> `Tor`.
  - Rename `VelocityResult` -> `Velocity`:
    - Rename `VelocityIntervals` -> `VelocityData`.
    - Rename `VelocityIntervalResult` -> `VelocityIntervals`.
  - Rename `VirtualMachineResult` -> `VirtualMachine`.
  - Rename the `Visit` field `ipLocation` type `DeprecatedIPLocation` -> `DeprecatedGeolocation`.
    - Instead of `DeprecatedIPLocationCity` use common `GeolocationCity`
  - Rename `Response` -> `VisitorsGetResponse`.
    - Omit extra inner type `ResponseVisits`
  - Rename `VpnResult` -> `VPN`.
    - Extract `confidence` type as `VPNConfidence`.
    - Extract `methods` type as `VPNMethods`.
  - Rename `WebhookVisit` -> `Webhook`.
    - Introduce new inner types: `WebhookRootApps`, `WebhookEmulator`, `WebhookIPInfo`, `WebhookIPBlocklist`, `WebhookTor`, `WebhookVPN`, `WebhookProxy`, `WebhookTampering`, `WebhookClonedApp`, `WebhookFactoryReset`, `WebhookJailbroken`, `WebhookFrida`, `WebhookPrivacySettings`, `WebhookVirtualMachine`, `WebhookHighActivity`, `WebhookLocationSpoofing`, `WebhookSuspectScore`, `WebhookRemoteControl`, `WebhookVelocity`, `WebhookDeveloperTools`. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))

### Minor Changes

- Added new `ipEvents`, `distinctIpByLinkedId`, and `distinctVisitorIdByLinkedId` fields to the `velocity` Smart Signal. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))
- - Make the `GeolocationCity` field `name` **required**.
  - Make the `GeolocationSubdivision` field `isoCode` **required**.
  - Make the `GeolocationSubdivision` field `name` **required**.
  - Make the `IPInfoASN` field `name` **required** .
  - Make the `IPInfoDataCenter` field `name` **required**.
  - Add **optional** `IdentificationConfidence` field `comment`.
  - **events**: Add **optional** `Botd` field `meta`.
  - **events**: Add **optional** `Identification` field `components`.
  - **events**: Make the `VPN` field `originCountry` **required**.
  - **visitors**: Add **optional** `Visit` field `components`.
  - **webhook**: Add **optional** `Webhook` field `components`. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))
- Remove `ipv4` format from `ip` field in `Botd`, `Identification`, `Visit` and `Webhook` models. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))
- **events**: Add `antiDetectBrowser` detection method to the `tampering` Smart Signal. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))

## 6.0.0-rc.0

### Major Changes

- - Remove the `BrowserDetails` field `botProbability`.
  - Update the `IdentificationConfidence` field `score` type format: `float` -> `double`.
  - Make the `RawDeviceAttributeError` field `name` **optional** .
  - Make the `RawDeviceAttributeError` field `message` **optional** .
  - **events**: Remove the `EventsResponse` field `error`.
    - [note]: The errors are represented by `ErrorResponse` model.
  - **events**: Update the `HighActivity` field `dailyRequests` type format: `number` -> `int64`.
  - **events**: Specify the `Tampering` field `anomalyScore` type format: `double`.
  - **webhook**: Make the `Webhook` fields **optional**: `visitorId`, `visitorFound`, `firstSeenAt`, `lastSeenAt`, `browserDetails`, `incognito`.
  - **webhook**: Make the `WebhookClonedApp` field `result` **optional**.
  - **webhook**: Make the `WebhookDeveloperTools` field `result` **optional**.
  - **webhook**: Make the `WebhookEmulator` field `result` **optional**.
  - **webhook**: Make the `WebhookFactoryReset` fields `time` and `timestamp` **optional**.
  - **webhook**: Make the `WebhookFrida` field `result` **optional**.
  - **webhook**: Update the `WebhookHighActivity` field `dailyRequests` type format: `number` -> `int64`.
  - **webhook**: Make the `WebhookIPBlocklist` fields `result` and `details` **optional**.
  - **webhook**: Make the `WebhookJailbroken` field `result` **optional**.
  - **webhook**: Make the `WebhookLocationSpoofing` field `result` **optional**.
  - **webhook**: Make the `WebhookPrivacySettings` field `result` **optional**.
  - **webhook**: Make the `WebhookProxy` field `result` **optional**.
  - **webhook**: Make the `WebhookRemoteControl` field `result` **optional**.
  - **webhook**: Make the `WebhookRootApps` field `result` **optional**.
  - **webhook**: Make the `WebhookSuspectScore` field `result` **optional**.
  - **webhook**: Make the `WebhookTampering` fields `result`, `anomalyScore` and `antiDetectBrowser` **optional**.
  - **webhook**: Specify the `WebhookTampering` field `anomalyScore` type format: `double`.
  - **webhook**: Make the `WebhookTor` field `result` **optional**.
  - **webhook**: Make the `WebhookVelocity` fields **optional**: `distinctIp`, `distinctLinkedId`, `distinctCountry`, `events`, `ipEvents`, `distinctIpByLinkedId`, `distinctVisitorIdByLinkedId`.
  - **webhook**: Make the `WebhookVirtualMachine` field `result` **optional**.
  - **webhook**: Make the `WebhookVPN` fields **optional**: `result`, `confidence`, `originTimezone`, `methods`. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))
- - Rename `BotdResult` -> `Botd`.
  - Rename `BotdDetectionResult` -> `BotdBot`:
    - Extract `result` type as `BotdBotResult`.
  - Rename `ClonedAppResult` -> `ClonedApp`.
  - Rename `DeveloperToolsResult` -> `DeveloperTools`.
  - Rename `EmulatorResult` -> `Emulator`.
  - Refactor error models:
    - Remove `ErrorCommon403Response`, `ErrorCommon429Response`, `ErrorEvent404Response`, `TooManyRequestsResponse`, `ErrorVisits403`, `ErrorUpdateEvent400Response`, `ErrorUpdateEvent409Response`, `ErrorVisitor400Response`, `ErrorVisitor404Response`, `IdentificationError`, `ProductError`.
    - Introduce `ErrorResponse` and `ErrorPlainResponse`.
      - [note]: `ErrorPlainResponse` has a different format `{ "error": string }` and it is used only in `GET /visitors`.
    - Extract `error` type as `Error`.
    - Extract `error.code` type as `ErrorCode`.
  - Rename `EventResponse` -> `EventsGetResponse`.
  - Rename `EventUpdateRequest` -> `EventsUpdateRequest`.
  - Rename `FactoryResetResult` -> `FactoryReset`.
  - Rename `FridaResult` -> `Frida`.
  - Rename `IPLocation` -> `Geolocation`:
    - Rename `IPLocationCity` -> `GeolocationCity`.
    - Extract `subdivisions` type as `GeolocationSubdivisions`.
    - Rename `Location` -> `GeolocationContinent`:
    - Introduce a dedicated type `GeolocationCountry`.
    - Rename `Subdivision` -> `GeolocationSubdivision`.
  - Rename `HighActivityResult` -> `HighActivity`.
  - Rename `Confidence` -> `IdentificationConfidence`.
  - Rename `SeenAt` -> `IdentificationSeenAt`.
  - Rename `IncognitoResult` -> `Incognito`.
  - Rename `IpBlockListResult` -> `IPBlocklist`:
    - Extract `details` type as `IPBlocklistDetails`.
  - Rename `IpInfoResult` -> `IPInfo`:
    - Rename `IpInfoResultV4` -> `IPInfoV4`.
    - Rename `IpInfoResultV6` -> `IPInfoV6`.
    - Rename `ASN` -> `IPInfoASN`.
    - Rename `DataCenter` -> `IPInfoDataCenter`.
  - Rename `JailbrokenResult` -> `Jailbroken`.
  - Rename `LocationSpoofingResult` -> `LocationSpoofing`.
  - Rename `PrivacySettingsResult` -> `PrivacySettings`.
  - Rename `ProductsResponse` -> `Products`:
    - Rename inner types: `ProductsResponseIdentification` -> `ProductIdentification`, `ProductsResponseIdentificationData` -> `Identification`, `ProductsResponseBotd` -> `ProductBotd`, `SignalResponseRootApps` -> `ProductRootApps`, `SignalResponseEmulator` -> `ProductEmulator`, `SignalResponseIpInfo` -> `ProductIPInfo`, `SignalResponseIpBlocklist` -> `ProductIPBlocklist`, `SignalResponseTor` -> `ProductTor`, `SignalResponseVpn` -> `ProductVPN`, `SignalResponseProxy` -> `ProductProxy`, `ProxyResult` -> `Proxy`, `SignalResponseIncognito` -> `ProductIncognito`, `SignalResponseTampering` -> `ProductTampering`, `SignalResponseClonedApp` -> `ProductClonedApp`, `SignalResponseFactoryReset` -> `ProductFactoryReset`, `SignalResponseJailbroken` -> `ProductJailbroken`, `SignalResponseFrida` -> `ProductFrida`, `SignalResponsePrivacySettings` -> `ProductPrivacySettings`, `SignalResponseVirtualMachine` -> `ProductVirtualMachine`, `SignalResponseRawDeviceAttributes` -> `ProductRawDeviceAttributes`, `RawDeviceAttributesResultValue` -> `RawDeviceAttributes`, `SignalResponseHighActivity` -> `ProductHighActivity`, `SignalResponseLocationSpoofing` -> `ProductLocationSpoofing`, `SignalResponseSuspectScore` -> `ProductSuspectScore`, `SignalResponseRemoteControl` -> `ProductRemoteControl`, `SignalResponseVelocity` -> `ProductVelocity`, `SignalResponseDeveloperTools` -> `ProductDeveloperTools`.
    - Extract `identification.data` type as `Identification`.
  - Use PHP `array` instead of `RawDeviceAttributesResult`
  - Rename `RemoteControlResult` -> `RemoteControl`.
  - Rename `RootAppsResult` -> `RootApps`.
  - Rename `SuspectScoreResult` -> `SuspectScore`.
  - Rename `TamperingResult` -> `Tampering`.
  - Rename `TorResult` -> `Tor`.
  - Rename `VelocityResult` -> `Velocity`:
    - Rename `VelocityIntervals` -> `VelocityData`.
    - Rename `VelocityIntervalResult` -> `VelocityIntervals`.
  - Rename `VirtualMachineResult` -> `VirtualMachine`.
  - Rename the `Visit` field `ipLocation` type `DeprecatedIPLocation` -> `DeprecatedGeolocation`.
    - Instead of `DeprecatedIPLocationCity` use common `GeolocationCity`
  - Rename `Response` -> `VisitorsGetResponse`.
    - Omit extra inner type `ResponseVisits`
  - Rename `VpnResult` -> `VPN`.
    - Extract `confidence` type as `VPNConfidence`.
    - Extract `methods` type as `VPNMethods`.
  - Rename `WebhookVisit` -> `Webhook`.
    - Introduce new inner types: `WebhookRootApps`, `WebhookEmulator`, `WebhookIPInfo`, `WebhookIPBlocklist`, `WebhookTor`, `WebhookVPN`, `WebhookProxy`, `WebhookTampering`, `WebhookClonedApp`, `WebhookFactoryReset`, `WebhookJailbroken`, `WebhookFrida`, `WebhookPrivacySettings`, `WebhookVirtualMachine`, `WebhookHighActivity`, `WebhookLocationSpoofing`, `WebhookSuspectScore`, `WebhookRemoteControl`, `WebhookVelocity`, `WebhookDeveloperTools`. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))

### Minor Changes

- Added new `ipEvents`, `distinctIpByLinkedId`, and `distinctVisitorIdByLinkedId` fields to the `velocity` Smart Signal. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))
- - Make the `GeolocationCity` field `name` **required**.
  - Make the `GeolocationSubdivision` field `isoCode` **required**.
  - Make the `GeolocationSubdivision` field `name` **required**.
  - Make the `IPInfoASN` field `name` **required** .
  - Make the `IPInfoDataCenter` field `name` **required**.
  - Add **optional** `IdentificationConfidence` field `comment`.
  - **events**: Add **optional** `Botd` field `meta`.
  - **events**: Add **optional** `Identification` field `components`.
  - **events**: Make the `VPN` field `originCountry` **required**.
  - **visitors**: Add **optional** `Visit` field `components`.
  - **webhook**: Add **optional** `Webhook` field `components`. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))
- Remove `ipv4` format from `ip` field in `Botd`, `Identification`, `Visit` and `Webhook` models. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))
- **events**: Add `antiDetectBrowser` detection method to the `tampering` Smart Signal. ([5645bf0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5645bf0f738f673b5a4941c27c42c0468670fdd5))

## 5.1.1

### Patch Changes

- Mark nullable types as an optional, will fix #123 ([9e8f44a](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9e8f44a01cd78edbbbd8b480753a63673b723c30))

## 5.1.0

### Minor Changes

- Introduce `toPrettyString` in models that returns json encoded model in a pretty format ([2e5010b](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/2e5010ba106f7e37c0f804fa1844e293c8bd9189))
- **visitors**: Add the confidence field to the VPN Detection Smart Signal ([4f809a0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/4f809a06b522161edcc23c769f18771f2821f2ac))

### Patch Changes

- Correctly handle boolean values in `updateEvent` method request body ([d5a8d80](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/d5a8d808f8c20388b0492c660f486d9f3bc4b694))
- Fix body in `updateEvent` method always being sent as empty json ([acdc56e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/acdc56e17238ee5531ac35aef0507165d2e315ca))

## 5.1.0-develop.2

### Patch Changes

- Correctly handle boolean values in `updateEvent` method request body ([d5a8d80](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/d5a8d808f8c20388b0492c660f486d9f3bc4b694))

## 5.1.0-develop.1

### Minor Changes

- Introduce `toPrettyString` in models that returns json encoded model in a pretty format ([2e5010b](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/2e5010ba106f7e37c0f804fa1844e293c8bd9189))

### Patch Changes

- Fix body in `updateEvent` method always being sent as empty json ([acdc56e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/acdc56e17238ee5531ac35aef0507165d2e315ca))

## 5.1.0-develop.0

### Minor Changes

- **visitors**: Add the confidence field to the VPN Detection Smart Signal ([4f809a0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/4f809a06b522161edcc23c769f18771f2821f2ac))

## [5.0.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v4.1.0...v5.0.0) (2024-09-09)

### ⚠ BREAKING CHANGES

- Renamed Error Response Model Names
  `ErrorEvent403ResponseError` → `Common403ErrorResponse`
  `ManyRequestsResponse` → `TooManyRequestsResponse`
- Webhook `tag` field is now optional
- API Methods now throws `SerializationException`
- API Methods returns tuple instead of models
- API Methods removed other than `getModel`
- Upgraded minimum php version to 8.1
- Request logic is rewritten, Upgraded minimum php version to 8.1

### Features

- add [Confidence Score v1.1](https://dev.fingerprint.com/docs/understanding-your-confidence-score-v11)
- add `remoteControl`, `velocity` and `developerTools` signals ([5bf9368](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5bf9368fb1bb5abbfa72366fe6c66fe32352ad54))
- add delete visitor data endpoint ([a00f325](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/a00f325706af38cc20e4c387db44dfa83c7a7a22))
- add retry after policy to api exception ([64e0510](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/64e05100a2c20c8d1f8e5f9719ee5292c59761c2))
- add support for validating webhook signatures inter-768 ([6a4cbd6](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/6a4cbd6e2a521a209806337d90ae8f7e291a534b))
- add update event endpoint (PUT) ([cb21d0b](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/cb21d0b4c48b7b94f4e9a7de1ab74313fe339e5f))
- change api to return tuple instead of serialized model ([62e4ad3](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/62e4ad3020425667f45e3beeb166b4095a437ab5))
- introduce rawResponse for getVisits and getEvent methods ([9b01ba6](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9b01ba65e7ac794c77afedc155823baef2c80b17))
- introduce serialization exception ([bfea23a](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/bfea23a50b61152d4fc65d290c730d0e3fcb6123))
- only generate models and docs from swagger codegen ([26e984f](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/26e984ffd01dc9f21af3dd4da75fcb7e4309961f))
- remove raw response and introduce with http info ([ce2fedf](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/ce2fedfcd94f5f1459049ba49eff75e2d3b8620c))
- rewrite request logic ([0016822](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/001682270fd4370484b25062550a65bd375b9372))
- upgrade min php version to 8 ([5698871](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5698871fa497ee4ad50b2941d39769b45f15dfc2))

### Bug Fixes

- php-cs-fixer keep nullable return annotations ([99011b7](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/99011b736a2782fc50f488d5e83d489540a860fb))
- serializaiton problem on sealed results ([29cb26c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/29cb26c3f50bfa035ca750948bb92a2299f579bd))
- use linter with current config via docker ([9613c34](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9613c34535c8ef675a0d5e129ca4a23b1ee6fcc9))

## [5.0.0-develop.3](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v5.0.0-develop.2...v5.0.0-develop.3) (2024-09-09)

### Bug Fixes

- php-cs-fixer keep nullable return annotations ([99011b7](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/99011b736a2782fc50f488d5e83d489540a860fb))

## [5.0.0-develop.3](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v5.0.0-develop.2...v5.0.0-develop.3) (2024-09-04)

### Bug Fixes

- php-cs-fixer keep nullable return annotations ([99011b7](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/99011b736a2782fc50f488d5e83d489540a860fb))

## [5.0.0-develop.2](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v5.0.0-develop.1...v5.0.0-develop.2) (2024-09-04)

### Bug Fixes

- use linter with current config via docker ([9613c34](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9613c34535c8ef675a0d5e129ca4a23b1ee6fcc9))

## [5.0.0-develop.1](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v4.1.0...v5.0.0-develop.1) (2024-09-04)

### ⚠ BREAKING CHANGES

- Renamed Error Response Model Names
  `ErrorEvent403ResponseError` → `Common403ErrorResponse`
  `ManyRequestsResponse` → `TooManyRequestsResponse`
- Webhook `tag` field is now optional
- API Methods now throws `SerializationException`
- API Methods returns tuple instead of models
- API Methods removed other than `getModel`
- Upgraded minimum php version to 8.1
- Request logic is rewritten, Upgraded minimum php version to 8.1

### Features

- add `remoteControl`, `velocity` and `developerTools` signals ([5bf9368](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5bf9368fb1bb5abbfa72366fe6c66fe32352ad54))
- add delete visitor data endpoint ([a00f325](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/a00f325706af38cc20e4c387db44dfa83c7a7a22))
- add retry after policy to api exception ([64e0510](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/64e05100a2c20c8d1f8e5f9719ee5292c59761c2))
- add support for validating webhook signatures inter-768 ([6a4cbd6](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/6a4cbd6e2a521a209806337d90ae8f7e291a534b))
- add update event endpoint (PUT) ([cb21d0b](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/cb21d0b4c48b7b94f4e9a7de1ab74313fe339e5f))
- change api to return tuple instead of serialized model ([62e4ad3](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/62e4ad3020425667f45e3beeb166b4095a437ab5))
- introduce rawResponse for getVisits and getEvent methods ([9b01ba6](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/9b01ba65e7ac794c77afedc155823baef2c80b17))
- introduce serialization exception ([bfea23a](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/bfea23a50b61152d4fc65d290c730d0e3fcb6123))
- only generate models and docs from swagger codegen ([26e984f](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/26e984ffd01dc9f21af3dd4da75fcb7e4309961f))
- remove raw response and introduce with http info ([ce2fedf](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/ce2fedfcd94f5f1459049ba49eff75e2d3b8620c))
- rewrite request logic ([0016822](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/001682270fd4370484b25062550a65bd375b9372))
- upgrade min php version to 8 ([5698871](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/5698871fa497ee4ad50b2941d39769b45f15dfc2))

### Bug Fixes

- serializaiton problem on sealed results ([29cb26c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/29cb26c3f50bfa035ca750948bb92a2299f579bd))

## [4.1.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v4.0.0...v4.1.0) (2024-03-26)

### Features

- deprecate support for PHP versions older than 8.1 ([56042c3](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/56042c35eb01bf39a23000af6996204571dced6d))

### Documentation

- **README:** fix badges formatting ([779c726](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/779c726ef99166eaec006cd8467612b4e8037eeb))

## [4.0.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v3.1.0...v4.0.0) (2024-03-12)

### ⚠ BREAKING CHANGES

- change models for the most smart signals
- make identification field `confidence` optional
- deprecated `ipLocation` field uses `DeprecatedIpLocation` model

### Features

- add `linkedId` field to the `BotdResult` type ([13d1998](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/13d19989391b671d36a5b933daf64816476867f8))
- add `originCountry` field to the `vpn` signal ([d3763f9](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/d3763f97d7f4909508abdfce5cec456de42727cf))
- add `SuspectScore` smart signal support ([aad70df](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/aad70df86d2dbc76c4bf12ede7b229f9bd413b32))
- change `url` field format from URI to regular String ([425576e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/425576ee9eb5f2f775ef075a3dc394f05d4858e8))
- fix `ipLocation` deprecation ([60c77d8](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/60c77d8f448ea5d3b469d77aca10829cb62281b2))
- make identification field `tag` required ([fbcb954](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/fbcb9547cf6dfbd03b5de00e4ae6114b9f43b06d))
- use shared structures for webhooks and event ([49480f9](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/49480f923b2bb19b6841522def6eac9aa0b64b67))

### Bug Fixes

- make fields required according to real API response ([d129f54](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/d129f54a8bad1c3df1c000b2a96337ffb90056c0))

## [3.1.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v3.0.0...v3.1.0) (2024-02-13)

### Features

- add method for decoding sealed results ([2008cf6](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/2008cf6427a1edced49aec1f2130e389622d8d88))

## [3.0.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v2.2.0...v3.0.0) (2024-01-11)

### ⚠ BREAKING CHANGES

- `IpInfo` field `data_center` renamed to `datacenter`

### Features

- deprecate `IPLocation` ([ad6201c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/ad6201cc619cf09f74f31389eb24fe208ea1ddc4))
- use `datacenter` instead of the wrong `dataCenter` ([19158aa](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/19158aa0054c37975a83cfe0076ad48626155943))

## [2.2.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v2.1.1...v2.2.0) (2023-11-27)

### Features

- add `highActivity` and `locationSpoofing` signals, support `originTimezone` for `vpn` signal ([e35961c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e35961cf93a86a0e3d3ebe6d373452e3f7918853))

## [2.1.1](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v2.1.0...v2.1.1) (2023-09-19)

### Bug Fixes

- update OpenAPI Schema with `asn` and `dataCenter` signals ([36fcfe3](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/36fcfe3be5617256a43b9f809b59f0ebde166765))
- update OpenAPI Schema with `auxiliaryMobile` method for VPN signal ([ab8c25a](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/ab8c25a890a9d78b6a52cf1a784228b4702bc4a1))

## [2.1.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v2.0.0...v2.1.0) (2023-07-31)

### Features

- add raw attributes support ([d47e33a](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/d47e33a8629dda47bab1bc72ac65739c48d416ab))
- add smart signals support ([40011bb](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/40011bbd07ccdf1d99be5968c1786a8eb0be06eb))

### Bug Fixes

- generate correct code for the RawDeviceAttributes signal ([fd71960](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/fd71960ce144181cd8e8a6c1200ddd60cd098d58))

## [2.0.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v1.2.2...v2.0.0) (2023-06-06)

### ⚠ BREAKING CHANGES

- For `getVisits` method `$before` argument is deprecated
  Use `$pagination_key` instead.

### Features

- update schema with correct `IpLocation` format and doc updates ([0318b55](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/0318b55599ede2e10d6ce9ec48c409d5fc605f03))

## [1.2.2](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v1.2.1...v1.2.2) (2023-05-26)

### Bug Fixes

- generate backtick symbol correctly for model documentation ([e33153c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e33153ca122633c61d4301fe34564687ef235768))
- update schema ([788dddb](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/788dddb6be54d570f609c920e2665cd3c704c56a))

## [1.2.1](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v1.2.0...v1.2.1) (2023-05-26)

### Bug Fixes

- update schema with improved documentation ([69c8bab](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/69c8bab9db2c7519c12eb30452bf4dc3fc578b84))
- update templates to correctly generate backtick symbol instead of html variant &#x60; ([bb7f732](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/bb7f73240c5ce00d16aa3d9d45c5f6b889326338)), closes [#x60](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/x60)

## [1.2.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v1.1.0...v1.2.0) (2023-05-16)

### Features

- update schema to introduce new signals ([cc7640a](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/cc7640a0e3af120feae7f4056fbd3db58b8bf976))

## [1.1.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v1.0.1...v1.1.0) (2023-03-01)

### Features

- **guzzle:** support laravel9 by guzzle 7.4 ([4368166](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/4368166459708cf9479bcd253b3b51563f91e439))
- **laravel:** support for 2 latest major laravel version ([e648b5e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e648b5ebcce050683e3a145c4f07e3381b9a0678))

## [1.1.0-develop.2](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v1.1.0-develop.1...v1.1.0-develop.2) (2023-03-01)

### Features

- **laravel:** support for 2 latest major laravel version ([e648b5e](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e648b5ebcce050683e3a145c4f07e3381b9a0678))

## [1.1.0-develop.1](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v1.0.1...v1.1.0-develop.1) (2023-03-01)

### Features

- **guzzle:** support laravel9 by guzzle 7.4 ([4368166](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/4368166459708cf9479bcd253b3b51563f91e439))

## [1.0.1](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v1.0.0...v1.0.1) (2022-11-11)

### Documentation

- **README:** change description formatting ([dd0acbe](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/dd0acbe89d6858bf4fc26a20f5c24170c7c21eaf))
- **README:** extra information about regions ([1310806](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/1310806b2015b02e9015b84304f7efc7eac3837c))
- **README:** move tests section to bottom ([c2b0bab](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/c2b0bab528d9b202607db06be9ebb70893b9235a))

## [1.0.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.4.1...v1.0.0) (2022-11-02)

### ⚠ BREAKING CHANGES

- We now have a mature version of the sdk

### Features

- remove beta disclaimer from readme ([cb39ecd](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/cb39ecdcd8648a69971b21afdbdb7529355728c6))

### Documentation

- **README:** add packagist badges ([3aea456](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/3aea456acc7797368b2edf8292ce028dab2632c8))

## [0.4.1](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.4.0...v0.4.1) (2022-11-02)

### Documentation

- **README:** update installation instructions & introduce new tags on composer.json: ([e8e6ad6](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e8e6ad6f5c6cf570a4c7ce121f66cef94704cbf0))

## [0.4.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.3.0...v0.4.0) (2022-11-02)

### Features

- add integration info query parameter ([e4a6c18](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/e4a6c18fa6b00ac45447e040790a6b2a7c078783))

## [0.3.0](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.2.5...v0.3.0) (2022-11-01)

### Features

- simplified configuration api ([#40](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/40)) ([0ea6edd](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/0ea6eddfbce92318f570ce496cd878676f9698b3))
- update php version for public consts & simplify docs ([#42](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/42)) ([18eb759](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/18eb759e1296dd7339cb711c02da077f359a5a0d))
- update schema to support url field for botd result ([#32](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/32)) ([002542b](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/002542b4fd667ea36ab728d1c177be314000cbae))

### Bug Fixes

- add force flag to rm command ([#33](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/33)) ([b8d8b9d](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/b8d8b9dec6c351b869fef0b50c37684ad317b6d5))
- api doc fixed for simpler use ([#43](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/43)) ([dca0952](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/dca0952e5bce0f7138e8de0da17584d6caa329d7))
- change prepareCmd command ([#36](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/36)) ([ece2766](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/ece27666ac1bff0c177858644de231dab2af6d69))
- changed readme instructions & renamed organization name ([334138c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/334138ccac1f8d71bad86b1825a595ee6f43465a))
- posix error on releaserc ([#38](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/38)) ([6a66999](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/6a669994eb21fc0081643120917a62a79637b6b7))
- updated list of ignored generated files ([#37](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/37)) ([8fced34](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/8fced34b4157c66add3662148fc8b03adafdea6f))

## [0.3.0-develop.7](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.3.0-develop.6...v0.3.0-develop.7) (2022-11-01)

### Bug Fixes

- api doc fixed for simpler use ([#43](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/43)) ([dca0952](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/dca0952e5bce0f7138e8de0da17584d6caa329d7))

## [0.3.0-develop.6](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.3.0-develop.5...v0.3.0-develop.6) (2022-11-01)

### Features

- update php version for public consts & simplify docs ([#42](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/42)) ([18eb759](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/18eb759e1296dd7339cb711c02da077f359a5a0d))

## [0.3.0-develop.5](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.3.0-develop.4...v0.3.0-develop.5) (2022-10-31)

### Features

- simplified configuration api ([#40](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/40)) ([0ea6edd](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/0ea6eddfbce92318f570ce496cd878676f9698b3))

## [0.3.0-develop.4](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.3.0-develop.3...v0.3.0-develop.4) (2022-10-26)

### Bug Fixes

- changed readme instructions & renamed organization name ([334138c](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/334138ccac1f8d71bad86b1825a595ee6f43465a))

## [0.3.0-develop.3](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.3.0-develop.2...v0.3.0-develop.3) (2022-10-25)

### Bug Fixes

- posix error on releaserc ([#38](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/38)) ([6a66999](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/6a669994eb21fc0081643120917a62a79637b6b7))
- updated list of ignored generated files ([#37](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/37)) ([8fced34](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/8fced34b4157c66add3662148fc8b03adafdea6f))

## [0.3.0-develop.2](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.3.0-develop.1...v0.3.0-develop.2) (2022-10-25)

### Bug Fixes

- change prepareCmd command ([#36](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/36)) ([ece2766](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/ece27666ac1bff0c177858644de231dab2af6d69))

## [0.3.0-develop.1](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/compare/v0.2.5...v0.3.0-develop.1) (2022-10-25)

### Features

- update schema to support url field for botd result ([#32](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/32)) ([002542b](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/002542b4fd667ea36ab728d1c177be314000cbae))

### Bug Fixes

- add force flag to rm command ([#33](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/issues/33)) ([b8d8b9d](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk/commit/b8d8b9dec6c351b869fef0b50c37684ad317b6d5))
