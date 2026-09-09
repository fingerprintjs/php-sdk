# Event Class

An identification event (`source: device`) or an Automation Intelligence event (`source: edge`).

Use `source` to tell them apart. Device events include Identification and device-derived Smart Signals. Edge events do not.

Consult the [Smart Signals reference](https://docs.fingerprint.com/docs/smart-signals-reference) for more details.


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**event_id** | **string** | Unique identifier of the user&#39;s request. The first portion of the event_id is a unix epoch milliseconds timestamp. |
**timestamp** | **int** | Timestamp of the event with millisecond precision in Unix time. |
**linked_id** | **string** | A customer-provided id that was sent with the request. | [optional]
**tags** | **array<string,mixed>** | A customer-provided value or an object that was sent with the identification request or updated later. | [optional]
**url** | **string** | Page URL from which the request was sent. | [optional]
**bot_info** | [**\Fingerprint\ServerSdk\Model\BotInfo**](BotInfo.md) |  | [optional]
**ip_info** | [**\Fingerprint\ServerSdk\Model\IPInfo**](IPInfo.md) |  | [optional]
**proxy** | **bool** | IP address was used by a public proxy provider or belonged to a known recent residential proxy | [optional]
**proxy_confidence** | [**\Fingerprint\ServerSdk\Model\ProxyConfidence**](ProxyConfidence.md) |  | [optional]
**proxy_details** | [**\Fingerprint\ServerSdk\Model\ProxyDetails**](ProxyDetails.md) |  | [optional]
**vpn** | **bool** | VPN or other anonymizing service has been used when sending the request. | [optional]
**vpn_confidence** | [**\Fingerprint\ServerSdk\Model\VpnConfidence**](VpnConfidence.md) |  | [optional]
**vpn_methods** | [**\Fingerprint\ServerSdk\Model\VpnMethods**](VpnMethods.md) |  | [optional]
**source** | [**\Fingerprint\ServerSdk\Model\EventSource**](EventSource.md) |  | [optional]
**incremental_identification_status** | [**\Fingerprint\ServerSdk\Model\IncrementalIdentificationStatus**](IncrementalIdentificationStatus.md) |  | [optional]
**environment_id** | **string** | Environment Id of the event. | [optional]
**suspect** | **bool** | Field is &#x60;true&#x60; if you have previously set the &#x60;suspect&#x60; flag for this event using the [Server API Update event endpoint](https://docs.fingerprint.com/reference/server-api-update-event). | [optional]
**sdk** | [**\Fingerprint\ServerSdk\Model\SDK**](SDK.md) |  | [optional]
**replayed** | **bool** | &#x60;true&#x60; if we determined that this payload was replayed, &#x60;false&#x60; otherwise. | [optional]
**identification** | [**\Fingerprint\ServerSdk\Model\Identification**](Identification.md) |  | [optional]
**supplementary_id_high_recall** | [**\Fingerprint\ServerSdk\Model\SupplementaryIDHighRecall**](SupplementaryIDHighRecall.md) |  | [optional]
**bundle_id** | **string** | Bundle Id of the iOS application integrated with the Fingerprint SDK for the event. | [optional]
**package_name** | **string** | Package name of the Android application integrated with the Fingerprint SDK for the event. | [optional]
**ip_address** | **string** | IP address of the requesting browser or bot. | [optional]
**user_agent** | **string** | User Agent of the client. | [optional]
**device** | **string** | Device model or family extracted from the user agent string. On web, this field is also present inside &#x60;browser_details&#x60;. | [optional]
**os** | **string** | Operating system family extracted from the user agent string. On web, this field is also present inside &#x60;browser_details&#x60;. | [optional]
**os_version** | **string** | Operating system version string extracted from the user agent string. On web, this field is also present inside &#x60;browser_details&#x60;. | [optional]
**client_referrer** | **string** | Client Referrer field corresponds to the &#x60;document.referrer&#x60; field gathered during an identification request. The value is an empty string if the user navigated to the page directly (not through a link, but, for example, by using a bookmark). | [optional]
**browser_details** | [**\Fingerprint\ServerSdk\Model\BrowserDetails**](BrowserDetails.md) |  | [optional]
**proximity** | [**\Fingerprint\ServerSdk\Model\Proximity**](Proximity.md) |  | [optional]
**active_call** | **bool** | Indicates whether the mobile device had an active call (cellular or VoIP) at the time of the request. Available from SDK 2.16.0+ on iOS and Android. | [optional]
**bot** | [**\Fingerprint\ServerSdk\Model\BotResult**](BotResult.md) |  | [optional]
**bot_type** | **string** | Additional classification of the bot type if detected. | [optional]
**cloned_app** | **bool** | Android specific cloned application detection. There are 2 values:  * &#x60;true&#x60; - Presence of app cloners work detected (e.g. fully cloned application found or launch of it inside of a not main working profile detected). * &#x60;false&#x60; - No signs of cloned application detected or the client is not Android. | [optional]
**developer_tools** | **bool** | &#x60;true&#x60; if the browser has DevTools open (Chrome, Firefox) or the Android/iOS device has Developer Tools enabled, &#x60;false&#x60; otherwise. | [optional]
**emulator** | **bool** | Android specific emulator detection. There are 2 values:  * &#x60;true&#x60; - Emulated environment detected (e.g. launch inside of AVD).  * &#x60;false&#x60; - No signs of emulated environment detected or the client is not Android. | [optional]
**factory_reset_timestamp** | **int** | The time of the most recent factory reset that happened on the **mobile device** is expressed as Unix epoch time. When a factory reset cannot be detected on the mobile device or when the request is initiated from a browser,  this field will correspond to the *epoch* time (i.e 1 Jan 1970 UTC) as a value of 0. See [Factory Reset Detection](https://docs.fingerprint.com/docs/smart-signals-reference#factory-reset-detection) to learn more about this Smart Signal. | [optional]
**frida** | **bool** | [Frida](https://frida.re/docs/) detection for Android and iOS devices. There are 2 values: * &#x60;true&#x60; - Frida detected * &#x60;false&#x60; - No signs of Frida or the client is not a mobile device. | [optional]
**ip_blocklist** | [**\Fingerprint\ServerSdk\Model\IPBlockList**](IPBlockList.md) |  | [optional]
**proxy_ml_score** | **float** | Machine learning–based proxy score, represented as a floating-point value between 0 and 1 (inclusive), with up to three decimal places of precision. A higher score means a higher confidence in the positive &#x60;proxy&#x60; detection result. This Smart Signal is currently in beta and only available to select customers. If you are interested, please [contact our support team](https://fingerprint.com/support/). | [optional]
**incognito** | **bool** | &#x60;true&#x60; if we detected incognito mode used in the browser, &#x60;false&#x60; otherwise. | [optional]
**jailbroken** | **bool** | iOS specific jailbreak detection. There are 2 values:  * &#x60;true&#x60; - Jailbreak detected. * &#x60;false&#x60; - No signs of jailbreak or the client is not iOS. | [optional]
**location_spoofing** | **bool** | Flag indicating whether the request came from a mobile device with location spoofing enabled. | [optional]
**mitm_attack** | **bool** | * &#x60;true&#x60; - When requests made from your users&#39; mobile devices to Fingerprint servers have been intercepted and potentially modified.  * &#x60;false&#x60; - Otherwise or when the request originated from a browser. See [MitM Attack Detection](https://docs.fingerprint.com/docs/smart-signals-reference#mitm-attack-detection) to learn more about this Smart Signal. | [optional]
**privacy_settings** | **bool** | &#x60;true&#x60; if the request is from a privacy aware browser (e.g. Tor) or from a browser in which fingerprinting is blocked. Otherwise &#x60;false&#x60;. | [optional]
**root_apps** | **bool** | Android specific root management apps detection. There are 2 values:  * &#x60;true&#x60; - Root Management Apps detected (e.g. Magisk). * &#x60;false&#x60; - No Root Management Apps detected or the client isn&#39;t Android. | [optional]
**rule_action** | [**\Fingerprint\ServerSdk\Model\EventRuleAction**](EventRuleAction.md) |  | [optional]
**simulator** | **bool** | iOS specific simulator detection. There are 2 values: * &#x60;true&#x60; - Simulator environment detected. * &#x60;false&#x60; - No signs of simulator or the client is not iOS. | [optional]
**suspect_score** | **int** | Suspect Score is an easy way to integrate Smart Signals into your fraud protection work flow.  It is a weighted representation of all Smart Signals present in the payload that helps identify suspicious activity. The value range is [0; S] where S is sum of all Smart Signals weights.  See more details here: https://docs.fingerprint.com/docs/suspect-score | [optional]
**tampering** | **bool** | The field can be used as a standalone flag for tampering detection. Alternatively, the more granular fields documented below can be used for workflows that require more context. * &#x60;true&#x60; if tampering is detected through an anomalous browser signature, anti-detect browser detection, or other tampering-related methods * &#x60;false&#x60; if none of the tampering checks return a positive result | [optional]
**tampering_confidence** | [**\Fingerprint\ServerSdk\Model\TamperingConfidence**](TamperingConfidence.md) |  | [optional]
**tampering_ml_score** | **float** | The output of this model is captured as tampering_ml_score, a number indicating how likely an event is coming from an anti detect browser. Values close to 1 signify higher confidence and we consider anything above the threshold of 0.8 to be actionable (the result and anti_detect_browser fields conveniently captures that fact) | [optional]
**tampering_details** | [**\Fingerprint\ServerSdk\Model\TamperingDetails**](TamperingDetails.md) |  | [optional]
**velocity** | [**\Fingerprint\ServerSdk\Model\Velocity**](Velocity.md) |  | [optional]
**virtual_machine** | **bool** | &#x60;true&#x60; if the request came from a browser running inside a virtual machine (e.g. VMWare), &#x60;false&#x60; otherwise. | [optional]
**virtual_machine_ml_score** | **float** | Machine learning–based virtual machine score, represented as a floating-point value between 0 and 1 (inclusive), with up to three decimal places of precision. A higher score means a higher confidence in the positive &#x60;virtual_machine&#x60; detection result. This Smart Signal is currently in beta and only available to select customers. If you are interested, please [contact our support team](https://fingerprint.com/support/). | [optional]
**vpn_ml_score** | **float** | Machine learning–based VPN score, represented as a floating-point value between 0 and 1 (inclusive), with up to three decimal places of precision. A higher score means a higher confidence in the positive &#x60;vpn&#x60; detection result. This Smart Signal is currently in beta and only available to select customers. If you are interested, please [contact our support team](https://fingerprint.com/support/). | [optional]
**vpn_origin_timezone** | **string** | Local timezone which is used in timezone_mismatch method. | [optional]
**vpn_origin_country** | **string** | Country of the request (Android SDK version &gt;&#x3D; 2.4.0, iOS SDK version &gt;&#x3D; 2.9.0, JS agent &gt;&#x3D; 3.12.9 / 4.0.2), ISO 3166 format or unknown. | [optional]
**high_activity_device** | **bool** | Flag indicating if the request came from a high-activity visitor. | [optional]
**rare_device** | **bool** | &#x60;true&#x60; if the device is considered rare based on its combination of hardware and software attributes.  A device is classified as rare if it falls within the top 99.9 percentile (lowest-frequency segment) of observed traffic,  or if its configuration has not been previously seen (&#x60;not_seen&#x60;). &gt; This Smart Signal is currently in beta and only available to select customers. If you are interested, please [contact our support team](https://fingerprint.com/support/). | [optional]
**rare_device_percentile_bucket** | [**\Fingerprint\ServerSdk\Model\RareDevicePercentileBucket**](RareDevicePercentileBucket.md) |  | [optional]
**raw_device_attributes** | [**\Fingerprint\ServerSdk\Model\RawDeviceAttributes**](RawDeviceAttributes.md) |  | [optional]
**labels** | [**\Fingerprint\ServerSdk\Model\LabelsInner[]**](LabelsInner.md) | Each label returns a prediction (true or false) for a specific use case (label field) based on a machine learning score. The machine learning score is determined by a model trained on customer data for that use case. This field is in the beta phase and only available to select customers. If you are interested, please [contact our support team](https://fingerprint.com/support/). | [optional]

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)