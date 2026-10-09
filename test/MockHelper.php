<?php

namespace Fingerprint\ServerSdk\Test;

use GuzzleHttp\Psr7\Response;

final class MockHelper
{
    /** @noinspection SpellCheckingInspection */
    public const MOCK_EVENT_ID = '1708102555327.NLOjmg';

    /** @noinspection SpellCheckingInspection */
    public const MOCK_VISITOR_ID = 'AcxioeQKffpXF8iGQK3P';

    /** @noinspection SpellCheckingInspection */
    public const MOCK_RULESET_ID = 'rs_b1k1blhqpOX3kU';
    public const MOCK_RETRY_AFTER = 30;

    public const OPERATION_GET_EVENT = 'OPERATION_GET_EVENT';

    public const OPERATION_GET_EVENT_UNKNOWN_FIELD = 'OPERATION_GET_EVENT_UNKNOWN_FIELD';

    public const OPERATION_GET_EVENT_WITH_RULESET = 'OPERATION_GET_EVENT_WITH_RULESET';

    public const OPERATION_SEARCH_EVENTS = 'OPERATION_SEARCH_EVENTS';

    public const OPERATION_ERROR_400_INVALID_EVENT_ID = 'OPERATION_ERROR_400_INVALID_EVENT_ID';

    public const OPERATION_ERROR_400_RULESET_NOT_FOUND = 'OPERATION_ERROR_400_RULESET_NOT_FOUND';

    public const OPERATION_ERROR_400_REQUEST_BODY_INVALID = 'OPERATION_ERROR_400_REQUEST_BODY_INVALID';

    public const OPERATION_ERROR_400_VISITOR_ID_REQUIRED = 'OPERATION_ERROR_400_VISITOR_ID_REQUIRED';

    public const OPERATION_ERROR_400_VISITOR_ID_INVALID = 'OPERATION_ERROR_400_VISITOR_ID_INVALID';

    public const OPERATION_ERROR_403_SUBSCRIPTION_IS_NOT_ACTIVE = 'OPERATION_ERROR_403_SUBSCRIPTION_IS_NOT_ACTIVE';

    public const OPERATION_ERROR_403_SECRET_API_KEY_REQUIRED = 'OPERATION_ERROR_403_SECRET_API_KEY_REQUIRED';

    public const OPERATION_ERROR_403_SECRET_API_KEY_NOT_FOUND = 'OPERATION_ERROR_403_SECRET_API_KEY_NOT_FOUND';

    public const OPERATION_ERROR_403_WRONG_REGION = 'OPERATION_ERROR_403_WRONG_REGION';

    public const OPERATION_ERROR_403_FEATURE_NOT_ENABLED = 'OPERATION_ERROR_403_FEATURE_NOT_ENABLED';

    public const OPERATION_ERROR_404_EVENT_NOT_FOUND = 'OPERATION_ERROR_404_EVENT_NOT_FOUND';

    public const OPERATION_ERROR_404_VISITOR_NOT_FOUND = 'OPERATION_ERROR_404_VISITOR_NOT_FOUND';

    public const OPERATION_ERROR_409_STATE_NOT_READY = 'OPERATION_ERROR_409_STATE_NOT_READY';

    public const OPERATION_ERROR_429_TOO_MANY_REQUESTS = 'OPERATION_ERROR_429_TOO_MANY_REQUESTS';

    public const OPERATION_ERROR_500_INTERNAL_SERVER_ERROR = 'OPERATION_ERROR_500_INTERNAL_SERVER_ERROR';

    public const OPERATION_ERROR_503_SERVICE_UNAVAILABLE = 'OPERATION_ERROR_503_SERVICE_UNAVAILABLE';

    public const OPERATION_ERROR_504_GATEWAY_TIMEOUT = 'OPERATION_ERROR_504_GATEWAY_TIMEOUT';

    /**
     * Mock response map: operationId => [file, status, extra_headers].
     */
    private const MOCK_RESPONSE_MAP = [
        self::OPERATION_GET_EVENT => [
            'file' => ['events', 'get_event_200.json'],
        ],
        self::OPERATION_GET_EVENT_UNKNOWN_FIELD => [
            'file' => ['events', 'get_event_200_with_unknown_field.json'],
        ],
        self::OPERATION_GET_EVENT_WITH_RULESET => [
            'file' => ['events', 'get_event_ruleset_200.json'],
        ],
        self::OPERATION_SEARCH_EVENTS => [
            'file' => ['events', 'search', 'get_event_search_200.json'],
        ],
        self::OPERATION_ERROR_400_INVALID_EVENT_ID => [
            'file' => ['errors', '400_event_id_invalid.json'],
            'status' => 400,
        ],
        self::OPERATION_ERROR_400_RULESET_NOT_FOUND => [
            'file' => ['errors', '400_ruleset_not_found.json'],
            'status' => 400,
        ],
        self::OPERATION_ERROR_400_REQUEST_BODY_INVALID => [
            'file' => ['errors', '400_request_body_invalid.json'],
            'status' => 400,
        ],
        self::OPERATION_ERROR_400_VISITOR_ID_INVALID => [
            'file' => ['errors', '400_visitor_id_invalid.json'],
            'status' => 400,
        ],
        self::OPERATION_ERROR_400_VISITOR_ID_REQUIRED => [
            'file' => ['errors', '400_visitor_id_required.json'],
            'status' => 400,
        ],
        self::OPERATION_ERROR_403_SECRET_API_KEY_REQUIRED => [
            'file' => ['errors', '403_secret_api_key_required.json'],
            'status' => 403,
        ],
        self::OPERATION_ERROR_403_SECRET_API_KEY_NOT_FOUND => [
            'file' => ['errors', '403_secret_api_key_not_found.json'],
            'status' => 403,
        ],
        self::OPERATION_ERROR_403_WRONG_REGION => [
            'file' => ['errors', '403_wrong_region.json'],
            'status' => 403,
        ],
        self::OPERATION_ERROR_403_FEATURE_NOT_ENABLED => [
            'file' => ['errors', '403_feature_not_enabled.json'],
            'status' => 403,
        ],
        self::OPERATION_ERROR_403_SUBSCRIPTION_IS_NOT_ACTIVE => [
            'file' => ['errors', '403_subscription_not_active.json'],
            'status' => 403,
        ],
        self::OPERATION_ERROR_404_EVENT_NOT_FOUND => [
            'file' => ['errors', '404_event_not_found.json'],
            'status' => 404,
        ],
        self::OPERATION_ERROR_404_VISITOR_NOT_FOUND => [
            'file' => ['errors', '404_visitor_not_found.json'],
            'status' => 404,
        ],
        self::OPERATION_ERROR_409_STATE_NOT_READY => [
            'file' => ['errors', '409_state_not_ready.json'],
            'status' => 409,
        ],
        self::OPERATION_ERROR_429_TOO_MANY_REQUESTS => [
            'file' => ['errors', '429_too_many_requests.json'],
            'status' => 429,
        ],
        self::OPERATION_ERROR_500_INTERNAL_SERVER_ERROR => [
            'file' => ['errors', '500_internal_server_error.json'],
            'status' => 500,
        ],
        self::OPERATION_ERROR_503_SERVICE_UNAVAILABLE => [
            'file' => ['errors', '503_service_unavailable.json'],
            'status' => 503,
        ],
        self::OPERATION_ERROR_504_GATEWAY_TIMEOUT => [
            'file' => ['errors', '504_search_timeout_exceeded.json'],
            'status' => 504,
        ],
    ];

    public static function getMockResponse(string $operationId): Response
    {
        $mockData = self::getMockData($operationId);

        return new Response($mockData['status'], [
            'Content-Type' => 'application/json',
        ], $mockData['contents']);
    }

    public static function getMockData(string $operationId): array
    {
        if (!isset(self::MOCK_RESPONSE_MAP[$operationId])) {
            throw new \InvalidArgumentException("Unknown operation ID: $operationId");
        }

        $data = self::MOCK_RESPONSE_MAP[$operationId];

        return [
            'contents' => file_get_contents(self::getMockFilePath($data['file'])),
            'status' => $data['status'] ?? 200,
        ];
    }

    private static function getMockFilePath(array $paths): string
    {
        return implode(DIRECTORY_SEPARATOR, array_merge([__DIR__, 'mocks'], $paths));
    }
}
