# TamperingConfidence Enum

The confidence level indicates how certain Fingerprint is that the current request involves browser tampering. This confidence level is determined by evaluating multiple factors, such as heuristic rules, probabilistic anomaly detection, an anti detect browser ml model, and other relevant methods. It is conveyed as a string with possible values such as high, medium, or low
In case of tampering: `true`
* **High confidence**: heuristic anti detect browser signals and the ml model are triggered, or all of the methods are triggered.
* **Medium confidence**: either the ml model triggers alone, the anomaly score triggers alone with or without the heuristic anti detect browser methods trigger.
* **Low confidence**: only the heuristic anti detect methods are triggered.

In case of tampering: `false`
* **High confidence:** Strong signals suggest the user is not tampering with their request.


## Values

| Name | Value | Description |
| --- | --- | --- |
LOW | 'low' |  |
MEDIUM | 'medium' |  |
HIGH | 'high' |  |

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)