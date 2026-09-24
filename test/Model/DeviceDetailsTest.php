<?php

namespace Fingerprint\ServerSdk\Test\Model;

use Fingerprint\ServerSdk\Model\DeviceDetails;
use Fingerprint\ServerSdk\ObjectSerializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DeviceDetails::class)]
class DeviceDetailsTest extends TestCase
{
    private const EXAMPLE = [
        'device_manufacturer' => 'samsung',
        'device_model' => 'SM-G991U',
        'os_version' => '13',
    ];

    /**
     * Constructor without arguments should initialize all properties to null.
     */
    public function testConstructorDefaults(): void
    {
        $model = new DeviceDetails();

        $this->assertNull($model->getDeviceManufacturer());
        $this->assertNull($model->getDeviceModel());
        $this->assertNull($model->getOsVersion());
    }

    /**
     * Constructor should accept an array and populate the properties.
     */
    public function testConstructorWithData(): void
    {
        $model = new DeviceDetails(self::EXAMPLE);

        $this->assertEquals(self::EXAMPLE['device_manufacturer'], $model->getDeviceManufacturer());
        $this->assertEquals(self::EXAMPLE['device_model'], $model->getDeviceModel());
        $this->assertEquals(self::EXAMPLE['os_version'], $model->getOsVersion());
    }

    /**
     * Setters should return the model instance for chaining.
     */
    public function testSettersReturnSelf(): void
    {
        $model = new DeviceDetails();

        $this->assertSame($model, $model->setDeviceManufacturer(self::EXAMPLE['device_manufacturer']));
        $this->assertSame($model, $model->setDeviceModel(self::EXAMPLE['device_model']));
        $this->assertSame($model, $model->setOsVersion(self::EXAMPLE['os_version']));
    }

    /**
     * Serialization should preserve all property values through a round-trip.
     *
     * @throws \DateMalformedStringException
     */
    public function testSerialization(): void
    {
        $model = new DeviceDetails(self::EXAMPLE);

        $json = json_encode(ObjectSerializer::sanitizeForSerialization($model));
        $deserialized = ObjectSerializer::deserialize(json_decode($json), DeviceDetails::class);

        $this->assertEquals($model->getDeviceManufacturer(), $deserialized->getDeviceManufacturer());
        $this->assertEquals($model->getDeviceModel(), $deserialized->getDeviceModel());
        $this->assertEquals($model->getOsVersion(), $deserialized->getOsVersion());
    }

    /**
     * ArrayAccess interface should allow bracket notation for getting and setting properties.
     *
     * @noinspection PhpConditionAlreadyCheckedInspection
     */
    public function testArrayAccess(): void
    {
        $model = new DeviceDetails();

        $model['device_model'] = self::EXAMPLE['device_model'];
        $this->assertEquals(self::EXAMPLE['device_model'], $model['device_model']);
        $this->assertTrue(isset($model['device_model']));

        unset($model['device_model']);
        $this->assertNull($model['device_model']);
    }

    /**
     * DeviceDetails has no required properties so an empty model should always be valid.
     */
    public function testValidation(): void
    {
        $emptyModel = new DeviceDetails();
        $this->assertTrue($emptyModel->valid());
        $this->assertEmpty($emptyModel->listInvalidProperties());

        $validModel = new DeviceDetails(self::EXAMPLE);
        $this->assertTrue($validModel->valid());
        $this->assertEmpty($validModel->listInvalidProperties());
    }

    /**
     * __toString should return a pretty-printed JSON representation.
     */
    public function testToString(): void
    {
        $model = new DeviceDetails(self::EXAMPLE);
        $string = (string) $model;

        $decoded = json_decode($string, true);
        $this->assertEquals(self::EXAMPLE['device_manufacturer'], $decoded['device_manufacturer']);
        $this->assertEquals(self::EXAMPLE['device_model'], $decoded['device_model']);
    }

    /**
     * getModelName should return the OpenAPI model name.
     */
    public function testGetModelName(): void
    {
        $model = new DeviceDetails();

        $this->assertEquals('DeviceDetails', $model->getModelName());
    }

    /**
     * offsetSet with null offset should append the value to the container.
     */
    public function testOffsetSetWithNullKey(): void
    {
        $model = new DeviceDetails();

        $model[] = 'appended_value';
        $this->assertEquals('appended_value', $model[0]);
    }

    /**
     * jsonSerialize should return the sanitized representation used by json_encode.
     */
    public function testJsonSerialize(): void
    {
        $model = new DeviceDetails(self::EXAMPLE);
        $serialized = $model->jsonSerialize();

        $this->assertIsObject($serialized);
        $this->assertEquals(self::EXAMPLE['device_manufacturer'], $serialized->device_manufacturer);
        $this->assertEquals(self::EXAMPLE['os_version'], $serialized->os_version);
    }

    /**
     * toHeaderValue should return a compact JSON string without newlines.
     */
    public function testToHeaderValue(): void
    {
        $model = new DeviceDetails(self::EXAMPLE);
        $header = $model->toHeaderValue();

        $decoded = json_decode($header, true);
        $this->assertIsArray($decoded);
        $this->assertEquals(self::EXAMPLE['device_model'], $decoded['device_model']);
        $this->assertStringNotContainsString("\n", $header);
    }

    /**
     * os_version is a free-form string: component count varies by platform and OS era
     * and must survive a round-trip verbatim, without normalization or zero-padding.
     */
    public function testOsVersionKeepsVariableComponentCount(): void
    {
        foreach (['17.4.1', '13', '16.1', '8.1.0'] as $version) {
            $model = new DeviceDetails(['os_version' => $version]);

            $json = json_encode(ObjectSerializer::sanitizeForSerialization($model));
            $deserialized = ObjectSerializer::deserialize(json_decode($json), DeviceDetails::class);

            $this->assertSame($version, $deserialized->getOsVersion());
        }
    }

    public function testIsNullableSetToNullPath(): void
    {
        $model = new DeviceDetails();
        $this->assertIsBool($model->isNullableSetToNull('device_model'));
    }
}
