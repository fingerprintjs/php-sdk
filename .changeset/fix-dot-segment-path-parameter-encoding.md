---
"fingerprint-pro-server-api-php-sdk": patch
---

Fixed `request_id`/`visitor_id` values of exactly `.` or `..` being collapsed by curl's URL normalization before the request is sent, causing `getEvent`, `updateEvent`, `deleteVisitorData`, and `getVisits` to hit the wrong endpoint instead of the requested resource.
