<?php

namespace DealNews\DatoCMS\CMA\Tests\DataTypes;

use DealNews\DatoCMS\CMA\DataTypes\Scalar;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class ScalarTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('validScalarProvider')]
    public function testValidScalarValues(mixed $value, mixed $expected) {
        $scalar = Scalar::init();
        $scalar->set($value);

        $this->assertEquals($expected, $scalar->jsonSerialize());
    }

    #[Group('unit')]
    #[DataProvider('invalidScalarProvider')]
    public function testInvalidScalarValues(mixed $value, string $expectedMessage) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $scalar = Scalar::init();
        $scalar->set($value);
    }

    #[Group('unit')]
    public function testSetMethodReturnsStatic() {
        $scalar = Scalar::init();
        $result = $scalar->set('test');

        $this->assertInstanceOf(Scalar::class, $result);
        $this->assertSame($scalar, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithValidValue() {
        $scalar = Scalar::init();
        $scalar->set('default value');
        $scalar->addLocale('en', 'English value');
        $scalar->addLocale('es', 'Spanish value');

        $result = $scalar->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEquals('English value', $result['en']);
        $this->assertEquals('Spanish value', $result['es']);
    }

    #[Group('unit')]
    public function testAddLocaleReturnsStatic() {
        $scalar = Scalar::init();
        $result = $scalar->addLocale('en', 'test');

        $this->assertInstanceOf(Scalar::class, $result);
        $this->assertSame($scalar, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithInvalidValue() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be scalar');

        $scalar = Scalar::init();
        $scalar->addLocale('en', ['invalid' => 'array']);
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsNullWhenEmpty() {
        $scalar = Scalar::init();

        $this->assertNull($scalar->jsonSerialize());
    }

    #[Group('unit')]
    public function testJsonSerializePrioritizesLocalizedValues() {
        $scalar = Scalar::init();
        $scalar->set('default value');
        $scalar->addLocale('en', 'English value');

        $result = $scalar->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEquals('English value', $result['en']);
    }

    #[Group('unit')]
    public function testNullValueIsValid() {
        $scalar = Scalar::init();
        $scalar->set(null);

        $this->assertNull($scalar->jsonSerialize());
    }

    public static function validScalarProvider(): array {
        return [
            'empty string'              => ['', ''],
            'normal string'             => ['hello world', 'hello world'],
            'long string'               => [str_repeat('a', 1000), str_repeat('a', 1000)],
            'string with special chars' => ['hello™ world®', 'hello™ world®'],
            'negative integer'          => [-42, -42],
            'zero integer'              => [0, 0],
            'positive integer'          => [42, 42],
            'negative float'            => [-3.14, -3.14],
            'zero float'                => [0.0, 0.0],
            'positive float'            => [3.14, 3.14],
            'boolean true'              => [true, true],
            'boolean false'             => [false, false],
            'null value'                => [null, null],
        ];
    }

    public static function invalidScalarProvider(): array {
        return [
            'array' => [
                ['value' => 'test'],
                'Value must be scalar',
            ],
            'empty array' => [
                [],
                'Value must be scalar',
            ],
            'object' => [
                new \stdClass(),
                'Value must be scalar',
            ],
            'resource' => [
                fopen('php://memory', 'r'),
                'Value must be scalar',
            ],
        ];
    }
}
