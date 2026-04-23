# RawDeviceAttributes Class

A curated subset of raw browser/device attributes that the API surface exposes. Each property contains a value or object with the data for the collected signal.


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**font_preferences** | [**\Fingerprint\ServerSdk\Model\FontPreferences**](FontPreferences.md) |  | [optional]
**emoji** | [**\Fingerprint\ServerSdk\Model\Emoji**](Emoji.md) |  | [optional]
**fonts** | **string[]** | List of fonts detected on the device. | [optional]
**device_memory** | **int** | Rounded amount of RAM (in gigabytes) reported by the browser. | [optional]
**timezone** | **string** | Timezone identifier detected on the client. | [optional]
**canvas** | [**\Fingerprint\ServerSdk\Model\Canvas**](Canvas.md) |  | [optional]
**languages** | **string[][]** | Navigator languages reported by the agent including fallbacks. Each inner array represents ordered language preferences reported by different APIs. Available for both browsers and iOS devices | [optional]
**webgl_extensions** | [**\Fingerprint\ServerSdk\Model\WebGlExtensions**](WebGlExtensions.md) |  | [optional]
**webgl_basics** | [**\Fingerprint\ServerSdk\Model\WebGlBasics**](WebGlBasics.md) |  | [optional]
**screen_resolution** | **int[]** | Current screen resolution. Available for both browsers and iOS devices | [optional]
**touch_support** | [**\Fingerprint\ServerSdk\Model\TouchSupport**](TouchSupport.md) |  | [optional]
**oscpu** | **string** | Navigator &#x60;oscpu&#x60; string. | [optional]
**architecture** | **int** | Integer representing the CPU architecture exposed by the browser. | [optional]
**cookies_enabled** | **bool** | Whether the cookies are enabled in the browser. | [optional]
**hardware_concurrency** | **int** | Number of logical CPU cores reported by the browser. | [optional]
**date_time_locale** | **string** | Locale derived from the Intl.DateTimeFormat API. Negative values indicate known error states. The negative statuses can be: - \&quot;-1\&quot;: A permanent status for browsers that don&#39;t support Intl API. - \&quot;-2\&quot;: A permanent status for browsers that don&#39;t supportDateTimeFormat constructor. - \&quot;-3\&quot;: A permanent status for browsers in which DateTimeFormat locale is undefined or null. | [optional]
**vendor** | **string** | Navigator vendor string. | [optional]
**color_depth** | **int** | Screen color depth in bits. | [optional]
**platform** | **string** | Navigator platform string. | [optional]
**session_storage** | **bool** | Whether sessionStorage is available. | [optional]
**local_storage** | **bool** | Whether localStorage is available. | [optional]
**audio** | **float** | AudioContext fingerprint or negative status when unavailable. The negative statuses can be: - -1: A permanent status for those browsers which are known to always suspend audio context - -2: A permanent status for browsers that don&#39;t support the signal - -3: A temporary status that means that an unexpected timeout has happened | [optional]
**plugins** | [**\Fingerprint\ServerSdk\Model\PluginsInner[]**](PluginsInner.md) | Browser plugins reported by &#x60;navigator.plugins&#x60;. | [optional]
**indexed_db** | **bool** | Whether IndexedDB is available. | [optional]
**math** | **string** | Hash of Math APIs used for entropy collection. | [optional]
**device_model** | **string** | Device model string. Available only for Android and iOS devices. | [optional]
**device_manufacturer** | **string** | Device manufacturer string. Available only for Android and iOS devices. | [optional]
**font_hash** | **string** | Unique identifier for the user’s installed fonts. | [optional]
**timezone_offset** | **string** | UTC offset in \&quot;±HH:MM\&quot; format derived from the detected IANA timezone. | [optional]

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)