<?php

use Fingerprint\ServerAPI\Model\EventsGetResponse;
use Fingerprint\ServerAPI\Model\EventsUpdateRequest;
use Fingerprint\ServerAPI\ObjectSerializer;
use Fingerprint\ServerAPI\SerializationException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ObjectSerializerTest extends TestCase
{
    /**
     * @throws \Random\RandomException
     */
    public function testUpdateEventTagSerialization(): void
    {
        $tag = [
            'automationTest_testName' => 'Automation Test Scenario 1',
            'automationTest_testId' => 'test_'.substr(bin2hex(random_bytes(4)), 0, 6),
            'automationTest_metadata' => [
                'id' => rand(1, 1000),
                'description' => 'This is a metadata description for automation testing.',
                'createdAt' => (new DateTime())->format(DateTimeInterface::ATOM),
                'updatedAt' => (new DateTime())->format(DateTimeInterface::ATOM),
            ],
            'automationTest_settings' => [
                'retries' => 3,
                'timeout' => 5000,
                'environment' => 'staging',
                'notifications' => ['email', 'slack', 'sms'],
            ],
            'automationTest_users' => [
                ['userId' => '123', 'roles' => ['admin', 'editor'], 'isActive' => true],
                ['userId' => '456', 'roles' => ['viewer'], 'isActive' => false],
            ],
            'automationTest_metrics' => [
                ['name' => 'executionTime', 'value' => 120.5, 'unit' => 'seconds'],
                ['name' => 'memoryUsage', 'value' => 256, 'unit' => 'MB'],
                ['name' => 'assertionsPassed', 'value' => 100],
            ],
            'automationTest_logs' => [
                [
                    'timestamp' => (new DateTime())->format(DateTimeInterface::ATOM),
                    'level' => 'info',
                    'message' => 'Test started.',
                ],
                [
                    'timestamp' => (new DateTime())->format(DateTimeInterface::ATOM),
                    'level' => 'error',
                    'message' => 'Assertion failed.',
                ],
            ],
        ];

        $body = new EventsUpdateRequest();
        $body->setTag($tag);

        $sanitized_body = ObjectSerializer::sanitizeForSerialization($body);
        $this->assertSame($sanitized_body->tag, $tag);
    }

    /**
     * @throws \Exception
     */
    public function testDeserializeValidResponse(): void
    {
        $json = file_get_contents(__DIR__.'/mocks/get_event_200.json');
        $response = new Response(200, ['Content-Type' => 'application/json'], $json);

        $result = ObjectSerializer::deserialize($response, EventsGetResponse::class);

        $this->assertInstanceOf(EventsGetResponse::class, $result);
        $this->assertNotNull($result->getProducts());
        $this->assertNotNull($result->getProducts()->getIdentification());
    }

    /**
     * @throws \Exception
     */
    public function testDeserializeBrokenFormatThrowsSerializationException(): void
    {
        $json = file_get_contents(__DIR__.'/mocks/get_event_200_with_broken_format.json');
        $response = new Response(200, ['Content-Type' => 'application/json'], $json);

        $this->expectException(SerializationException::class);

        ObjectSerializer::deserialize($response, EventsGetResponse::class);
    }

    public function testToQueryValueCsv(): void
    {
        $result = ObjectSerializer::toQueryValue(['a', 'b', 'c'], 'csv');
        $this->assertEquals('a,b,c', $result);
    }

    public function testToQueryValueMulti(): void
    {
        $input = ['a', 'b', 'c'];
        $result = ObjectSerializer::toQueryValue($input, 'multi');
        $this->assertEquals($input, $result);
    }

    public function testToQueryValuePipes(): void
    {
        $result = ObjectSerializer::toQueryValue(['a', 'b', 'c'], 'pipes');
        $this->assertEquals('a|b|c', $result);
    }

    public function testToQueryValueBoolTrue(): void
    {
        $result = ObjectSerializer::toQueryValue(true);
        $this->assertEquals('true', $result);
    }

    public function testToQueryValueBoolFalse(): void
    {
        $result = ObjectSerializer::toQueryValue(false);
        $this->assertEquals('false', $result);
    }

    public function testSanitizeWithDateTime(): void
    {
        $date = new DateTime('2024-01-15T10:30:00+00:00');
        $result = ObjectSerializer::sanitizeForSerialization($date);
        $this->assertEquals($date->format(DateTimeInterface::ATOM), $result);
    }

    public function testSanitizeWithDateFormat(): void
    {
        $date = new DateTime('2024-01-15T10:30:00+00:00');
        $result = ObjectSerializer::sanitizeForSerialization($date, 'date');
        $this->assertEquals('2024-01-15', $result);
    }

    public function testSanitizeWithScalar(): void
    {
        $this->assertEquals('hello', ObjectSerializer::sanitizeForSerialization('hello'));
        $this->assertEquals(42, ObjectSerializer::sanitizeForSerialization(42));
        $this->assertTrue(ObjectSerializer::sanitizeForSerialization(true));
    }

    public function testSanitizeWithNullPropertiesSkipped(): void
    {
        $body = new EventsUpdateRequest();
        $sanitized = ObjectSerializer::sanitizeForSerialization($body);

        $this->assertIsObject($sanitized);
        $vars = get_object_vars($sanitized);
        $this->assertEmpty($vars);
    }

    public function testToPathValue(): void
    {
        $this->assertSame('hello%20world', ObjectSerializer::toPathValue('hello world'));
        $this->assertSame('simple', ObjectSerializer::toPathValue('simple'));
    }

    /**
     * Verifies path traversal sequences are encoded so a single path segment
     * cannot escape into a sibling resource (e.g. `../events`).
     */
    public function testToPathValueEncodesPathTraversal(): void
    {
        $this->assertSame('..%2Fevents', ObjectSerializer::toPathValue('../events'));
        $this->assertSame('..%2F..%2Fevents', ObjectSerializer::toPathValue('../../events'));
    }

    /**
     * Verifies slashes are always encoded, since an un-encoded slash would let
     * a path parameter inject extra path segments.
     */
    public function testToPathValueEncodesSlash(): void
    {
        $this->assertSame('abc%2Fdef', ObjectSerializer::toPathValue('abc/def'));
    }

    /**
     * A value that looks like an absolute URL must not be able to redirect
     * the request elsewhere; its scheme and slashes are encoded so it stays
     * a single, inert path segment.
     */
    public function testToPathValueDoesNotDecodeHostLookingValue(): void
    {
        $this->assertSame('https%3A%2F%2Fdomain.tld%2Fevil', ObjectSerializer::toPathValue('https://domain.tld/evil'));
    }

    public function testToPathValueWithEmptyString(): void
    {
        $this->assertSame('', ObjectSerializer::toPathValue(''));
    }
}
