<?php

namespace DealNews\DatoCMS\CMA\Tests\DataTypes;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\DataTypes\Scalar;

/**
 * Tests for the Common abstract base class (tested via Scalar subclass)
 *
 * Focuses on edge cases and exception paths not covered by concrete type tests.
 */
class CommonTest extends TestCase {

    #[Group('unit')]
    public function testInitReturnsNewInstance() {
        $scalar1 = Scalar::init();
        $scalar2 = Scalar::init();

        $this->assertNotSame($scalar1, $scalar2);
    }

    #[Group('unit')]
    public function testInitReturnsCorrectType() {
        $scalar = Scalar::init();

        $this->assertInstanceOf(Scalar::class, $scalar);
    }

    #[Group('unit')]
    public function testSetReturnsSelfForChaining() {
        $scalar = Scalar::init();
        $result = $scalar->set('value');

        $this->assertSame($scalar, $result);
    }

    #[Group('unit')]
    public function testAddLocaleReturnsSelfForChaining() {
        $scalar = Scalar::init();
        $result = $scalar->addLocale('en', 'value');

        $this->assertSame($scalar, $result);
    }

    #[Group('unit')]
    public function testChainedLocaleCalls() {
        $scalar = Scalar::init()
            ->addLocale('en', 'English')
            ->addLocale('es', 'Spanish')
            ->addLocale('fr', 'French');

        $result = $scalar->jsonSerialize();

        $this->assertEquals([
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
        ], $result);
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsNullWhenEmpty() {
        $scalar = Scalar::init();

        $this->assertNull($scalar->jsonSerialize());
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsValueWhenSet() {
        $scalar = Scalar::init()->set('test value');

        $this->assertEquals('test value', $scalar->jsonSerialize());
    }

    #[Group('unit')]
    public function testJsonSerializePrioritizesLocalizedOverValue() {
        $scalar = Scalar::init()
            ->set('default')
            ->addLocale('en', 'localized');

        $result = $scalar->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEquals('localized', $result['en']);
        $this->assertArrayNotHasKey('default', $result);
    }

    #[Group('unit')]
    public function testJsonSerializeWithJsonSerializableObject() {
        // Create an object that implements JsonSerializable
        $json_obj = new class implements \JsonSerializable {
            public function jsonSerialize(): mixed {
                return ['serialized' => 'data'];
            }
        };

        $scalar = Scalar::init();
        
        // Use reflection to bypass validation and set value directly
        $reflection = new \ReflectionClass($scalar);
        $property = $reflection->getProperty('value');
        $property->setValue($scalar, $json_obj);

        $result = $scalar->jsonSerialize();

        $this->assertEquals(['serialized' => 'data'], $result);
    }

    #[Group('unit')]
    public function testJsonSerializeThrowsForNonSerializableObject() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Value is an object and does not implement the Export or JsonSerializable interface'
        );

        $scalar = Scalar::init();
        
        // Use reflection to bypass validation and set value directly
        $reflection = new \ReflectionClass($scalar);
        $property = $reflection->getProperty('value');
        $property->setValue($scalar, new \stdClass());

        $scalar->jsonSerialize();
    }

    #[Group('unit')]
    public function testJsonSerializeWithJsonSerializableObjectInLocale() {
        $json_obj = new class implements \JsonSerializable {
            public function jsonSerialize(): mixed {
                return ['locale_serialized' => 'data'];
            }
        };

        $scalar = Scalar::init();
        
        // Use reflection to add a JsonSerializable object to localized_values
        $reflection = new \ReflectionClass($scalar);
        $property = $reflection->getProperty('localized_values');
        $property->setValue($scalar, ['es' => $json_obj]);

        $result = $scalar->jsonSerialize();

        $this->assertEquals([
            'es' => ['locale_serialized' => 'data'],
        ], $result);
    }

    #[Group('unit')]
    public function testJsonSerializeThrowsForNonSerializableObjectInLocale() {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Locale en does not implement the Export or JsonSerializable interface'
        );

        $scalar = Scalar::init();
        
        // Use reflection to add a non-serializable object to localized_values
        $reflection = new \ReflectionClass($scalar);
        $property = $reflection->getProperty('localized_values');
        $property->setValue($scalar, ['en' => new \stdClass()]);

        $scalar->jsonSerialize();
    }

    #[Group('unit')]
    public function testMultipleLocalesWithMixedScalarAndObjectValues() {
        $json_obj = new class implements \JsonSerializable {
            public function jsonSerialize(): mixed {
                return ['type' => 'object'];
            }
        };

        $scalar = Scalar::init();
        
        // Use reflection to set mixed localized values
        $reflection = new \ReflectionClass($scalar);
        $property = $reflection->getProperty('localized_values');
        $property->setValue($scalar, [
            'en' => 'plain string',
            'es' => $json_obj,
            'fr' => 42,
        ]);

        $result = $scalar->jsonSerialize();

        $this->assertEquals([
            'en' => 'plain string',
            'es' => ['type' => 'object'],
            'fr' => 42,
        ], $result);
    }
}
