# Velocity Class

Sums key data points for a specific `visitor_id`, `ip_address` and `linked_id` at three distinct time
intervals: 5 minutes, 1 hour, and 24 hours as follows: 

- Number of distinct IP addresses associated to the visitor ID.
- Number of distinct linked IDs associated with the visitor ID.
- Number of distinct countries associated with the visitor ID.
- Number of identification events associated with the visitor ID.
- Number of identification events associated with the detected IP address.
- Number of distinct IP addresses associated with the provided linked ID.
- Number of distinct visitor IDs associated with the provided linked ID.

The `24_hours` interval of `distinct_ip`, `distinct_linked_id`, `distinct_country`,
`distinct_ip_by_linked_id` and `distinct_visitor_id_by_linked_id` will be omitted 
if the number of `events` for the visitor ID in the last 24
hours (`events.['24_hours']`) is higher than 20.000.

All will not necessarily be returned in a response, some may be omitted if the 
associated event does not have the required data, such as a linked_id.


## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**distinct_ip** | [**\Fingerprint\ServerSdk\Model\VelocityData**](VelocityData.md) |  | [optional]
**distinct_linked_id** | [**\Fingerprint\ServerSdk\Model\VelocityData**](VelocityData.md) |  | [optional]
**distinct_country** | [**\Fingerprint\ServerSdk\Model\VelocityData**](VelocityData.md) |  | [optional]
**events** | [**\Fingerprint\ServerSdk\Model\VelocityData**](VelocityData.md) |  | [optional]
**ip_events** | [**\Fingerprint\ServerSdk\Model\VelocityData**](VelocityData.md) |  | [optional]
**distinct_ip_by_linked_id** | [**\Fingerprint\ServerSdk\Model\VelocityData**](VelocityData.md) |  | [optional]
**distinct_visitor_id_by_linked_id** | [**\Fingerprint\ServerSdk\Model\VelocityData**](VelocityData.md) |  | [optional]

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)