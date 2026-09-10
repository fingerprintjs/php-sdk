<?php

namespace Fingerprint\ServerAPI;

use DateTime;
use Fingerprint\ServerAPI\Api\FingerprintApi;
use Fingerprint\ServerAPI\Model\BotdBotResult;
use Fingerprint\ServerAPI\Model\ErrorCode;
use Fingerprint\ServerAPI\Model\ErrorPlainResponse;
use Fingerprint\ServerAPI\Model\ErrorResponse;
use Fingerprint\ServerAPI\Model\EventsUpdateRequest;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

/**
 * @internal
 *
 */
class FingerprintApiTest extends TestCase
{
    /** @noinspection SpellCheckingInspection */
    public const MOCK_REQUEST_ID = '1708102555327.NLOjmg';
    public const MOCK_REQUEST_ID_WITH_UNKNOWN = 'UNKNOWN_FIELD_REQUEST_ID';
    public const MOCK_REQUEST_ID_WITH_BROKEN = 'BROKEN_FIELD_REQUEST_ID';
    /** @noinspection SpellCheckingInspection */
    public const MOCK_EXTRA_FIELDS_REQUEST_ID = '0KSh65EnVoB85JBmloQK';
    public const MOCK_REQUEST_ALL_ERRORS = 'ALL_ERRORS';
    public const MOCK_REQUEST_EXTRA_FIELDS = 'EXTRA_FIELDS';
    /** @noinspection SpellCheckingInspection */
    public const MOCK_VISITOR_ID = 'AcxioeQKffpXF8iGQK3P';
    public const MOCK_VISITOR_REQUEST_ID = '1655373780901.HhjRFX';
    public const MOCK_VISITOR_ID_403_ERROR_FORBIDDEN = 'VISITOR_ID_403_ERROR';
    public const MOCK_VISITOR_ID_403_FEATURE_NOT_ENABLED = 'VISITOR_ID_403_FEATURE_NOT_ENABLED';
    public const MOCK_VISITOR_ID_403_TOKEN_NOT_FOUND = 'VISITOR_ID_403_TOKEN_NOT_FOUND';
    public const MOCK_VISITOR_ID_403_TOKEN_REQUIRED = 'VISITOR_ID_403_TOKEN_REQUIRED';
    public const MOCK_VISITOR_ID_403_WRONG_REGION = 'VISITOR_ID_403_WRONG_REGION';
    public const MOCK_VISITOR_ID_403_SUBSCRIPTION_NOT_ACTIVE = 'VISITOR_ID_403_SUBSCRIPTION_NOT_ACTIVE';
    public const MOCK_VISITOR_ID_429_ERROR = 'VISITOR_ID_429_ERROR';
    public const MOCK_ERROR_429_TOO_MANY_REQUESTS = 'VISITOR_ID_429_ERROR_DELETE';
    public const MOCK_EVENT_ID_403_TOKEN_REQUIRED = 'EVENT_ID_403_ERROR_TOKEN_REQUIRED';
    public const MOCK_EVENT_ID_403_TOKEN_NOT_FOUND = 'EVENT_ID_403_ERROR_TOKEN_NOT_FOUND';
    public const MOCK_EVENT_ID_403_WRONG_REGION = 'EVENT_ID_403_WRONG_REGION';
    public const MOCK_EVENT_ID_404_ERROR = 'EVENT_ID_404_ERROR';
    public const MOCK_VISITOR_ID_400_EMPTY_VISITOR_ID = 'VISITOR_ID_400_EMPTY_VISITOR_ID';
    public const MOCK_VISITOR_ID_400_INCORRECT_VISITOR_ID = 'VISITOR_ID_400_INCORRECT_VISITOR_ID';
    public const MOCK_VISITOR_ID_404_ERROR = 'VISITOR_ID_404_ERROR';
    public const MOCK_EVENT_ID_409_ERROR = 'EVENT_ID_409_ERROR';
    public const MOCK_EVENT_ID = 'MOCK_EVENT_ID';
    public const MOCK_EVENT_ID_400_ERROR = 'EVENT_ID_400_ERROR';
    public const MOCK_RELATED_VISITORS = 'MOCK_RELATED_VISITORS';

    protected FingerprintApi $fingerprint_api;

    protected ClientInterface $client;
    protected MockHandler $mockHandler;

    public function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $this->client = new Client(['handler' => HandlerStack::create($this->mockHandler)]);
        $this->fingerprint_api = new FingerprintApi($this->client);
    }

    /**
     * Verifies getEvent deserializes all product data correctly.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetEvent()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID));

        $event = $this->fingerprint_api->getEvent(self::MOCK_REQUEST_ID)[0];
        $products = $event->getProducts();
        $identification_product = $products->getIdentification();
        $botd_product = $products->getBotd();
        $vpn_product = $products->getVpn();
        $ip_info_product = $products->getIpInfo();
        $cloned_app_product = $products->getClonedApp();
        $factory_reset_product = $products->getFactoryReset();
        $jailbroken_product = $products->getJailbroken();
        $frida_product = $products->getFrida();
        $privacy_settings_product = $products->getPrivacySettings();
        $virtual_machine_product = $products->getVirtualMachine();
        $request_id = $identification_product->getData()->getRequestId();
        $this->assertEquals(self::MOCK_REQUEST_ID, $request_id);
        $this->assertEquals(BotdBotResult::NOT_DETECTED, $botd_product->getData()->getBot()->getResult());
        $this->assertFalse($vpn_product->getData()->getMethods()->getPublicVpn());
        $this->assertEquals('94.142.239.124', $ip_info_product->getData()->getV4()->getAddress());
        /** @noinspection SpellCheckingInspection */
        $this->assertEquals('Hlavni mesto Praha', $ip_info_product->getData()->getV4()->getGeolocation()->getSubdivisions()[0]->name);
        $this->assertFalse($cloned_app_product->getData()->getResult());
        $this->assertEquals(new DateTime('1970-01-01T00:00:00Z'), $factory_reset_product->getData()->getTime());
        $this->assertFalse($jailbroken_product->getData()->getResult());
        $this->assertFalse($frida_product->getData()->getResult());
        $this->assertFalse($privacy_settings_product->getData()->getResult());
        $this->assertFalse($virtual_machine_product->getData()->getResult());

        $raw_device_attributes = $products->getRawDeviceAttributes()->getData();
        $this->assertEquals(127, $raw_device_attributes['architecture']->value);
        $this->assertEquals(35.73832903057337, $raw_device_attributes['audio']->value);
        $this->assertEquals('4dce9d6017c3e0c052a77252f29f2b1c', $raw_device_attributes['canvas']->value->Geometry);
        $this->assertEquals('p3', $raw_device_attributes['colorGamut']->value);
        $this->assertTrue($raw_device_attributes['cookiesEnabled']->value);

        $location_spuffing = $products->getLocationSpoofing()->getData();
        $this->assertFalse($location_spuffing->getResult());

        $high_activity = $products->getHighActivity()->getData();
        $this->assertFalse($high_activity->getResult());
    }

    /**
     * Verifies getEvent handles extra fields in response without errors.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetEventWithExtraFields()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_EXTRA_FIELDS));

        $event = $this->fingerprint_api->getEvent(self::MOCK_REQUEST_EXTRA_FIELDS)[0];
        $products = $event->getProducts();
        $identification_product = $products->getIdentification();
        $request_id = $identification_product->getData()->getRequestId();
        $this->assertEquals(self::MOCK_EXTRA_FIELDS_REQUEST_ID, $request_id);
    }

    /**
     * Verifies getEvent deserializes error responses for all products.
     *
     * @throws ApiException
     * @throws SerializationException
     * @throws GuzzleException
     */
    public function testGetEventWithAllErrors()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ALL_ERRORS));

        $event = $this->fingerprint_api->getEvent(self::MOCK_REQUEST_ALL_ERRORS)[0];
        $products = $event->getProducts();
        $identification_error = $products->getIdentification()->getError();
        $botd_error = $products->getBotd()->getError();
        $emulator_error = $products->getEmulator()->getError();
        $incognito_error = $products->getIncognito()->getError();
        $tor_error = $products->getTor()->getError();
        $ip_blocklist_error = $products->getIpBlocklist()->getError();
        $ip_info_error = $products->getIpInfo()->getError();
        $proxy_error = $products->getProxy()->getError();
        $root_apps_error = $products->getRootApps()->getError();
        $tampering_error = $products->getTampering()->getError();
        $vpn_error = $products->getVpn()->getError();
        $cloned_app_error = $products->getClonedApp()->getError();
        $factory_reset_error = $products->getFactoryReset()->getError();
        $jailbroken_error = $products->getJailbroken()->getError();
        $frida_error = $products->getFrida()->getError();
        $privacy_settings_error = $products->getPrivacySettings()->getError();
        $virtual_machine_error = $products->getVirtualMachine()->getError();
        $this->assertEquals(ErrorCode::FAILED, $identification_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $botd_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $emulator_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $incognito_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $tor_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $ip_blocklist_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $ip_info_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $proxy_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $root_apps_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $tampering_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $vpn_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $cloned_app_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $factory_reset_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $jailbroken_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $frida_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $privacy_settings_error->getCode());
        $this->assertEquals(ErrorCode::FAILED, $virtual_machine_error->getCode());

        $raw_device_attributes = $products->getRawDeviceAttributes()->getData();
        $this->assertEquals('Error', $raw_device_attributes['audio']->error->name);
        $this->assertEquals('Error', $raw_device_attributes['canvas']->error->name);
    }

    /**
     * Verifies getVisits returns correct visitor ID.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetVisits()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID));

        $visits = $this->fingerprint_api->getVisits(self::MOCK_VISITOR_ID)[0];
        $this->assertEquals(self::MOCK_VISITOR_ID, $visits->getVisitorId());
    }

    /**
     * Verifies getVisits filters visits by request ID.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetVisitsByRequestId()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID, 'GET', self::MOCK_VISITOR_REQUEST_ID));

        $visits = $this->fingerprint_api->getVisits(self::MOCK_VISITOR_ID, self::MOCK_VISITOR_REQUEST_ID)[0];
        foreach ($visits->getVisits() as $visit) {
            $this->assertEquals(self::MOCK_VISITOR_REQUEST_ID, $visit->getRequestId());
        }
    }

    /**
     * Verifies getVisits respects the limit parameter.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetVisitsWithLimit()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID));

        $limit = 100;
        $visits = $this->fingerprint_api->getVisits(self::MOCK_VISITOR_ID, null, $limit)[0];
        $count = count($visits->getVisits());
        $this->assertLessThanOrEqual($limit, $count);
    }

    /**
     * Verifies getEvent raw response matches the mock JSON.
     *
     * @throws ApiException
     * @throws SerializationException
     * @throws GuzzleException
     */
    public function testGetEventRawResponse()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID));

        $response = $this->fingerprint_api->getEvent(self::MOCK_REQUEST_ID)[1];
        $mockedResult = Utils::jsonDecode(file_get_contents(__DIR__ . '/mocks/get_event_200.json'));
        $this->assertEquals($mockedResult, Utils::jsonDecode($response->getBody()->getContents()));
    }

    /**
     * Verifies nullable seenAt field is deserialized as null.
     *
     * @throws ApiException
     * @throws SerializationException
     * @throws GuzzleException
     */
    public function testGetEventNullableSeenAt()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID));

        $event = $this->fingerprint_api->getEvent(self::MOCK_REQUEST_ID)[0];
        $products = $event->getProducts();
        $seenAt = $products->getIdentification()->getData()->getLastSeenAt();
        $this->assertEquals(null, $seenAt->getGlobal());
    }

    /**
     * Verifies getVisits raw response matches the mock JSON.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetVisitsRawResponse()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID));

        $response = $this->fingerprint_api->getVisits(self::MOCK_VISITOR_ID)[1];
        $mockedResult = Utils::jsonDecode(file_get_contents(__DIR__ . '/mocks/get_visitors_200_limit_500.json'));
        $this->assertEquals($mockedResult, Utils::jsonDecode($response->getBody()->getContents()));
    }

    /**
     * Verifies unknown fields don't break model deserialization.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetEventParsedModelWithUnknownField()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID_WITH_UNKNOWN));

        $event = $this->fingerprint_api->getEvent(self::MOCK_REQUEST_ID_WITH_UNKNOWN)[0];
        $this->assertFalse($event->getProducts()->getIncognito()->getData()->getResult());
    }

    /**
     * Verifies unknown fields are preserved in the raw response.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetUnknownFieldFromEvent()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID_WITH_UNKNOWN));

        $response = $this->fingerprint_api->getEvent(self::MOCK_REQUEST_ID_WITH_UNKNOWN)[1];
        $responseBody = Utils::jsonDecode($response->getBody()->getContents());
        $this->assertEquals('field', $responseBody->unknown);
        $this->assertEquals('field', $responseBody->products->unknown);
        $this->assertEquals('field', $responseBody->products->identification->unknown);
        $this->assertEquals('field', $responseBody->products->identification->data->unknown);
    }

    /**
     * Verifies broken format throws SerializationException with accessible response.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function testGetBrokenFormatEvent()
    {
        $event = null;
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID_WITH_BROKEN));

        try {
            list($event, $response) = $this->fingerprint_api->getEvent(self::MOCK_REQUEST_ID_WITH_BROKEN);
        } catch (SerializationException $exception) {
            $response = $exception->getResponse();
        }
        $responseBody = Utils::jsonDecode($response->getBody()->getContents());

        $this->assertNull($event);
        $this->assertNotNull($responseBody);
        $this->assertEquals('format', $responseBody->products->identification->data->linkedId->broken);
    }

    /**
     * Verifies deleteVisitorData returns 200 on success.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testDeleteVisitorData()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID, 'DELETE'));

        $response = $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID)[1];
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Verifies getVisits throws 403 ApiException for forbidden access.
     *
     * @throws SerializationException
     * @throws GuzzleException
     */
    public function testGetVisits403Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_403_ERROR_FORBIDDEN));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->getVisits(self::MOCK_VISITOR_ID_403_ERROR_FORBIDDEN);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorPlainResponse::class, get_class($e->getErrorDetails()));

            throw $e;
        }
    }

    /**
     * Verifies getVisits throws 429 ApiException with retry-after header.
     *
     * @throws SerializationException
     * @throws GuzzleException
     */
    public function testGetVisits429Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_429_ERROR));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(429);

        try {
            $this->fingerprint_api->getVisits(self::MOCK_VISITOR_ID_429_ERROR);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorPlainResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(30, $e->getRetryAfter());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 403 for token required error.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testGetEvent403TokenRequired()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_403_TOKEN_REQUIRED));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->getEvent(self::MOCK_EVENT_ID_403_TOKEN_REQUIRED);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::TOKEN_REQUIRED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 403 for token not found error.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testGetEvent403TokenNotFound()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_403_TOKEN_NOT_FOUND));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->getEvent(self::MOCK_EVENT_ID_403_TOKEN_NOT_FOUND);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::TOKEN_NOT_FOUND, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 403 for wrong region error.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testGetEvent403WrongRegion()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_403_WRONG_REGION));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->getEvent(self::MOCK_EVENT_ID_403_WRONG_REGION);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::WRONG_REGION, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies getEvent throws 404 for request not found error.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testGetEvent404Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_404_ERROR));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);

        try {
            $this->fingerprint_api->getEvent(self::MOCK_EVENT_ID_404_ERROR);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals('request id is not found', $e->getErrorDetails()->getError()->getMessage());
            $this->assertEquals(ErrorCode::REQUEST_NOT_FOUND, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 400 for incorrect visitor ID.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData400IncorrectVisitorId()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_400_INCORRECT_VISITOR_ID, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID_400_INCORRECT_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals('invalid visitor id', $e->getErrorDetails()->getError()->getMessage());
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 400 for empty visitor ID.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData400EmptyVisitorId()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_400_EMPTY_VISITOR_ID, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID_400_EMPTY_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals('visitor id is required', $e->getErrorDetails()->getError()->getMessage());
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for feature not enabled.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData403FeatureNotEnabled()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_403_FEATURE_NOT_ENABLED, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID_403_FEATURE_NOT_ENABLED);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::FEATURE_NOT_ENABLED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for token not found.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData403TokenNotFound()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_403_TOKEN_NOT_FOUND, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID_403_TOKEN_NOT_FOUND);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::TOKEN_NOT_FOUND, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for token required.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData403TokenRequired()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_403_TOKEN_REQUIRED, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID_403_TOKEN_REQUIRED);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::TOKEN_REQUIRED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for wrong region.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData403WrongRegion()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_403_WRONG_REGION, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID_403_WRONG_REGION);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::WRONG_REGION, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 403 for inactive subscription.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData403SubscriptionIsNotActive()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_403_SUBSCRIPTION_NOT_ACTIVE, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID_403_SUBSCRIPTION_NOT_ACTIVE);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::SUBSCRIPTION_NOT_ACTIVE, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 404 for visitor not found.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData404Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_404_ERROR, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_VISITOR_ID_404_ERROR);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::VISITOR_NOT_FOUND, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies deleteVisitorData throws 429 with retry-after header.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testDeleteVisitorData429Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_ERROR_429_TOO_MANY_REQUESTS, 'DELETE'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(429);

        try {
            $this->fingerprint_api->deleteVisitorData(self::MOCK_ERROR_429_TOO_MANY_REQUESTS);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::TOO_MANY_REQUESTS, $e->getErrorDetails()->getError()->getCode());
            $this->assertEquals(30, $e->getRetryAfter());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent sends correct request body and method.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testUpdateEvent()
    {
        $this->mockHandler->append(new Response(200));
        $body = new EventsUpdateRequest([
            'linked_id' => 'test',
            'tag' => [
                'test' => 'true',
            ],
            'suspect' => false,
        ]);
        [, $res] = $this->fingerprint_api->updateEvent($body, self::MOCK_REQUEST_ID);

        $req = $this->mockHandler->getLastRequest();
        $this->assertEquals($req->getBody()->getContents(), json_encode([
            'linkedId' => 'test',
            'tag' => [
                'test' => 'true',
            ],
            'suspect' => false,
        ]));
        $this->assertEquals('PUT', $req->getMethod());

        $this->assertEquals(200, $res->getStatusCode());
    }

    /**
     * Verifies updateEvent throws 400 for invalid request body.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testUpdateEvent400Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_400_ERROR, 'PUT'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->fingerprint_api->updateEvent(new EventsUpdateRequest([]), self::MOCK_EVENT_ID_400_ERROR);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 403 for wrong region.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testUpdateEvent403WrongRegion()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_403_WRONG_REGION, 'PUT'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->updateEvent(new EventsUpdateRequest([]), self::MOCK_EVENT_ID_403_WRONG_REGION);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::WRONG_REGION, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 403 for token required.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testUpdateEvent403TokenRequired()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_403_TOKEN_REQUIRED, 'PUT'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->updateEvent(new EventsUpdateRequest([]), self::MOCK_EVENT_ID_403_TOKEN_REQUIRED);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::TOKEN_REQUIRED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 403 for token not found.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testUpdateEvent403TokenNotFound()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_403_TOKEN_NOT_FOUND, 'PUT'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->updateEvent(new EventsUpdateRequest([]), self::MOCK_EVENT_ID_403_TOKEN_NOT_FOUND);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::TOKEN_NOT_FOUND, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 404 for request not found.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testUpdateEvent404Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_404_ERROR, 'PUT'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);

        try {
            $this->fingerprint_api->updateEvent(new EventsUpdateRequest([]), self::MOCK_EVENT_ID_404_ERROR);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::REQUEST_NOT_FOUND, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies updateEvent throws 409 for state not ready.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testUpdateEvent409Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_EVENT_ID_409_ERROR, 'PUT'));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(409);

        try {
            $this->fingerprint_api->updateEvent(new EventsUpdateRequest([]), self::MOCK_EVENT_ID_409_ERROR);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::STATE_NOT_READY, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies getRelatedVisitors returns correct visitor IDs.
     *
     * @throws ApiException
     * @throws SerializationException
     * @throws GuzzleException
     */
    public function testGetRelatedVisitors()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_RELATED_VISITORS));

        list($relatedVisitors) = $this->fingerprint_api->getRelatedVisitors(self::MOCK_VISITOR_ID);

        /** @noinspection SpellCheckingInspection */
        $this->assertEquals('NtCUJGceWX9RpvSbhvOm', $relatedVisitors->getRelatedVisitors()[0]->getVisitorId());

        /** @noinspection SpellCheckingInspection */
        $this->assertEquals('25ee02iZwGxeyT0jMNkZ', $relatedVisitors->getRelatedVisitors()[1]->getVisitorId());
    }

    /**
     * Verifies getRelatedVisitors throws 400 for invalid request.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testGetRelatedVisitors400Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_400_EMPTY_VISITOR_ID));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->fingerprint_api->getRelatedVisitors(self::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies getRelatedVisitors throws 403 for feature not enabled.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testGetRelatedVisitors403Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_403_FEATURE_NOT_ENABLED));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->getRelatedVisitors(self::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::FEATURE_NOT_ENABLED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies getRelatedVisitors throws 404 for visitor not found.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testGetRelatedVisitors404Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID_404_ERROR));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);

        try {
            $this->fingerprint_api->getRelatedVisitors(self::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::VISITOR_NOT_FOUND, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies getRelatedVisitors throws 429 with retry-after header.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testGetRelatedVisitors429Error()
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_ERROR_429_TOO_MANY_REQUESTS));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(429);

        try {
            $this->fingerprint_api->getRelatedVisitors(self::MOCK_VISITOR_ID);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::TOO_MANY_REQUESTS, $e->getErrorDetails()->getError()->getCode());
            $this->assertEquals(30, $e->getRetryAfter());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents sends only the limit query parameter (plus the
     * `reverse` parameter, which now defaults to `false` and is therefore
     * always sent unless explicitly overridden).
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testSearchEventsWithOnlyLimit()
    {
        $this->mockHandler->append(function (RequestInterface $request) {
            $queryArray = [];
            parse_str($request->getUri()->getQuery(), $queryArray);
            $this->assertCount(3, $queryArray);
            $this->assertEquals('10', $queryArray['limit']);
            $this->assertEquals('false', $queryArray['reverse']);

            return $this->returnMockResponse("get_event_search_200.json");
        });

        list($events) = $this->fingerprint_api->searchEvents(10);

        $this->assertCount(1, $events->getEvents());
        /** @noinspection SpellCheckingInspection */
        $this->assertEquals("Ibk1527CUFmcnjLwIs4A9", $events->getEvents()[0]->getProducts()->getIdentification()->getData()->getVisitorId());
    }

    /**
     * Verifies searchEvents still accepts an explicit `null` for `reverse`.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testSearchEventsAcceptsNullReversePositionally()
    {
        $this->mockHandler->append(function (RequestInterface $request) {
            $queryArray = [];
            parse_str($request->getUri()->getQuery(), $queryArray);
            $this->assertArrayNotHasKey('reverse', $queryArray);
            $this->assertEquals('10', $queryArray['limit']);

            return $this->returnMockResponse("get_event_search_200.json");
        });

        // Intentional positional call to test behavior
        list($events) = $this->fingerprint_api->searchEvents(10, null, null, null, null, null, null, null, null);

        $this->assertCount(1, $events->getEvents());
    }

    /**
     * Verifies searchEvents sends subset of query parameters.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testSearchEventsWithPartialParams()
    {
        $this->mockHandler->append(function (RequestInterface $request) {
            $queryArray = [];
            parse_str($request->getUri()->getQuery(), $queryArray);
            $this->assertCount(5, $queryArray);
            $this->assertEquals('10', $queryArray['limit']);
            $this->assertEquals('true', $queryArray['reverse']);
            $this->assertEquals('linked_id', $queryArray['linked_id']);

            return $this->returnMockResponse("get_event_search_200.json");
        });

        list($events) = $this->fingerprint_api->searchEvents(10, visitor_id: self::MOCK_VISITOR_ID, linked_id: "linked_id", reverse: true);

        $this->assertCount(1, $events->getEvents());
        /** @noinspection SpellCheckingInspection */
        $this->assertEquals("Ibk1527CUFmcnjLwIs4A9", $events->getEvents()[0]->getProducts()->getIdentification()->getData()->getVisitorId());
    }

    /**
     * Verifies searchEvents sends all query parameters correctly.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testSearchEventsWithAllParams()
    {
        $start = new DateTime('2020-01-01 00:00:00');
        $end = new DateTime('2020-01-02 00:00:00');

        $this->mockHandler->append(function (RequestInterface $request) use ($end, $start) {
            $queryArray = [];
            parse_str($request->getUri()->getQuery(), $queryArray);

            $expected = [
                'limit' => '10',
                'pagination_key' => 'pagination',
                'reverse' => 'true',
                'linked_id' => 'linked_id',
                'start' => $start->getTimestamp(),
                'end' => $end->getTimestamp(),
                'suspect' => 'true',
                'bot' => 'good',
                'ip_address' => '127.0.0.1/16',
                'vpn' => 'true',
                'virtual_machine' => 'true',
                'tampering' => 'true',
                'anti_detect_browser' => 'true',
                'incognito' => 'true',
                'privacy_settings' => 'true',
                'jailbroken' => 'true',
                'frida' => 'true',
                'factory_reset' => 'true',
                'cloned_app' => 'true',
                'emulator' => 'true',
                'root_apps' => 'true',
                'vpn_confidence' => 'medium',
                'min_suspect_score' => '0.5',
                'ip_blocklist' => 'true',
                'datacenter' => 'true',
                'developer_tools' => 'true',
                'location_spoofing' => 'true',
                'mitm_attack' => 'true',
                'proxy' => 'true',
                'sdk_version' => 'testSdkVersion',
                'sdk_platform' => 'testSdkPlatform',
                'proximity_id' => 'testProximityId',
                'proximity_precision_radius' => 10,
            ];

            $extraKeys = ['ii', 'visitor_id'];

            // Plus 1 for environment query
            $this->assertCount(count($expected) + count($extraKeys) + 1, $queryArray);
            foreach ($expected as $query => $value) {
                $this->assertArrayHasKey($query, $queryArray, "Missing query parameter: $query");
                $this->assertEquals($value, $queryArray[$query]);
            }

            $envs = $queryArray['environment'];
            $this->assertEquals(['env1', 'env2'], $envs);

            return $this->returnMockResponse('get_event_search_200.json');
        });

        list($events) = $this->fingerprint_api->searchEvents(
            10,
            pagination_key: 'pagination',
            visitor_id: self::MOCK_VISITOR_ID,
            bot: 'good',
            ip_address: '127.0.0.1/16',
            linked_id: 'linked_id',
            start: $start->getTimestamp(),
            end: $end->getTimestamp(),
            reverse: true,
            suspect: true,
            vpn: true,
            virtual_machine: true,
            tampering: true,
            anti_detect_browser: true,
            incognito: true,
            privacy_settings: true,
            jailbroken: true,
            frida: true,
            factory_reset: true,
            cloned_app: true,
            emulator: true,
            root_apps: true,
            vpn_confidence: 'medium',
            min_suspect_score: 0.5,
            ip_blocklist: true,
            datacenter: true,
            developer_tools: true,
            location_spoofing: true,
            mitm_attack: true,
            proxy: true,
            sdk_version: 'testSdkVersion',
            sdk_platform: 'testSdkPlatform',
            environment: ['env1', 'env2'],
            proximity_id: "testProximityId",
            proximity_precision_radius: 10
        );

        $this->assertCount(1, $events->getEvents());
        /** @noinspection SpellCheckingInspection */
        $this->assertEquals('Ibk1527CUFmcnjLwIs4A9', $events->getEvents()[0]->getProducts()->getIdentification()->getData()->getVisitorId());
    }

    /**
     * Verifies searchEvents throws 400 for invalid bot type.
     *
     * @throws SerializationException
     * @throws GuzzleException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testSearchEvents400Error()
    {
        $this->mockHandler->append($this->returnMockResponse('errors/400_bot_type_invalid.json', 400));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(400);

        try {
            $this->fingerprint_api->searchEvents(10, pagination_key: 'pagination', visitor_id: self::MOCK_VISITOR_ID, bot: 'invalid');
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::REQUEST_CANNOT_BE_PARSED, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies searchEvents throws 403 for inactive subscription.
     *
     * @throws GuzzleException
     * @throws SerializationException
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testSearchEvents403Error()
    {
        $this->mockHandler->append($this->returnMockResponse('errors/403_subscription_not_active.json', 403));

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        try {
            $this->fingerprint_api->searchEvents(10);
        } catch (ApiException $e) {
            $this->assertEquals(ErrorResponse::class, get_class($e->getErrorDetails()));
            $this->assertEquals(ErrorCode::SUBSCRIPTION_NOT_ACTIVE, $e->getErrorDetails()->getError()->getCode());

            throw $e;
        }
    }

    /**
     * Verifies getEvent encodes a path-traversal request_id into a single
     * path segment rather than letting it escape to a sibling resource.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetEventEncodesPathTraversalRequestId(): void
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID));

        $this->fingerprint_api->getEvent('../events');

        $request = $this->mockHandler->getLastRequest();
        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/events/..%2Fevents', $request->getUri()->getPath());
    }

    /**
     * Verifies an absolute-URL-shaped request_id is treated as an opaque path
     * segment and never changes the request's target host.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetEventDoesNotRedirectHostForRequestId(): void
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID));

        $this->fingerprint_api->getEvent('https://domain.tld/evil');

        $request = $this->mockHandler->getLastRequest();
        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/events/https%3A%2F%2Fdomain.tld%2Fevil', $request->getUri()->getPath());
    }

    /**
     * Verifies an empty request_id still produces a distinct trailing segment
     * instead of collapsing onto the bare /events collection endpoint.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetEventWithEmptyRequestIdDoesNotCallCollectionEndpoint(): void
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_REQUEST_ID));

        $this->fingerprint_api->getEvent('');

        $request = $this->mockHandler->getLastRequest();
        $this->assertNotSame('/events', $request->getUri()->getPath());
        $this->assertSame('/events/', $request->getUri()->getPath());
    }

    /**
     * Verifies updateEvent encodes a path-traversal request_id the same way
     * as the read path.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testUpdateEventEncodesPathTraversalRequestId(): void
    {
        $this->mockHandler->append(new Response(200));

        $this->fingerprint_api->updateEvent(new EventsUpdateRequest(), '../events');

        $request = $this->mockHandler->getLastRequest();
        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/events/..%2Fevents', $request->getUri()->getPath());
    }

    /**
     * Verifies an absolute-URL-shaped request_id passed to updateEvent never
     * changes the request's target host.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testUpdateEventDoesNotRedirectHostForRequestId(): void
    {
        $this->mockHandler->append(new Response(200));

        $this->fingerprint_api->updateEvent(new EventsUpdateRequest(), 'https://domain.tld/evil');

        $request = $this->mockHandler->getLastRequest();
        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/events/https%3A%2F%2Fdomain.tld%2Fevil', $request->getUri()->getPath());
    }

    /**
     * Verifies deleteVisitorData encodes a path-traversal visitor_id into a
     * single path segment rather than letting it escape to a sibling
     * resource.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testDeleteVisitorDataEncodesPathTraversalVisitorId(): void
    {
        $this->mockHandler->append(new Response(200));

        $this->fingerprint_api->deleteVisitorData('../visitors');

        $request = $this->mockHandler->getLastRequest();
        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/visitors/..%2Fvisitors', $request->getUri()->getPath());
    }

    /**
     * Verifies an absolute-URL-shaped visitor_id is treated as an opaque path
     * segment and never changes the request's target host.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testDeleteVisitorDataDoesNotRedirectHostForVisitorId(): void
    {
        $this->mockHandler->append(new Response(200));

        $this->fingerprint_api->deleteVisitorData('https://domain.tld/evil');

        $request = $this->mockHandler->getLastRequest();
        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/visitors/https%3A%2F%2Fdomain.tld%2Fevil', $request->getUri()->getPath());
    }

    /**
     * Verifies an empty visitor_id still produces a distinct trailing segment
     * instead of collapsing onto the bare /visitors collection endpoint.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testDeleteVisitorDataWithEmptyVisitorIdDoesNotCallCollectionEndpoint(): void
    {
        $this->mockHandler->append(new Response(200));

        $this->fingerprint_api->deleteVisitorData('');

        $request = $this->mockHandler->getLastRequest();
        $this->assertNotSame('/visitors', $request->getUri()->getPath());
        $this->assertSame('/visitors/', $request->getUri()->getPath());
    }

    /**
     * Verifies getVisits encodes a path-traversal visitor_id into a single
     * path segment rather than letting it escape to a sibling resource.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetVisitsEncodesPathTraversalVisitorId(): void
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID));

        $this->fingerprint_api->getVisits('../visitors');

        $request = $this->mockHandler->getLastRequest();
        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/visitors/..%2Fvisitors', $request->getUri()->getPath());
    }

    /**
     * Verifies an absolute-URL-shaped visitor_id passed to getVisits never
     * changes the request's target host.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetVisitsDoesNotRedirectHostForVisitorId(): void
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID));

        $this->fingerprint_api->getVisits('https://domain.tld/evil');

        $request = $this->mockHandler->getLastRequest();
        $this->assertSame('api.fpjs.io', $request->getUri()->getHost());
        $this->assertSame('/visitors/https%3A%2F%2Fdomain.tld%2Fevil', $request->getUri()->getPath());
    }

    /**
     * Verifies an empty visitor_id passed to getVisits still produces a
     * distinct trailing segment instead of collapsing onto the bare
     * /visitors collection endpoint.
     *
     * @throws ApiException
     * @throws GuzzleException
     * @throws SerializationException
     */
    public function testGetVisitsWithEmptyVisitorIdDoesNotCallCollectionEndpoint(): void
    {
        $this->mockHandler->append($this->getMockResponse(self::MOCK_VISITOR_ID));

        $this->fingerprint_api->getVisits('');

        $request = $this->mockHandler->getLastRequest();
        $this->assertNotSame('/visitors', $request->getUri()->getPath());
        $this->assertSame('/visitors/', $request->getUri()->getPath());
    }

    protected function getVersion()
    {
        $config_file = file_get_contents(__DIR__ . '/../composer.json');
        $config = json_decode($config_file, true);

        return $config['version'];
    }

    /**
     * Mock response map: method => mockId => [file, status, extra_headers].
     */
    private const MOCK_RESPONSE_MAP = [
        'GET' => [
            self::MOCK_REQUEST_ID => ['get_event_200.json', 200],
            self::MOCK_REQUEST_ALL_ERRORS => ['get_event_200_all_errors.json', 200],
            self::MOCK_REQUEST_EXTRA_FIELDS => ['get_event_200_extra_fields.json', 200],
            self::MOCK_REQUEST_ID_WITH_UNKNOWN => ['get_event_200_with_unknown_field.json', 200],
            self::MOCK_REQUEST_ID_WITH_BROKEN => ['get_event_200_with_broken_format.json', 200],
            self::MOCK_EVENT_ID_403_TOKEN_REQUIRED => ['errors/403_token_required.json', 403],
            self::MOCK_EVENT_ID_403_TOKEN_NOT_FOUND => ['errors/403_token_not_found.json', 403],
            self::MOCK_EVENT_ID_403_WRONG_REGION => ['errors/403_wrong_region.json', 403],
            self::MOCK_EVENT_ID_404_ERROR => ['errors/404_request_not_found.json', 404],
            self::MOCK_EVENT_ID_400_ERROR => ['errors/400_request_body_invalid.json', 400],
            self::MOCK_VISITOR_ID => ['get_visitors_200_limit_500.json', 200],
            self::MOCK_VISITOR_REQUEST_ID => ['get_visitors_200_limit_1.json', 200],
            self::MOCK_VISITOR_ID_403_ERROR_FORBIDDEN => ['get_visitors_403_forbidden.json', 403],
            self::MOCK_VISITOR_ID_403_FEATURE_NOT_ENABLED => ['errors/403_feature_not_enabled.json', 403],
            self::MOCK_VISITOR_ID_403_TOKEN_NOT_FOUND => ['errors/403_token_not_found.json', 403],
            self::MOCK_VISITOR_ID_403_TOKEN_REQUIRED => ['errors/403_token_required.json', 403],
            self::MOCK_VISITOR_ID_403_WRONG_REGION => ['errors/403_wrong_region.json', 403],
            self::MOCK_VISITOR_ID_403_SUBSCRIPTION_NOT_ACTIVE => ['errors/403_subscription_not_active.json', 403],
            self::MOCK_VISITOR_ID_429_ERROR => ['get_visitors_429_too_many_requests.json', 429, ['retry-after' => 30]],
            self::MOCK_ERROR_429_TOO_MANY_REQUESTS => ['errors/429_too_many_requests.json', 429, ['retry-after' => 30]],
            self::MOCK_VISITOR_ID_400_INCORRECT_VISITOR_ID => ['errors/400_visitor_id_invalid.json', 400],
            self::MOCK_VISITOR_ID_400_EMPTY_VISITOR_ID => ['errors/400_visitor_id_required.json', 400],
            self::MOCK_VISITOR_ID_404_ERROR => ['errors/404_visitor_not_found.json', 404],
            self::MOCK_RELATED_VISITORS => ['related-visitors/get_related_visitors_200.json', 200],
        ],
        'PUT' => [
            self::MOCK_EVENT_ID => ['update_event.json', 200],
            self::MOCK_EVENT_ID_409_ERROR => ['errors/409_state_not_ready.json', 409],
            self::MOCK_EVENT_ID_404_ERROR => ['errors/404_request_not_found.json', 404],
            self::MOCK_EVENT_ID_403_TOKEN_REQUIRED => ['errors/403_token_required.json', 403],
            self::MOCK_EVENT_ID_403_TOKEN_NOT_FOUND => ['errors/403_token_not_found.json', 403],
            self::MOCK_EVENT_ID_403_WRONG_REGION => ['errors/403_wrong_region.json', 403],
            self::MOCK_EVENT_ID_400_ERROR => ['errors/400_request_body_invalid.json', 400],
        ],
    ];

    protected function getMockResponse(string $mockId, string $method = 'GET', ?string $operationId = null): Response
    {
        $headers = ['Content-Type' => 'application/json'];
        $methodKey = ('PUT' === $method) ? 'PUT' : 'GET';
        $map = self::MOCK_RESPONSE_MAP[$methodKey] ?? [];

        if (isset($map[$mockId])) {
            [$file, $status] = $map[$mockId];
            $headers = array_merge($headers, $map[$mockId][2] ?? []);
        } elseif ('PUT' === $methodKey) {
            $file = 'update_event.json';
            $status = 200;
        } else {
            throw new RuntimeException("Unknown mock ID: $mockId for method: $method");
        }

        $contents = file_get_contents(__DIR__ . "/mocks/$file");
        if ($operationId) {
            $visits_mock_data = json_decode($contents);
            $visits_mock_data->visits = array_filter($visits_mock_data->visits, function ($item) use ($operationId) {
                return isset($item->requestId) && $item->requestId === $operationId;
            });
            $contents = json_encode($visits_mock_data);
        }

        return new Response($status, $headers, $contents);
    }

    protected function returnMockResponse(string $path, $status = 200, array $headers = []): Response
    {
        $contents = file_get_contents(__DIR__ . "/mocks/$path");

        return new Response($status, $headers, $contents);
    }
}
