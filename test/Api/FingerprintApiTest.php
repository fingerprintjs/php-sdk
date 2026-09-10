<?php

namespace Fingerprint\ServerSdk\Test\Api;

use Fingerprint\ServerSdk\Api\FingerprintApi;
use Fingerprint\ServerSdk\ApiException;
use Fingerprint\ServerSdk\Configuration;
use Fingerprint\ServerSdk\Model\BotInfoConfidence;
use Fingerprint\ServerSdk\Model\BotResult;
use Fingerprint\ServerSdk\Model\ErrorCode;
use Fingerprint\ServerSdk\Model\ErrorResponse;
use Fingerprint\ServerSdk\Model\Event;
use Fingerprint\ServerSdk\Model\EventRuleAction;
use Fingerprint\ServerSdk\Model\EventSource;
use Fingerprint\ServerSdk\Model\EventUpdate;
use Fingerprint\ServerSdk\Model\Identification;
use Fingerprint\ServerSdk\Model\IdentificationConfidence;
use Fingerprint\ServerSdk\Model\IncrementalIdentificationStatus;
use Fingerprint\ServerSdk\Model\RuleActionType;
use Fingerprint\ServerSdk\Model\SDK;
use Fingerprint\ServerSdk\Model\SearchEventsBot;
use Fingerprint\ServerSdk\Model\SearchEventsBotInfo;
use Fingerprint\ServerSdk\Model\SearchEventsIncrementalIdentificationStatus;
use Fingerprint\ServerSdk\Model\SearchEventsSdkPlatform;
use Fingerprint\ServerSdk\Model\SearchEventsSource;
use Fingerprint\ServerSdk\Model\SearchEventsVpnConfidence;
use Fingerprint\ServerSdk\Model\SupplementaryIDHighRecall;
use Fingerprint\ServerSdk\Test\MockHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

use function PHPUnit\Framework\assertEquals;

/**
 * @internal
 */
#[CoversClass(FingerprintApi::class)]
class FingerprintApiTest extends TestCase
{
    protected FingerprintApi $api;

    protected MockHandler $mockHandler;

    protected Configuration $configuration;

    public function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $client = new Client(['handler' => HandlerStack::create($this->mockHandler)]);
        $this->configuration = new Configuration('test-api-key');
        $this->api = new FingerprintApi($this->configuration, $client);
    }

    public function testItReturnsCorrectConfiguration(): void
    {
        assertEquals($this->configuration, $this->api->getConfig());
    }

    /**
     * Verifies getEvent deserializes all data correctly.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_GET_EVENT));

        $actual = json_decode(MockHelper::getMockData(MockHelper::OPERATION_GET_EVENT)['contents']);
        $event = $this->api->getEvent(MockHelper::MOCK_EVENT_ID);

        $this->assertEvent($event, $actual);
    }

    /**
     * Verifies getEvent raw response matches the mock JSON.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEventRawResponse()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_GET_EVENT));

        $response = $this->api->getEventWithHttpInfo(MockHelper::MOCK_EVENT_ID)[1];
        $mockedResult = Utils::jsonDecode(MockHelper::getMockData(MockHelper::OPERATION_GET_EVENT)['contents']);
        $this->assertEquals($mockedResult, Utils::jsonDecode($response->getBody()->getContents()));
    }

    /**
     * Verifies unknown fields don't break model deserialization.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEventParsedModelWithUnknownField()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_GET_EVENT_UNKNOWN_FIELD));

        $event = $this->api->getEvent(MockHelper::MOCK_EVENT_ID);
        $this->assertFalse($event->getIncognito());
    }

    /**
     * Verifies getEvent operation with ruleset evaluation.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEventWithRuleset()
    {
        $eventId = MockHelper::MOCK_EVENT_ID;
        $rulesetId = MockHelper::MOCK_RULESET_ID;
        $this->mockHandler->append(function (RequestInterface $request) use ($eventId, $rulesetId) {
            $this->assertStringContainsString("/events/$eventId", $request->getUri()->getPath());
            $queryArray = $this->parseQueryString($request->getUri()->getQuery());
            $this->assertCount(2, $queryArray);
            $this->assertEquals($rulesetId, $queryArray['ruleset_id']);

            return MockHelper::getMockResponse(MockHelper::OPERATION_GET_EVENT_WITH_RULESET);
        });

        $event = $this->api->getEvent($eventId, $rulesetId);
        $actual = json_decode(MockHelper::getMockData(MockHelper::OPERATION_GET_EVENT_WITH_RULESET)['contents']);

        $ruleAction = $event->getRuleAction();
        $actualRuleAction = $actual->rule_action;
        $this->assertEquals(EventRuleAction::class, get_class($ruleAction));
        $this->assertEquals(RuleActionType::BLOCK, $ruleAction->getType());
        $this->assertEquals($actualRuleAction->status_code, $ruleAction->getStatusCode());
        $this->assertEquals($actualRuleAction->rule_id, $ruleAction->getRuleId());
        $this->assertEquals($actualRuleAction->rule_expression, $ruleAction->getRuleExpression());
        $this->assertEquals($actualRuleAction->ruleset_id, $ruleAction->getRulesetId());

        $ruleActionHeaders = $ruleAction->getHeaders();
        $actualRuleActionHeaders = $actualRuleAction->headers;
        $this->assertSameSize($actualRuleActionHeaders, $ruleActionHeaders);
        for ($i = 0; $i < count($ruleActionHeaders); ++$i) {
            $actualRuleActionHeader = $actualRuleActionHeaders[$i];
            $ruleActionHeader = $ruleActionHeaders[$i];
            $this->assertEquals($actualRuleActionHeader->name, $ruleActionHeader->getName());
            $this->assertEquals($actualRuleActionHeader->value, $ruleActionHeader->getValue());
        }
    }

    /**
     * Verifies unknown fields are preserved in the raw response.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetUnknownFieldFromEvent()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_GET_EVENT_UNKNOWN_FIELD));

        $response = $this->api->getEventWithHttpInfo(MockHelper::MOCK_EVENT_ID)[1];
        $responseBody = Utils::jsonDecode($response->getBody()->getContents());
        $this->assertEquals('unknown_field_value', $responseBody->unknown_field);
        $this->assertEquals('unknown_sub_field_value', $responseBody->browser_details->unknown_sub_field);
    }

    /**
     * Verifies getEventAsync sends correct request.
     */
    public function testGetEventAsync()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_GET_EVENT));

        $actual = json_decode(MockHelper::getMockData(MockHelper::OPERATION_GET_EVENT)['contents']);
        $event = $this->api->getEventAsync(MockHelper::MOCK_EVENT_ID)->wait();

        $this->assertEvent($event, $actual);
    }

    /**
     * Verifies getEvent throws 400 for invalid event_id.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent400InvalidEventId()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_400_INVALID_EVENT_ID));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->api->getEvent('invalid');
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $errorDetails->getError()->getCode());
            $this->assertEquals('invalid event_id', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 400 when ruleset not found.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent400RulesetNotFound()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_400_RULESET_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->api->getEvent(MockHelper::MOCK_EVENT_ID, 'invalid');
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::RULESET_NOT_FOUND, $errorDetails->getError()->getCode());
            $this->assertEquals('ruleset not found', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 403 for secret api key required error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent403SecretApiKeyRequired()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SECRET_API_KEY_REQUIRED));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->getEvent(MockHelper::MOCK_EVENT_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SECRET_API_KEY_REQUIRED, $errorDetails->getError()->getCode());
            $this->assertEquals('secret API key in header is missing or empty', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 403 for secret api key not found error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent403SecretApiKeyNotFound()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SECRET_API_KEY_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->getEvent(MockHelper::MOCK_EVENT_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SECRET_API_KEY_NOT_FOUND, $errorDetails->getError()->getCode());
            $this->assertEquals('no fingerprint workspace found for specified secret API key', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 404 for event not found error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent404EventNotFoundError()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_404_EVENT_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);

        try {
            $this->api->getEvent(MockHelper::MOCK_EVENT_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::EVENT_NOT_FOUND, $errorDetails->getError()->getCode());
            $this->assertEquals('event id not found', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 429 for too many requests error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent429TooManyRequestsError()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_429_TOO_MANY_REQUESTS));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(429);

        try {
            $this->api->getEvent(MockHelper::MOCK_EVENT_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::TOO_MANY_REQUESTS, $errorDetails->getError()->getCode());
            $this->assertEquals('too many requests', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 500 for internal error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent500Error()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_500_INTERNAL_SERVER_ERROR));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(500);

        try {
            $this->api->getEvent(MockHelper::MOCK_EVENT_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::FAILED, $errorDetails->getError()->getCode());
            $this->assertEquals('internal server error', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 504.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testGetEvent504Error()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_504_GATEWAY_TIMEOUT));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(504);

        try {
            $this->api->getEvent(MockHelper::MOCK_EVENT_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::FAILED, $errorDetails->getError()->getCode());
            $this->assertEquals('gateway timeout', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent sends correct request body and method.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testUpdateEvent()
    {
        $this->mockHandler->append(new Response(200));
        $body = new EventUpdate([
            'linked_id' => 'test',
            'tags' => [
                'test' => 'true',
            ],
            'suspect' => false,
        ]);
        $this->api->updateEvent(MockHelper::MOCK_EVENT_ID, $body);

        $req = $this->mockHandler->getLastRequest();
        $this->assertEquals($req->getBody()->getContents(), json_encode([
            'linked_id' => 'test',
            'tags' => [
                'test' => 'true',
            ],
            'suspect' => false,
        ]));
        $this->assertEquals('PATCH', $req->getMethod());
    }

    /**
     * Verifies updateEventAsync sends correct request body and method.
     */
    public function testUpdateEventAsync()
    {
        $this->mockHandler->append(new Response(200));
        $body = new EventUpdate([
            'linked_id' => 'test',
            'tags' => [
                'test' => 'true',
            ],
            'suspect' => false,
        ]);
        $this->api->updateEventAsync(MockHelper::MOCK_EVENT_ID, $body)->wait();

        $req = $this->mockHandler->getLastRequest();
        $this->assertEquals($req->getBody()->getContents(), json_encode([
            'linked_id' => 'test',
            'tags' => [
                'test' => 'true',
            ],
            'suspect' => false,
        ]));
        $this->assertEquals('PATCH', $req->getMethod());
    }

    /**
     * Verifies getEvent throws 400 for invalid request body.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testUpdateEvent400RequestBodyInvalid()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_400_REQUEST_BODY_INVALID));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->api->updateEvent(MockHelper::MOCK_EVENT_ID, new EventUpdate());
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $errorDetails->getError()->getCode());
            $this->assertEquals('request body is not valid', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 403 for secret api key required error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testUpdateEvent403SecretApiKeyRequired()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SECRET_API_KEY_REQUIRED));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->updateEvent(MockHelper::MOCK_EVENT_ID, new EventUpdate());
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SECRET_API_KEY_REQUIRED, $errorDetails->getError()->getCode());
            $this->assertEquals('secret API key in header is missing or empty', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 403 for secret api key not found error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testUpdateEvent403SecretApiKeyNotFound()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SECRET_API_KEY_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->updateEvent(MockHelper::MOCK_EVENT_ID, new EventUpdate());
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SECRET_API_KEY_NOT_FOUND, $errorDetails->getError()->getCode());
            $this->assertEquals('no fingerprint workspace found for specified secret API key', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 403 for wrong region error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testUpdateEvent403WrongRegion()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_WRONG_REGION));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->updateEvent(MockHelper::MOCK_EVENT_ID, new EventUpdate());
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::WRONG_REGION, $errorDetails->getError()->getCode());
            $this->assertEquals('wrong region', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 404 for event not found error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testUpdateEvent404EventNotFoundError()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_404_EVENT_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);

        try {
            $this->api->updateEvent('invalid', new EventUpdate());
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::EVENT_NOT_FOUND, $errorDetails->getError()->getCode());
            $this->assertEquals('event id not found', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 409 for state not ready error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testUpdateEvent409StateNotReadyError()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_409_STATE_NOT_READY));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(409);

        try {
            $this->api->updateEvent(MockHelper::MOCK_EVENT_ID, new EventUpdate());
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::STATE_NOT_READY, $errorDetails->getError()->getCode());
            $this->assertEquals('resource is not mutable yet, try again', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents sends only limit query parameter.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEventsWithOnlyLimit()
    {
        $this->mockHandler->append(function (RequestInterface $request) {
            $queryArray = $this->parseQueryString($request->getUri()->getQuery());
            $this->assertCount(2, $queryArray);
            $this->assertEquals('15', $queryArray['limit']);

            return MockHelper::getMockResponse(MockHelper::OPERATION_SEARCH_EVENTS);
        });

        $events = $this->api->searchEvents(15)->getEvents();

        $this->assertCount(1, $events);
        /* @noinspection SpellCheckingInspection */
        $this->assertEquals('Ibk1527CUFmcnjLwIs4A9', $events[0]->getIdentification()->getVisitorId());
    }

    /**
     * Verifies searchEventsRequest create query string with correct format for environment filtering.
     */
    public function testSearchEventsRequestMultipleEnvironment(): void
    {
        $request = $this->api->searchEventsRequest(environment: ['env1', 'env2']);
        $this->assertStringContainsString('environment=env1&environment=env2', $request->getUri()->getQuery());
    }

    /**
     * Verifies searchEvents sends subset of query parameters.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEventsWithPartialParams()
    {
        $this->mockHandler->append(function (RequestInterface $request) {
            $queryArray = $this->parseQueryString($request->getUri()->getQuery());
            $this->assertCount(5, $queryArray);
            $this->assertEquals('15', $queryArray['limit']);
            $this->assertEquals('true', $queryArray['reverse']);
            $this->assertEquals('linked_id', $queryArray['linked_id']);
            $this->assertEquals(MockHelper::MOCK_VISITOR_ID, $queryArray['visitor_id']);

            return MockHelper::getMockResponse(MockHelper::OPERATION_SEARCH_EVENTS);
        });

        $events = $this->api->searchEvents(15, visitor_id: MockHelper::MOCK_VISITOR_ID, linked_id: 'linked_id', reverse: true)->getEvents();

        $this->assertCount(1, $events);

        /** @noinspection SpellCheckingInspection */
        $visitorId = 'Ibk1527CUFmcnjLwIs4A9';
        $this->assertEquals($visitorId, $events[0]->getIdentification()->getVisitorId());
    }

    /**
     * Verifies searchEvents sends all query parameters correctly.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEventsWithAllParams()
    {
        $startDate = new \DateTime('2020-01-01 00:00:00');
        $endDate = new \DateTime('2020-01-02 00:00:00');

        $expected = [
            'limit' => 15,
            'pagination_key' => 'pagination',
            'visitor_id' => MockHelper::MOCK_VISITOR_ID,
            'high_recall_id' => 'high_recall_id',
            'bot' => [SearchEventsBot::GOOD, 'good'],
            'bot_info' => [SearchEventsBotInfo::ALL, 'all'],
            'bot_info_confidence' => [[BotInfoConfidence::HIGH, BotInfoConfidence::MEDIUM], ['high', 'medium']],
            'bot_info_provider' => [['google', 'openai'], ['google', 'openai']],
            'bot_info_name' => [['gemini', 'chatgpt'], ['gemini', 'chatgpt']],
            'ip_address' => '127.0.0.1/16',
            'asn' => 'asn',
            'linked_id' => 'linked_id',
            'url' => 'url',
            'bundle_id' => 'bundle_id',
            'package_name' => 'package_name',
            'origin' => 'origin',
            'start' => [$startDate, '2020-01-01T00:00:00.000+00:00'],
            'end' => $endDate->getTimestamp(),
            'reverse' => true,
            'suspect' => true,
            'vpn' => true,
            'virtual_machine' => true,
            'tampering' => true,
            'anti_detect_browser' => true,
            'incognito' => true,
            'privacy_settings' => true,
            'jailbroken' => true,
            'frida' => true,
            'factory_reset' => true,
            'cloned_app' => true,
            'emulator' => true,
            'root_apps' => true,
            'vpn_confidence' => [SearchEventsVpnConfidence::MEDIUM, 'medium'],
            'min_suspect_score' => '0.5',
            'developer_tools' => true,
            'location_spoofing' => true,
            'mitm_attack' => true,
            'proxy' => true,
            'sdk_version' => 'testSdkVersion',
            'sdk_platform' => [SearchEventsSdkPlatform::JS, 'js'],
            'environment' => [['env1', 'env2'], ['env1', 'env2']],
            'proximity_id' => 'testProximityId',
            'total_hits' => 100,
            'tor_node' => true,
            'incremental_identification_status' => [SearchEventsIncrementalIdentificationStatus::COMPLETED, 'completed'],
            'simulator' => true,
            'source' => [[SearchEventsSource::EDGE], ['edge']],
            'active_call' => false,
        ];

        $this->mockHandler->append(function (RequestInterface $request) use ($expected) {
            $queryArray = $this->parseQueryString($request->getUri()->getQuery());

            $extraKeys = ['ii'];

            $this->assertCount(count($expected) + count($extraKeys), $queryArray);
            foreach ($expected as $query => $value) {
                $this->assertArrayHasKey($query, $queryArray, "Missing query parameter: $query");
                $expectedValue = is_array($value) ? $value[1] : (is_bool($value) ? ($value ? 'true' : 'false') : $value);
                if (is_array($expectedValue)) {
                    $actualValue = is_array($queryArray[$query]) ? $queryArray[$query] : [$queryArray[$query]];
                    $expectedValueItems = implode(', ', $expectedValue);
                    $queryItems = implode(', ', $actualValue);
                    $this->assertArrayIsEqualToArrayIgnoringListOfKeys($expectedValue, $actualValue, [], "Array query parameter '$query' expected to have '$expectedValueItems' got '$queryItems'");
                } else {
                    $this->assertEquals($expectedValue, $queryArray[$query], "Query parameter '$query' expected '$expectedValue' got '$queryArray[$query]'");
                }
            }

            return MockHelper::getMockResponse(MockHelper::OPERATION_SEARCH_EVENTS);
        });

        $events = $this->api->searchEvents(
            limit: $expected['limit'],
            pagination_key: $expected['pagination_key'],
            visitor_id: $expected['visitor_id'],
            high_recall_id: $expected['high_recall_id'],
            bot: $expected['bot'][0],
            bot_info: $expected['bot_info'][0],
            bot_info_confidence: $expected['bot_info_confidence'][0],
            bot_info_provider: $expected['bot_info_provider'][0],
            bot_info_name: $expected['bot_info_name'][0],
            ip_address: $expected['ip_address'],
            asn: $expected['asn'],
            linked_id: $expected['linked_id'],
            url: $expected['url'],
            bundle_id: $expected['bundle_id'],
            package_name: $expected['package_name'],
            origin: $expected['origin'],
            start: $expected['start'][0],
            end: $expected['end'],
            reverse: $expected['reverse'],
            suspect: $expected['suspect'],
            vpn: $expected['vpn'],
            virtual_machine: $expected['virtual_machine'],
            tampering: $expected['tampering'],
            anti_detect_browser: $expected['anti_detect_browser'],
            incognito: $expected['incognito'],
            privacy_settings: $expected['privacy_settings'],
            jailbroken: $expected['jailbroken'],
            frida: $expected['frida'],
            factory_reset: $expected['factory_reset'],
            cloned_app: $expected['cloned_app'],
            emulator: $expected['emulator'],
            root_apps: $expected['root_apps'],
            vpn_confidence: $expected['vpn_confidence'][0],
            min_suspect_score: $expected['min_suspect_score'],
            developer_tools: $expected['developer_tools'],
            location_spoofing: $expected['location_spoofing'],
            mitm_attack: $expected['mitm_attack'],
            proxy: $expected['proxy'],
            sdk_version: $expected['sdk_version'],
            sdk_platform: $expected['sdk_platform'][0],
            environment: $expected['environment'],
            proximity_id: $expected['proximity_id'],
            total_hits: $expected['total_hits'],
            tor_node: $expected['tor_node'],
            incremental_identification_status: $expected['incremental_identification_status'][0],
            simulator: $expected['simulator'],
            source: $expected['source'][0],
            active_call: $expected['active_call'],
        )->getEvents();

        $this->assertCount(1, $events);
        /* @noinspection SpellCheckingInspection */
        $this->assertEquals('Ibk1527CUFmcnjLwIs4A9', $events[0]->getIdentification()->getVisitorId());
    }

    /**
     * Verifies searchEventsAsync sends correct request.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function testSearchEventsAsyncWithOnlyLimit()
    {
        $this->mockHandler->append(function (RequestInterface $request) {
            $queryArray = $this->parseQueryString($request->getUri()->getQuery());
            $this->assertCount(2, $queryArray);
            $this->assertEquals('15', $queryArray['limit']);

            return MockHelper::getMockResponse(MockHelper::OPERATION_SEARCH_EVENTS);
        });

        $events = $this->api->searchEventsAsync(15)->wait()->getEvents();

        $this->assertCount(1, $events);
        /* @noinspection SpellCheckingInspection */
        $this->assertEquals('Ibk1527CUFmcnjLwIs4A9', $events[0]->getIdentification()->getVisitorId());
    }

    /**
     * Verifies searchEvents throws 400 for invalid visitor id.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEvents400VisitorIdInvalid()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_400_VISITOR_ID_INVALID));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->api->searchEvents(visitor_id: 'invalid');
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $errorDetails->getError()->getCode());
            $this->assertEquals('invalid visitor id', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents throws 403 for secret api key required error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEvents403SecretApiKeyRequired()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SECRET_API_KEY_REQUIRED));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->searchEvents();
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SECRET_API_KEY_REQUIRED, $errorDetails->getError()->getCode());
            $this->assertEquals('secret API key in header is missing or empty', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents throws 403 for secret api key not found error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEvents403SecretApiKeyNotFound()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SECRET_API_KEY_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->searchEvents();
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SECRET_API_KEY_NOT_FOUND, $errorDetails->getError()->getCode());
            $this->assertEquals('no fingerprint workspace found for specified secret API key', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents throws 403 for wrong region error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEvents403WrongRegion()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_WRONG_REGION));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->searchEvents();
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::WRONG_REGION, $errorDetails->getError()->getCode());
            $this->assertEquals('wrong region', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents throws 429 too many requests.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEvents429Error()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_429_TOO_MANY_REQUESTS));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(429);

        try {
            $this->api->searchEvents();
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::TOO_MANY_REQUESTS, $errorDetails->getError()->getCode());
            $this->assertEquals('too many requests', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents throws 500 for internal error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEvents500Error()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_500_INTERNAL_SERVER_ERROR));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(500);

        try {
            $this->api->searchEvents();
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::FAILED, $errorDetails->getError()->getCode());
            $this->assertEquals('internal server error', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents throws 504.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testSearchEvents504Error()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_504_GATEWAY_TIMEOUT));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(504);

        try {
            $this->api->searchEvents();
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::FAILED, $errorDetails->getError()->getCode());
            $this->assertEquals('gateway timeout', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData sends correct request.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testDeleteVisitorData()
    {
        $visitorId = MockHelper::MOCK_VISITOR_ID;
        $this->mockHandler->append(function (RequestInterface $request) use ($visitorId) {
            $this->assertStringContainsString("/visitors/$visitorId", $request->getUri()->getPath());
            $this->assertEquals('DELETE', $request->getMethod());

            return new Response(200);
        });

        $this->api->deleteVisitorData($visitorId);
    }

    /**
     * Verifies deleteVisitorDataAsync sends correct request.
     */
    public function testDeleteVisitorDataAsync()
    {
        $visitorId = MockHelper::MOCK_VISITOR_ID;
        $this->mockHandler->append(function (RequestInterface $request) use ($visitorId) {
            $this->assertStringContainsString("/visitors/$visitorId", $request->getUri()->getPath());
            $this->assertEquals('DELETE', $request->getMethod());

            return new Response(200);
        });

        $this->api->deleteVisitorDataAsync($visitorId)->wait();
    }

    /**
     * Verifies deleteVisitorData throws 400 for incorrect visitor ID.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData400IncorrectVisitorId()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_400_VISITOR_ID_INVALID));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->api->deleteVisitorData('invalid');
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals('invalid visitor id', $errorDetails->getError()->getMessage());
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $errorDetails->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 400 for empty visitor ID.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData400VisitorIdRequired()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_400_VISITOR_ID_REQUIRED));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->api->deleteVisitorData('');
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals('visitor id is required', $errorDetails->getError()->getMessage());
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $errorDetails->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for secret api key required error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testDeleteVisitorData403SecretApiKeyRequired()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SECRET_API_KEY_REQUIRED));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->deleteVisitorData(MockHelper::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SECRET_API_KEY_REQUIRED, $errorDetails->getError()->getCode());
            $this->assertEquals('secret API key in header is missing or empty', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for secret api key not found error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testDeleteVisitorData403SecretApiKeyNotFound()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SECRET_API_KEY_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->deleteVisitorData(MockHelper::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SECRET_API_KEY_NOT_FOUND, $errorDetails->getError()->getCode());
            $this->assertEquals('no fingerprint workspace found for specified secret API key', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for feature not enabled.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData403FeatureNotEnabledError()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_FEATURE_NOT_ENABLED));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->deleteVisitorData(MockHelper::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::FEATURE_NOT_ENABLED, $errorDetails->getError()->getCode());
            $this->assertEquals('feature not enabled', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for inactive subscription.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData403SubscriptionIsNotActive()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_403_SUBSCRIPTION_IS_NOT_ACTIVE));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->api->deleteVisitorData(MockHelper::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::SUBSCRIPTION_NOT_ACTIVE, $errorDetails->getError()->getCode());
            $this->assertEquals('forbidden', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 404 for visitor not found.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData404Error()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_404_VISITOR_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);

        try {
            $this->api->deleteVisitorData(MockHelper::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::VISITOR_NOT_FOUND, $errorDetails->getError()->getCode());
            $this->assertEquals('visitor not found', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 429 for too many requests error.
     *
     * @throws GuzzleException
     * @throws \DateMalformedStringException
     */
    public function testDeleteVisitorData429TooManyRequestsError()
    {
        $this->mockHandler->append(MockHelper::getMockResponse(MockHelper::OPERATION_ERROR_429_TOO_MANY_REQUESTS));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(429);

        try {
            $this->api->deleteVisitorData(MockHelper::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));

            /** @var ErrorResponse $errorDetails */
            $errorDetails = $e->getErrorDetails();
            $this->assertEquals(ErrorCode::TOO_MANY_REQUESTS, $errorDetails->getError()->getCode());
            $this->assertEquals('too many requests', $errorDetails->getError()->getMessage());

            throw $e;
        }
    }

    /**
     * @throws \DateMalformedStringException
     * @throws GuzzleException
     */
    public function testGetEventConnectException(): void
    {
        $this->mockHandler->append(new ConnectException(
            'Connection refused',
            new Request('GET', '/events/test')
        ));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessageMatches('/Connection refused/');

        $this->api->getEvent('test');
    }

    /**
     * @throws \DateMalformedStringException
     * @throws GuzzleException
     */
    public function testSearchEventsConnectException(): void
    {
        $this->mockHandler->append(new ConnectException(
            'DNS resolution failed',
            new Request('GET', '/events/search')
        ));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessageMatches('/DNS resolution failed/');

        $this->api->searchEvents(10);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws GuzzleException
     */
    public function testDeleteVisitorDataConnectException(): void
    {
        $this->mockHandler->append(new ConnectException(
            'Connection timed out',
            new Request('DELETE', '/visitors/test')
        ));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessageMatches('/Connection timed out/');

        $this->api->deleteVisitorData('test-visitor');
    }

    /**
     * @throws \DateMalformedStringException
     * @throws GuzzleException
     */
    public function testUpdateEventConnectException(): void
    {
        $this->mockHandler->append(new ConnectException(
            'Network unreachable',
            new Request('PATCH', '/events/test')
        ));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessageMatches('/Network unreachable/');

        $this->api->updateEvent('test', new EventUpdate());
    }

    /**
     * @throws \DateMalformedStringException
     * @throws GuzzleException
     * @throws ApiException
     */
    public function testCreateHttpClientOptionWithDebug(): void
    {
        $debugFile = tempnam(sys_get_temp_dir(), 'fp_debug_');
        // tempnam() creates the file; remove it so the request path must recreate/write it.
        unlink($debugFile);

        $this->configuration->setDebug(true);
        $this->configuration->setDebugFile($debugFile);

        $this->mockHandler->append(new Response(200, [], '{}'));

        try {
            // The debug option is applied internally when making a request.
            $this->api->deleteVisitorData('test');
            $this->assertFileExists($debugFile);
        } finally {
            if (file_exists($debugFile)) {
                unlink($debugFile);
            }
        }
    }

    /**
     * @throws \DateMalformedStringException
     * @throws GuzzleException
     * @throws ApiException
     */
    public function testCreateHttpClientOptionWithCertFile(): void
    {
        $certFile = tempnam(sys_get_temp_dir(), 'fp_cert_');
        $this->configuration->setCertFile($certFile);

        $this->mockHandler->append(new Response(200, [], '{}'));
        $this->api->deleteVisitorData('test');
        // If we get here without error, cert option was accepted
        $this->assertTrue(true);
        unlink($certFile);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws GuzzleException
     * @throws ApiException
     */
    public function testCreateHttpClientOptionWithKeyFile(): void
    {
        $keyFile = tempnam(sys_get_temp_dir(), 'fp_key_');
        $this->configuration->setKeyFile($keyFile);

        $this->mockHandler->append(new Response(200, [], '{}'));
        $this->api->deleteVisitorData('test');
        $this->assertTrue(true);
        unlink($keyFile);
    }

    public function testCreateHttpClientOptionDebugFileOpenFailure(): void
    {
        $this->configuration->setDebug(true);
        $this->configuration->setDebugFile('/nonexistent/directory/debug.log');

        $this->mockHandler->append(new Response(200, [], '{}'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Failed to open the debug file/');

        $this->api->deleteVisitorData('test');
    }

    public function testSearchEventsRequestLimitTooHigh(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->api->searchEventsRequest(limit: 101);
    }

    public function testSearchEventsRequestLimitTooLow(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->api->searchEventsRequest(limit: 0);
    }

    public function testSearchEventsRequestTotalHitsTooHigh(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->api->searchEventsRequest(total_hits: 1001);
    }

    public function testSearchEventsRequestTotalHitsTooLow(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->api->searchEventsRequest(total_hits: 0);
    }

    public function testSearchEventsRequestSourceTooMany(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->api->searchEventsRequest(source: [SearchEventsSource::EDGE, SearchEventsSource::EDGE]);
    }

    public function testGetEventWithInvalidJson(): void
    {
        $this->mockHandler->append(new Response(200, [], 'not-valid-json'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessageMatches('/Error JSON decoding/');

        $this->api->getEvent('test');
    }

    public function testDeleteVisitorDataNon2xxStatusCode(): void
    {
        $this->mockHandler->append(new Response(301, [], ''));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(301);

        $this->api->deleteVisitorData('test');
    }

    public function testGetEventNon2xxStatusCode(): void
    {
        $this->mockHandler->append(new Response(301, [], ''));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(301);

        $this->api->getEvent('test');
    }

    public function testSearchEventsNon2xxStatusCode(): void
    {
        $this->mockHandler->append(new Response(301, [], ''));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(301);

        $this->api->searchEvents(10);
    }

    public function testUpdateEventNon2xxStatusCode(): void
    {
        $this->mockHandler->append(new Response(301, [], ''));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(301);

        $this->api->updateEvent('test', new EventUpdate());
    }

    /**
     * Verifies getEventRequest encodes a path-traversal event_id into a single
     * path segment rather than letting it escape to a sibling resource.
     */
    public function testGetEventRequestEncodesPathTraversalEventId(): void
    {
        $request = $this->api->getEventRequest('../events');

        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/v4/events/..%2Fevents', $request->getUri()->getPath());
    }

    /**
     * Verifies a hostname-shaped event_id is treated as an opaque path segment
     * and never changes the request's target host.
     */
    public function testGetEventRequestDoesNotRedirectHostForEvilEventId(): void
    {
        $request = $this->api->getEventRequest('domain.tld');

        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/v4/events/domain.tld', $request->getUri()->getPath());
    }

    /**
     * Verifies an empty event_id still produces a distinct trailing segment
     * instead of collapsing onto the bare /events collection endpoint.
     */
    public function testGetEventRequestWithEmptyEventIdDoesNotCallCollectionEndpoint(): void
    {
        $request = $this->api->getEventRequest('');

        $this->assertNotSame('/v4/events', $request->getUri()->getPath());
        $this->assertSame('/v4/events/', $request->getUri()->getPath());
    }

    /**
     * Verifies updateEventRequest encodes a path-traversal event_id the same
     * way as the read path.
     */
    public function testUpdateEventRequestEncodesPathTraversalEventId(): void
    {
        $request = $this->api->updateEventRequest('../events', new EventUpdate());

        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/v4/events/..%2Fevents', $request->getUri()->getPath());
    }

    /**
     * Verifies a hostname-shaped event_id passed to updateEvent never changes
     * the request's target host.
     */
    public function testUpdateEventRequestDoesNotRedirectHostForEvilEventId(): void
    {
        $request = $this->api->updateEventRequest('domain.tld', new EventUpdate());

        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/v4/events/domain.tld', $request->getUri()->getPath());
    }

    /**
     * Verifies deleteVisitorDataRequest encodes a path-traversal visitor_id
     * into a single path segment rather than letting it escape to a sibling
     * resource.
     */
    public function testDeleteVisitorDataRequestEncodesPathTraversalVisitorId(): void
    {
        $request = $this->api->deleteVisitorDataRequest('../visitors');

        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/v4/visitors/..%2Fvisitors', $request->getUri()->getPath());
    }

    /**
     * Verifies a hostname-shaped visitor_id is treated as an opaque path
     * segment and never changes the request's target host.
     */
    public function testDeleteVisitorDataRequestDoesNotRedirectHostForEvilVisitorId(): void
    {
        $request = $this->api->deleteVisitorDataRequest('domain.tld');

        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/v4/visitors/domain.tld', $request->getUri()->getPath());
    }

    /**
     * Verifies an empty visitor_id still produces a distinct trailing segment
     * instead of collapsing onto the bare /visitors collection endpoint.
     */
    public function testDeleteVisitorDataRequestWithEmptyVisitorIdDoesNotCallCollectionEndpoint(): void
    {
        $request = $this->api->deleteVisitorDataRequest('');

        $this->assertNotSame('/v4/visitors', $request->getUri()->getPath());
        $this->assertSame('/v4/visitors/', $request->getUri()->getPath());
    }

    private function parseQueryString(string $query): array
    {
        $queryArray = [];
        foreach (explode('&', $query) as $pair) {
            [$key, $value] = explode('=', $pair, 2) + [1 => ''];
            $key = urldecode($key);
            $value = urldecode($value);
            if (isset($queryArray[$key])) {
                if (!is_array($queryArray[$key])) {
                    $queryArray[$key] = [$queryArray[$key]];
                }
                $queryArray[$key][] = $value;
            } else {
                $queryArray[$key] = $value;
            }
        }

        return $queryArray;
    }

    private function assertEvent(Event $event, \stdClass $actual): void
    {
        $this->assertEquals(MockHelper::MOCK_EVENT_ID, $event->getEventId());
        $this->assertEquals($actual->timestamp, $event->getTimestamp());
        $this->assertEquals(EventSource::DEVICE, $event->getSource());
        $this->assertEquals(IncrementalIdentificationStatus::COMPLETED, $event->getIncrementalIdentificationStatus());
        $this->assertEquals($actual->linked_id, $event->getLinkedId());
        $this->assertEquals(null, $event->getEnvironmentId());
        $this->assertEquals(null, $event->getSuspect());

        $sdk = $event->getSdk();
        $actualSdk = $actual->sdk;
        $this->assertEquals(SDK::class, get_class($sdk));
        $this->assertEquals($actualSdk->platform, $sdk->getPlatform());
        $this->assertEquals($actualSdk->version, $sdk->getVersion());
        $sdkIntegrations = $sdk->getIntegrations();
        $this->assertCount(1, $sdkIntegrations);
        $firstSdkIntegration = $sdkIntegrations[0];
        $actualIntegration = $actualSdk->integrations[0];
        $this->assertEquals($actualIntegration->name, $firstSdkIntegration->getName());
        $this->assertEquals($actualIntegration->version, $firstSdkIntegration->getVersion());
        $subIntegration = $firstSdkIntegration->getSubintegration();
        $actualSubIntegration = $actualIntegration->subintegration;
        $this->assertEquals($actualSubIntegration->name, $subIntegration->getName());
        $this->assertEquals($actualSubIntegration->version, $subIntegration->getVersion());

        $this->assertEquals($actual->replayed, $event->getReplayed());

        $identification = $event->getIdentification();
        $actualIdentification = $actual->identification;
        $this->assertEquals(Identification::class, get_class($identification));
        $this->assertEquals($actualIdentification->visitor_id, $identification->getVisitorId());
        $this->assertEquals($actualIdentification->visitor_found, $identification->getVisitorFound());
        $this->assertEquals($actualIdentification->first_seen_at, $identification->getFirstSeenAt());
        $this->assertEquals($actualIdentification->last_seen_at, $identification->getLastSeenAt());
        $identificationConfidence = $identification->getConfidence();
        $actualIdentificationConfidence = $actualIdentification->confidence;
        $this->assertEquals(IdentificationConfidence::class, get_class($identificationConfidence));
        $this->assertEquals($actualIdentificationConfidence->score, $identificationConfidence->getScore());
        $this->assertEquals($actualIdentificationConfidence->version, $identificationConfidence->getVersion());

        $supplementaryIdHighRecall = $event->getSupplementaryIdHighRecall();
        $actualSupplementaryIdHighRecall = $actual->supplementary_id_high_recall;
        $this->assertEquals(SupplementaryIDHighRecall::class, get_class($supplementaryIdHighRecall));
        $this->assertEquals($actualSupplementaryIdHighRecall->visitor_id, $supplementaryIdHighRecall->getVisitorId());
        $this->assertEquals($actualSupplementaryIdHighRecall->visitor_found, $supplementaryIdHighRecall->getVisitorFound());
        $this->assertEquals($actualSupplementaryIdHighRecall->first_seen_at, $supplementaryIdHighRecall->getFirstSeenAt());
        $this->assertEquals($actualSupplementaryIdHighRecall->last_seen_at, $supplementaryIdHighRecall->getLastSeenAt());
        $supplementaryIdHighRecallConfidence = $supplementaryIdHighRecall->getConfidence();
        $supplementaryIdHighRecallActualConfidence = $actualSupplementaryIdHighRecall->confidence;
        $this->assertEquals(IdentificationConfidence::class, get_class($supplementaryIdHighRecallConfidence));
        $this->assertEquals($supplementaryIdHighRecallActualConfidence->score, $supplementaryIdHighRecallConfidence->getScore());
        $this->assertEquals($supplementaryIdHighRecallActualConfidence->version, $supplementaryIdHighRecallConfidence->getVersion());

        $this->assertEquals([], $event->getTags());
        $this->assertEquals($actual->url, $event->getUrl());
        $this->assertEquals(null, $event->getBundleId());
        $this->assertEquals(null, $event->getPackageName());
        $this->assertEquals($actual->ip_address, $event->getIpAddress());
        $this->assertEquals($actual->user_agent, $event->getUserAgent());
        $this->assertEquals($actual->client_referrer, $event->getClientReferrer());

        $browserDetails = $event->getBrowserDetails();
        $actualBrowserDetails = $actual->browser_details;
        $this->assertEquals($actualBrowserDetails->browser_name, $browserDetails->getBrowserName());
        $this->assertEquals($actualBrowserDetails->browser_major_version, $browserDetails->getBrowserMajorVersion());
        $this->assertEquals($actualBrowserDetails->browser_full_version, $browserDetails->getBrowserFullVersion());
        $this->assertEquals($actualBrowserDetails->os, $browserDetails->getOs());
        $this->assertEquals($actualBrowserDetails->os_version, $browserDetails->getOsVersion());
        $this->assertEquals($actualBrowserDetails->device, $browserDetails->getDevice());

        $proximity = $event->getProximity();
        $actualProximity = $actual->proximity;
        $this->assertEquals($actualProximity->id, $proximity->getId());
        $this->assertEquals($actualProximity->precision_radius, $proximity->getPrecisionRadius());
        $this->assertEquals($actualProximity->confidence, $proximity->getConfidence());

        $this->assertEquals($actual->active_call, $event->getActiveCall());

        $this->assertEquals(BotResult::NOT_DETECTED, $event->getBot());
        $this->assertEquals(null, $event->getBotType());
        $this->assertEquals(null, $event->getBotInfo());
        $this->assertEquals($actual->cloned_app, $event->getClonedApp());
        $this->assertEquals($actual->developer_tools, $event->getDeveloperTools());
        $this->assertEquals($actual->emulator, $event->getEmulator());
        $this->assertEquals($actual->factory_reset_timestamp, $event->getFactoryResetTimestamp());
        $this->assertEquals($actual->frida, $event->getFrida());

        $ipBlocklist = $event->getIpBlocklist();
        $actualIpBlocklist = $actual->ip_blocklist;
        $this->assertEquals($actualIpBlocklist->email_spam, $ipBlocklist->getEmailSpam());
        $this->assertEquals($actualIpBlocklist->attack_source, $ipBlocklist->getAttackSource());
        $this->assertEquals($actualIpBlocklist->tor_node, $ipBlocklist->getTorNode());

        $this->assertFalse($event->getVpn());
        /* @noinspection SpellCheckingInspection */
        $this->assertEquals('Hlavni mesto Praha', $event->getIpInfo()->getV4()->getGeolocation()->getSubdivisions()[0]->getName());
        $this->assertFalse($event->getJailbroken());
        $this->assertFalse($event->getPrivacySettings());
        $this->assertFalse($event->getVirtualMachine());
        $this->assertFalse($event->getLocationSpoofing());
        $this->assertFalse($event->getHighActivityDevice());

        $rawDeviceAttributes = $event->getRawDeviceAttributes();
        $this->assertEquals(127, $rawDeviceAttributes->getArchitecture());
        $this->assertEquals(124.04347745512496, $rawDeviceAttributes->getAudio());
        $this->assertEquals('db3c1462576a399a03ae93d0ab9eb5c4', $rawDeviceAttributes->getCanvas()->getGeometry());
        $this->assertEquals('24', $rawDeviceAttributes->getColorDepth());
        $this->assertTrue($rawDeviceAttributes->getCookiesEnabled());
        $this->assertEquals(80, $rawDeviceAttributes->getBatteryLevel());
        $this->assertTrue($rawDeviceAttributes->getBatteryCharging());
        $this->assertTrue($rawDeviceAttributes->getBatteryLowPowerMode());
        $this->assertEquals('691e3845c85c202a1514b6fd7ef17065', $rawDeviceAttributes->getKeyboardLayoutHash());
        $this->assertEquals('en-US', $rawDeviceAttributes->getKeyboardLayoutName());

        $labels = $event->getLabels();
        $actualLabels = $actual->labels;
        $totalLabel = count($labels);
        $this->assertCount($totalLabel, $labels);
        for ($i = 0; $i < $totalLabel; ++$i) {
            $this->assertEquals($actualLabels[$i]->label, $labels[$i]->getLabel());
            $this->assertEquals($actualLabels[$i]->prediction, $labels[$i]->getPrediction());
            $this->assertEquals($actualLabels[$i]->ml_score, $labels[$i]->getMlScore());
        }
    }
}
