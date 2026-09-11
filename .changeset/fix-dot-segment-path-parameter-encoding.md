---
"@fingerprint/php-sdk": patch
---

Fixed `event_id`/`visitor_id` values of exactly `.` or `..` being collapsed by curl's URL normalization before the request is sent, causing `getEvent`, `updateEvent`, and `deleteVisitorData` to hit the wrong endpoint instead of the requested resource.
