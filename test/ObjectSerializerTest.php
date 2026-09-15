<?php

namespace Fingerprint\ServerSdk\Test;

use Fingerprint\ServerSdk\Model\BotInfoConfidence;
use Fingerprint\ServerSdk\Model\Event;
use Fingerprint\ServerSdk\Model\EventUpdate;
use Fingerprint\ServerSdk\ObjectSerializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ObjectSerializer::class)]
class ObjectSerializerTest extends TestCase
{
    /**
     * @throws \Random\RandomException;
     */
    public function testUpdateEventTagSerialization(): void
    {
        $tag = [
            'automationTest_testName' => 'Automation Test Scenario 1',
            'automationTest_testId' => 'test_'.substr(bin2hex(random_bytes(4)), 0, 6),
            'automationTest_metadata' => [
                'id' => rand(1, 1000),
                'description' => 'This is a metadata description for automation testing.',
                'createdAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
                'updatedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
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
                    'timestamp' => (new \DateTime())->format(\DateTimeInterface::ATOM),
                    'level' => 'info',
                    'message' => 'Test started.',
                ],
                [
                    'timestamp' => (new \DateTime())->format(\DateTimeInterface::ATOM),
                    'level' => 'error',
                    'message' => 'Assertion failed.',
                ],
            ],
        ];

        $body = new EventUpdate();
        $body->setTags($tag);

        $sanitized_body = ObjectSerializer::sanitizeForSerialization($body);
        $this->assertSame($sanitized_body->tags, $tag);
    }

    /**
     * @throws \Exception
     */
    public function testDeserializeValidResponse(): void
    {
        $json = json_decode(file_get_contents(__DIR__.'/mocks/events/get_event_200.json'));

        /** @var Event $result */
        $result = ObjectSerializer::deserialize($json, Event::class);

        $this->assertInstanceOf(Event::class, $result);
        $this->assertNotNull($result->getEventId());
        $this->assertNotNull($result->getIdentification()->getVisitorId());
    }

    public function testSanitizeWithNullPropertiesSkipped(): void
    {
        $body = new EventUpdate();
        $sanitized = ObjectSerializer::sanitizeForSerialization($body);

        $this->assertIsObject($sanitized);
        $vars = get_object_vars($sanitized);
        $this->assertEmpty($vars);
    }

    /**
     * setDateTimeFormat should change the format used when serializing DateTime objects.
     */
    public function testSetDateTimeFormat(): void
    {
        $date = new \DateTime('2025-06-15 10:30:00');

        ObjectSerializer::setDateTimeFormat('Y-m-d');
        $serialized = ObjectSerializer::sanitizeForSerialization($date);
        $this->assertEquals('2025-06-15', $serialized);

        ObjectSerializer::setDateTimeFormat(\DateTimeInterface::ATOM);
        $serialized = ObjectSerializer::sanitizeForSerialization($date);
        $this->assertEquals($date->format(\DateTimeInterface::ATOM), $serialized);
    }

    /**
     * sanitizeFilename should strip directory paths and return only the filename.
     */
    public function testSanitizeFilename(): void
    {
        $this->assertEquals('file.txt', ObjectSerializer::sanitizeFilename('/path/to/file.txt'));
        $this->assertEquals('file.txt', ObjectSerializer::sanitizeFilename('C:\Users\test\file.txt'));
        $this->assertEquals('file.txt', ObjectSerializer::sanitizeFilename('file.txt'));
        $this->assertEquals('file.txt', ObjectSerializer::sanitizeFilename('/file.txt'));
        $this->assertEquals('', ObjectSerializer::sanitizeFilename('/path/to/'));
    }

    /**
     * buildQuery should return an empty string for an empty params array.
     */
    public function testBuildQueryEmpty(): void
    {
        $this->assertSame('', ObjectSerializer::buildQuery([]));
    }

    /**
     * buildQuery should encode simple key-value pairs.
     */
    public function testBuildQuerySimpleParams(): void
    {
        $params = ['key1' => 'value1', 'key2' => 'value2'];

        $this->assertSame('key1=value1&key2=value2', ObjectSerializer::buildQuery($params));
    }

    /**
     * buildQuery should repeat the key for array values.
     */
    public function testBuildQueryArrayValues(): void
    {
        $params = ['tag' => ['a', 'b', 'c']];

        $this->assertSame('tag=a&tag=b&tag=c', ObjectSerializer::buildQuery($params));
    }

    /**
     * buildQuery should encode special characters with RFC 3986 by default.
     */
    public function testBuildQueryEncodesSpecialCharacters(): void
    {
        $params = ['q' => 'a b+c', 'url' => 'https://example.com?a=1'];

        $this->assertSame(
            'q=a%20b%2Bc&url=https%3A%2F%2Fexample.com%3Fa%3D1',
            ObjectSerializer::buildQuery($params)
        );
    }

    /**
     * buildQuery should cast boolean values to 'true' and 'false' strings.
     */
    public function testBuildQueryBooleanValues(): void
    {
        $params = ['active' => true, 'archived' => false];

        $this->assertSame('active=true&archived=false', ObjectSerializer::buildQuery($params));
    }

    /**
     * buildQuery should include the key without a value when the value is null.
     */
    public function testBuildQueryNullValue(): void
    {
        $params = ['key' => null];

        $this->assertSame('key', ObjectSerializer::buildQuery($params));
    }

    /**
     * buildQuery should skip encoding when encoding is set to false.
     */
    public function testBuildQueryNoEncoding(): void
    {
        $params = ['q' => 'a b+c'];

        $this->assertSame('q=a b+c', ObjectSerializer::buildQuery($params, false));
    }

    /**
     * buildQuery should extract the value from a BackedEnum scalar parameter.
     */
    public function testBuildQueryBackedEnumScalar(): void
    {
        $params = ['confidence' => BotInfoConfidence::HIGH];

        $this->assertSame('confidence=high', ObjectSerializer::buildQuery($params));
    }

    /**
     * buildQuery should extract the value from BackedEnum items inside an array parameter.
     */
    public function testBuildQueryBackedEnumArray(): void
    {
        $params = ['confidence' => [BotInfoConfidence::HIGH, BotInfoConfidence::MEDIUM]];

        $this->assertSame('confidence=high&confidence=medium', ObjectSerializer::buildQuery($params));
    }

    /**
     * buildQuery should throw InvalidArgumentException for an invalid encoding type.
     */
    public function testBuildQueryInvalidEncoding(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ObjectSerializer::buildQuery(['key' => 'value'], 'invalid');
    }

    /**
     * toQueryValue with int|\DateTime type should format a DateTime using the current dateTimeFormat.
     */
    public function testToQueryValueDateTimeUnionWithDateTime(): void
    {
        $date = new \DateTime('2024-03-15T12:00:00+00:00');
        $result = ObjectSerializer::toQueryValue($date, 'before', 'int|\DateTime');
        $this->assertSame(['before' => $date->format(\DateTimeInterface::ATOM)], $result);
    }

    /**
     * toQueryValue with \DateTime|int (reversed) should also format a DateTime correctly.
     */
    public function testToQueryValueDateTimeUnionReversedWithDateTime(): void
    {
        $date = new \DateTime('2024-03-15T12:00:00+00:00');
        $result = ObjectSerializer::toQueryValue($date, 'after', '\DateTime|int');
        $this->assertSame(['after' => $date->format(\DateTimeInterface::ATOM)], $result);
    }

    /**
     * toQueryValue with int|\DateTime type should pass an integer through as-is.
     */
    public function testToQueryValueDateTimeUnionWithInt(): void
    {
        $result = ObjectSerializer::toQueryValue(1700000000, 'before', 'int|\DateTime');
        $this->assertSame(['before' => 1700000000], $result);
    }

    /**
     * toQueryValue with int|\DateTime type should omit a null value when the parameter is not required.
     */
    public function testToQueryValueDateTimeUnionNullOptional(): void
    {
        $result = ObjectSerializer::toQueryValue(null, 'before', 'int|\DateTime', 'form', true, false);
        $this->assertSame([], $result);
    }

    /**
     * toQueryValue with int|\DateTime type should return an empty string for a null value when the parameter is required.
     */
    public function testToQueryValueDateTimeUnionNullRequired(): void
    {
        $result = ObjectSerializer::toQueryValue(null, 'before', 'int|\DateTime', 'form', true, true);
        $this->assertSame(['before' => ''], $result);
    }

    /**
     * toQueryValue with int|\DateTime type should pass zero through because it should be treated as an integer.
     */
    public function testToQueryValueDateTimeUnionZeroOptional(): void
    {
        $result = ObjectSerializer::toQueryValue(0, 'before', 'int|\DateTime', 'form', true, false);
        $this->assertSame(['before' => 0], $result);
    }

    /**
     * toQueryValue with int|\DateTime type should respect a custom dateTimeFormat when formatting a DateTime.
     */
    public function testToQueryValueDateTimeUnionCustomFormat(): void
    {
        $date = new \DateTime('2024-03-15T12:00:00+00:00');
        ObjectSerializer::setDateTimeFormat('Y-m-d');
        $result = ObjectSerializer::toQueryValue($date, 'before', 'int|\DateTime');
        ObjectSerializer::setDateTimeFormat(\DateTimeInterface::ATOM);
        $this->assertSame(['before' => '2024-03-15'], $result);
    }

    // -- sanitizeTimestamp --

    public function testSanitizeTimestamp(): void
    {
        $result = ObjectSerializer::sanitizeTimestamp('2024-01-01T00:00:00.123456789');
        $this->assertSame('2024-01-01T00:00:00.123456', $result);
    }

    // -- toPathValue --

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
    public function testToPathValueEncodesAbsoluteUrlValue(): void
    {
        $this->assertSame('https%3A%2F%2Fdomain.tld%2Fevil', ObjectSerializer::toPathValue('https://domain.tld/evil'));
    }

    public function testToPathValueWithEmptyString(): void
    {
        $this->assertSame('', ObjectSerializer::toPathValue(''));
    }

    /**
     * A path parameter of exactly '.' or '..' is an RFC 3986 dot-segment:
     * left as a literal dot, URL normalizers (including curl, before the
     * request ever reaches the wire — see RawRequestCapture-based tests in
     * FingerprintApiTest) collapse it into the parent/current path instead
     * of treating it as an opaque resource identifier. The dots must be
     * percent-encoded so no normalizer can mistake the segment for one.
     */
    public function testToPathValueEncodesDotSegment(): void
    {
        $this->assertSame('%2E', ObjectSerializer::toPathValue('.'));
    }

    public function testToPathValueEncodesDotDotSegment(): void
    {
        $this->assertSame('%2E%2E', ObjectSerializer::toPathValue('..'));
    }

    /**
     * Only a segment consisting solely of dots is special under RFC 3986;
     * anything else containing a dot (e.g. a real event ID) must pass
     * through unencoded, since '.' is otherwise a safe, unreserved character.
     */
    public function testToPathValueDoesNotEncodeDotsInOtherwiseNormalValues(): void
    {
        $this->assertSame('1708102555327.NLOjmg', ObjectSerializer::toPathValue('1708102555327.NLOjmg'));
        $this->assertSame('...', ObjectSerializer::toPathValue('...'));
    }

    // -- toHeaderValue --

    public function testToHeaderValueWithString(): void
    {
        $this->assertSame('test', ObjectSerializer::toHeaderValue('test'));
    }

    public function testToHeaderValueWithDateTime(): void
    {
        $dt = new \DateTime('2024-01-15T12:00:00+00:00');
        $result = ObjectSerializer::toHeaderValue($dt);
        $this->assertStringContainsString('2024-01-15', $result);
    }

    public function testToHeaderValueWithModel(): void
    {
        $event = new Event(['event_id' => 'e1', 'timestamp' => 1]);
        $result = ObjectSerializer::toHeaderValue($event);
        $this->assertIsString($result);
        $this->assertStringContainsString('e1', $result);
    }

    // -- toString --

    public function testToStringWithBool(): void
    {
        $this->assertSame('true', ObjectSerializer::toString(true));
        $this->assertSame('false', ObjectSerializer::toString(false));
    }

    public function testToStringWithDateTime(): void
    {
        $dt = new \DateTime('2024-01-15T12:00:00+00:00');
        $result = ObjectSerializer::toString($dt);
        $this->assertStringContainsString('2024-01-15', $result);
    }

    public function testToStringWithNumber(): void
    {
        $this->assertSame('42', ObjectSerializer::toString(42));
        $this->assertSame('3.14', ObjectSerializer::toString(3.14));
    }

    // -- serializeCollection --

    public function testSerializeCollectionPipes(): void
    {
        $this->assertSame('a|b|c', ObjectSerializer::serializeCollection(['a', 'b', 'c'], 'pipes'));
        $this->assertSame('a|b', ObjectSerializer::serializeCollection(['a', 'b'], 'pipeDelimited'));
    }

    public function testSerializeCollectionTsv(): void
    {
        $this->assertSame("a\tb\tc", ObjectSerializer::serializeCollection(['a', 'b', 'c'], 'tsv'));
    }

    public function testSerializeCollectionSsv(): void
    {
        $this->assertSame('a b c', ObjectSerializer::serializeCollection(['a', 'b', 'c'], 'ssv'));
        $this->assertSame('a b', ObjectSerializer::serializeCollection(['a', 'b'], 'spaceDelimited'));
    }

    public function testSerializeCollectionCsv(): void
    {
        $this->assertSame('a,b,c', ObjectSerializer::serializeCollection(['a', 'b', 'c'], 'csv'));
    }

    public function testSerializeCollectionMulti(): void
    {
        $result = ObjectSerializer::serializeCollection(['a', 'b'], 'multi', true);
        $this->assertStringContainsString('a', $result);
        $this->assertStringContainsString('b', $result);
    }

    // -- deserialize edge cases --

    public function testDeserializeNull(): void
    {
        $this->assertNull(ObjectSerializer::deserialize(null, Event::class));
    }

    public function testDeserializeArray(): void
    {
        $data = [
            ['event_id' => 'e1', 'timestamp' => 1],
            ['event_id' => 'e2', 'timestamp' => 2],
        ];
        $result = ObjectSerializer::deserialize($data, Event::class.'[]');
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Event::class, $result[0]);
        $this->assertSame('e1', $result[0]->getEventId());
    }

    public function testDeserializeArrayWithInvalidData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ObjectSerializer::deserialize('not-an-array', Event::class.'[]');
    }

    public function testDeserializeMap(): void
    {
        $data = (object) ['key1' => 'value1', 'key2' => 'value2'];
        $result = ObjectSerializer::deserialize($data, 'array<string,string>');
        $this->assertSame(['key1' => 'value1', 'key2' => 'value2'], $result);
    }

    public function testDeserializeObject(): void
    {
        $data = (object) ['foo' => 'bar'];
        $result = ObjectSerializer::deserialize($data, 'object');
        $this->assertIsArray($result);
        $this->assertSame('bar', $result['foo']);
    }

    public function testDeserializeMixed(): void
    {
        $this->assertSame('hello', ObjectSerializer::deserialize('hello', 'mixed'));
        $this->assertSame(42, ObjectSerializer::deserialize(42, 'mixed'));
    }

    public function testDeserializeDateTimeEmpty(): void
    {
        $this->assertNull(ObjectSerializer::deserialize('', '\DateTime'));
    }

    public function testDeserializeDateTimeValid(): void
    {
        $result = ObjectSerializer::deserialize('2024-01-15T12:00:00Z', '\DateTime');
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function testDeserializeDateTimeHighPrecision(): void
    {
        $result = ObjectSerializer::deserialize('2024-01-15T12:00:00.123456789012Z', '\DateTime');
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function testDeserializePrimitiveInt(): void
    {
        $result = ObjectSerializer::deserialize('42', 'int');
        $this->assertSame(42, $result);
    }

    public function testDeserializePrimitiveBool(): void
    {
        $result = ObjectSerializer::deserialize(true, 'bool');
        $this->assertTrue($result);
    }

    public function testDeserializePrimitiveString(): void
    {
        $result = ObjectSerializer::deserialize('hello', 'string');
        $this->assertSame('hello', $result);
    }

    public function testDeserializePrimitiveFloat(): void
    {
        $result = ObjectSerializer::deserialize('3.14', 'float');
        $this->assertSame(3.14, $result);
    }

    public function testDeserializeFromJsonString(): void
    {
        $json = json_encode(['event_id' => 'e1', 'timestamp' => 1]);
        $result = ObjectSerializer::deserialize($json, Event::class);
        $this->assertInstanceOf(Event::class, $result);
        $this->assertSame('e1', $result->getEventId());
    }

    public function testDeserializeArrayFromJsonString(): void
    {
        $json = json_encode([['event_id' => 'e1', 'timestamp' => 1]]);
        $result = ObjectSerializer::deserialize($json, Event::class.'[]');
        $this->assertCount(1, $result);
    }
}
