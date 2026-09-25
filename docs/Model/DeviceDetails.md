# DeviceDetails Class

Native, SDK-collected mobile device identification signals (manufacturer, model, and OS version). Structurally separate from the top-level `device`, `os`, and `os_version` fields and from `browser_details`, all of which are derived from user-agent parsing rather than native SDK signals.

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**device_manufacturer** | **string** | Raw device manufacturer string as reported by the device OS. Not normalized: casing is vendor-defined (samsung, Xiaomi, OPPO, HUAWEI). Always &#x60;Apple&#x60; on iOS. | [optional]
**device_model** | **string** | Raw device model identifier, as reported by the mobile OS. | [optional]
**os_version** | **string** | Mobile operating system version. Component count is not fixed and must not be assumed by consumers: iOS always reports &#x60;major.minor.patch&#x60; (e.g. &#x60;17.4.1&#x60;), while Android&#39;s precision varies by OS era and which raw signal resolved it — &#x60;major&#x60; only (&#x60;9&#x60;, &#x60;13&#x60;) since Android 10 dropped point releases, &#x60;major.minor&#x60; (&#x60;16.1&#x60;) from Android 16 (API 36+) reintroducing a minor component, or a genuine &#x60;major.minor.patch&#x60; (&#x60;8.1.0&#x60;) on pre-Android 10 devices that shipped real point releases. Never a fabricated/zero-padded component. | [optional]

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)