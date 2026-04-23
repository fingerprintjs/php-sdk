# TamperingDetails Class

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**anomaly_score** | **float** | The output of this model is captured as anomaly_score, a statistical score indicating how rare the visitor&#39;s browser signature is compared to the overall population. Values close to 1 signify highly anomalous browsers and we consider anything above the threshold of 0.5 to be actionable (the result field conveniently captures that fact). | [optional]
**anti_detect_browser** | **bool** | Detects whether the request shows evidence of anti-detect browser usage. This field may be triggered by: * heuristic detection of known anti-detect browser behavior * machine learning detection of anti-detect browser patterns  Examples of anti-detect browsers include tools such as AdsPower, DolphinAnty, OctoBrowser, and GoLogin. | [optional]

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)