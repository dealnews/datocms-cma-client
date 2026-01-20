<?php

namespace DealNews\DatoCMS\CMA\Tests\DataTypes;

use DealNews\DatoCMS\CMA\DataTypes\Location;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('validLocationProvider')]
    public function testValidLocationValues(array $value, array $expected) {
        $location = Location::init();
        $location->set($value);

        $this->assertEquals($expected, $location->jsonSerialize());
    }

    #[Group('unit')]
    #[DataProvider('invalidLocationProvider')]
    public function testInvalidLocationValues(mixed $value, string $expectedMessage) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $location = Location::init();
        $location->set($value);
    }

    #[Group('unit')]
    public function testSetLocationHelperMethod() {
        $location = Location::init();
        $result   = $location->setLocation(40.7128, -74.0060);

        $this->assertInstanceOf(Location::class, $result);
        $this->assertEquals([
            'latitude'  => 40.7128,
            'longitude' => -74.0060,
        ], $location->jsonSerialize());
    }

    #[Group('unit')]
    public function testSetLocationMethodChaining() {
        $location = Location::init();
        $result   = $location->setLocation(0.0, 0.0);

        $this->assertInstanceOf(Location::class, $result);
        $this->assertSame($location, $result);
    }

    #[Group('unit')]
    public function testSetMethodReturnsStatic() {
        $location = Location::init();
        $result   = $location->set(['latitude' => 40.7128, 'longitude' => -74.0060]);

        $this->assertInstanceOf(Location::class, $result);
        $this->assertSame($location, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithValidLocation() {
        $location = Location::init();
        $location->set(['latitude' => 40.7128, 'longitude' => -74.0060]);
        $location->addLocale('en', ['latitude' => 51.5074, 'longitude' => -0.1278]);
        $location->addLocale('es', ['latitude' => 40.4168, 'longitude' => -3.7038]);

        $result = $location->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEquals(['latitude' => 51.5074, 'longitude' => -0.1278], $result['en']);
        $this->assertEquals(['latitude' => 40.4168, 'longitude' => -3.7038], $result['es']);
    }

    #[Group('unit')]
    public function testAddLocaleReturnsStatic() {
        $location = Location::init();
        $result   = $location->addLocale('en', ['latitude' => 0.0, 'longitude' => 0.0]);

        $this->assertInstanceOf(Location::class, $result);
        $this->assertSame($location, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithInvalidLocation() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in expected format');

        $location = Location::init();
        $location->addLocale('en', 'invalid');
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsNullWhenEmpty() {
        $location = Location::init();

        $this->assertNull($location->jsonSerialize());
    }

    #[Group('unit')]
    public function testJsonSerializePrioritizesLocalizedValues() {
        $location = Location::init();
        $location->set(['latitude' => 40.7128, 'longitude' => -74.0060]);
        $location->addLocale('en', ['latitude' => 51.5074, 'longitude' => -0.1278]);

        $result = $location->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('en', $result);
        $this->assertEquals(['latitude' => 51.5074, 'longitude' => -0.1278], $result['en']);
    }

    #[Group('unit')]
    public function testNullValueIsValid() {
        $location = Location::init();
        $location->set(null);

        $this->assertNull($location->jsonSerialize());
    }

    public static function validLocationProvider(): array {
        return [
            'NYC coordinates' => [
                ['latitude' => 40.7128, 'longitude' => -74.0060],
                ['latitude' => 40.7128, 'longitude' => -74.0060],
            ],
            'London coordinates' => [
                ['latitude' => 51.5074, 'longitude' => -0.1278],
                ['latitude' => 51.5074, 'longitude' => -0.1278],
            ],
            'Equator Prime Meridian' => [
                ['latitude' => 0.0, 'longitude' => 0.0],
                ['latitude' => 0.0, 'longitude' => 0.0],
            ],
            'latitude boundary min' => [
                ['latitude' => -90.0, 'longitude' => 0.0],
                ['latitude' => -90.0, 'longitude' => 0.0],
            ],
            'latitude boundary max' => [
                ['latitude' => 90.0, 'longitude' => 0.0],
                ['latitude' => 90.0, 'longitude' => 0.0],
            ],
            'longitude boundary min' => [
                ['latitude' => 0.0, 'longitude' => -180.0],
                ['latitude' => 0.0, 'longitude' => -180.0],
            ],
            'longitude boundary max' => [
                ['latitude' => 0.0, 'longitude' => 180.0],
                ['latitude' => 0.0, 'longitude' => 180.0],
            ],
            'negative latitude' => [
                ['latitude' => -33.8688, 'longitude' => 151.2093],
                ['latitude' => -33.8688, 'longitude' => 151.2093],
            ],
            'positive values' => [
                ['latitude' => 35.6762, 'longitude' => 139.6503],
                ['latitude' => 35.6762, 'longitude' => 139.6503],
            ],
        ];
    }

    public static function invalidLocationProvider(): array {
        return [
            'missing latitude' => [
                ['longitude' => 0.0],
                'Value not in expected format',
            ],
            'missing longitude' => [
                ['latitude' => 0.0],
                'Value not in expected format',
            ],
            'latitude below -90' => [
                ['latitude' => -90.1, 'longitude' => 0.0],
                'Latitude not in the expected format',
            ],
            'latitude above 90' => [
                ['latitude' => 90.1, 'longitude' => 0.0],
                'Latitude not in the expected format',
            ],
            'longitude below -180' => [
                ['latitude' => 0.0, 'longitude' => -180.1],
                'Longitude not in the expected format',
            ],
            'longitude above 180' => [
                ['latitude' => 0.0, 'longitude' => 180.1],
                'Longitude not in the expected format',
            ],
            'latitude as string' => [
                ['latitude' => 'invalid', 'longitude' => 0.0],
                'Latitude not in the expected format',
            ],
            'longitude as string' => [
                ['latitude' => 0.0, 'longitude' => 'invalid'],
                'Longitude not in the expected format',
            ],
            'latitude as array' => [
                ['latitude' => [40.7128], 'longitude' => 0.0],
                'Latitude not in the expected format',
            ],
            'longitude as object' => [
                ['latitude' => 0.0, 'longitude' => new \stdClass()],
                'Longitude not in the expected format',
            ],
            'non-array input' => [
                'not an array',
                'Value not in expected format',
            ],
            'empty array' => [
                [],
                'Value not in expected format',
            ],
        ];
    }
}
