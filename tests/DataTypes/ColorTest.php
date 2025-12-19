<?php

namespace DealNews\DatoCMS\CMA\Tests\DataTypes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\DataTypes\Color;

class ColorTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('validColorProvider')]
    public function testValidColorValues(array $value, array $expected) {
        $color = Color::init();
        $color->set($value);
        
        $this->assertEquals($expected, $color->jsonSerialize());
    }

    #[Group('unit')]
    #[DataProvider('invalidColorProvider')]
    public function testInvalidColorValues(mixed $value, string $expectedMessage) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        
        $color = Color::init();
        $color->set($value);
    }

    #[Group('unit')]
    public function testSetColorHelperMethod() {
        $color = Color::init();
        $result = $color->setColor(128, 64, 200, 150);
        
        $this->assertInstanceOf(Color::class, $result);
        $this->assertEquals([
            'red' => 128,
            'green' => 64,
            'blue' => 200,
            'alpha' => 150,
        ], $color->jsonSerialize());
    }

    #[Group('unit')]
    public function testSetColorMethodChaining() {
        $color = Color::init();
        $result = $color->setColor(0, 0, 0, 0);
        
        $this->assertInstanceOf(Color::class, $result);
        $this->assertSame($color, $result);
    }

    #[Group('unit')]
    public function testSetMethodReturnsStatic() {
        $color = Color::init();
        $result = $color->set(['red' => 100, 'green' => 100, 'blue' => 100, 'alpha' => 100]);
        
        $this->assertInstanceOf(Color::class, $result);
        $this->assertSame($color, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithValidColor() {
        $color = Color::init();
        $color->set(['red' => 255, 'green' => 0, 'blue' => 0, 'alpha' => 255]);
        $color->addLocale('en', ['red' => 0, 'green' => 255, 'blue' => 0, 'alpha' => 255]);
        $color->addLocale('es', ['red' => 0, 'green' => 0, 'blue' => 255, 'alpha' => 255]);
        
        $result = $color->jsonSerialize();
        
        $this->assertIsArray($result);
        $this->assertEquals(['red' => 0, 'green' => 255, 'blue' => 0, 'alpha' => 255], $result['en']);
        $this->assertEquals(['red' => 0, 'green' => 0, 'blue' => 255, 'alpha' => 255], $result['es']);
    }

    #[Group('unit')]
    public function testAddLocaleReturnsStatic() {
        $color = Color::init();
        $result = $color->addLocale('en', ['red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => 0]);
        
        $this->assertInstanceOf(Color::class, $result);
        $this->assertSame($color, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithInvalidColor() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in expected format');
        
        $color = Color::init();
        $color->addLocale('en', 'invalid');
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsNullWhenEmpty() {
        $color = Color::init();
        
        $this->assertNull($color->jsonSerialize());
    }

    #[Group('unit')]
    public function testJsonSerializePrioritizesLocalizedValues() {
        $color = Color::init();
        $color->set(['red' => 255, 'green' => 0, 'blue' => 0, 'alpha' => 255]);
        $color->addLocale('en', ['red' => 0, 'green' => 255, 'blue' => 0, 'alpha' => 255]);
        
        $result = $color->jsonSerialize();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('en', $result);
        $this->assertEquals(['red' => 0, 'green' => 255, 'blue' => 0, 'alpha' => 255], $result['en']);
    }

    #[Group('unit')]
    public function testNullValueIsValid() {
        $color = Color::init();
        $color->set(null);
        
        $this->assertNull($color->jsonSerialize());
    }

    public static function validColorProvider(): array {
        return [
            'black transparent' => [
                ['red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => 0],
                ['red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => 0],
            ],
            'white opaque' => [
                ['red' => 255, 'green' => 255, 'blue' => 255, 'alpha' => 255],
                ['red' => 255, 'green' => 255, 'blue' => 255, 'alpha' => 255],
            ],
            'random mid-range' => [
                ['red' => 128, 'green' => 64, 'blue' => 200, 'alpha' => 150],
                ['red' => 128, 'green' => 64, 'blue' => 200, 'alpha' => 150],
            ],
            'red boundary min' => [
                ['red' => 0, 'green' => 100, 'blue' => 100, 'alpha' => 100],
                ['red' => 0, 'green' => 100, 'blue' => 100, 'alpha' => 100],
            ],
            'red boundary max' => [
                ['red' => 255, 'green' => 100, 'blue' => 100, 'alpha' => 100],
                ['red' => 255, 'green' => 100, 'blue' => 100, 'alpha' => 100],
            ],
            'green boundary min' => [
                ['red' => 100, 'green' => 0, 'blue' => 100, 'alpha' => 100],
                ['red' => 100, 'green' => 0, 'blue' => 100, 'alpha' => 100],
            ],
            'green boundary max' => [
                ['red' => 100, 'green' => 255, 'blue' => 100, 'alpha' => 100],
                ['red' => 100, 'green' => 255, 'blue' => 100, 'alpha' => 100],
            ],
            'blue boundary min' => [
                ['red' => 100, 'green' => 100, 'blue' => 0, 'alpha' => 100],
                ['red' => 100, 'green' => 100, 'blue' => 0, 'alpha' => 100],
            ],
            'blue boundary max' => [
                ['red' => 100, 'green' => 100, 'blue' => 255, 'alpha' => 100],
                ['red' => 100, 'green' => 100, 'blue' => 255, 'alpha' => 100],
            ],
            'alpha boundary min' => [
                ['red' => 100, 'green' => 100, 'blue' => 100, 'alpha' => 0],
                ['red' => 100, 'green' => 100, 'blue' => 100, 'alpha' => 0],
            ],
            'alpha boundary max' => [
                ['red' => 100, 'green' => 100, 'blue' => 100, 'alpha' => 255],
                ['red' => 100, 'green' => 100, 'blue' => 100, 'alpha' => 255],
            ],
        ];
    }

    public static function invalidColorProvider(): array {
        return [
            'missing red' => [
                ['green' => 100, 'blue' => 100, 'alpha' => 100],
                "Invalid color attribute: 'red'",
            ],
            'missing green' => [
                ['red' => 100, 'blue' => 100, 'alpha' => 100],
                "Invalid color attribute: 'green'",
            ],
            'missing blue' => [
                ['red' => 100, 'green' => 100, 'alpha' => 100],
                "Invalid color attribute: 'blue'",
            ],
            'missing alpha' => [
                ['red' => 100, 'green' => 100, 'blue' => 100],
                "Invalid color attribute: 'alpha'",
            ],
            'red below 0' => [
                ['red' => -1, 'green' => 100, 'blue' => 100, 'alpha' => 100],
                "Invalid color attribute: 'red'",
            ],
            'red above 255' => [
                ['red' => 256, 'green' => 100, 'blue' => 100, 'alpha' => 100],
                "Invalid color attribute: 'red'",
            ],
            'green below 0' => [
                ['red' => 100, 'green' => -1, 'blue' => 100, 'alpha' => 100],
                "Invalid color attribute: 'green'",
            ],
            'green above 255' => [
                ['red' => 100, 'green' => 256, 'blue' => 100, 'alpha' => 100],
                "Invalid color attribute: 'green'",
            ],
            'blue below 0' => [
                ['red' => 100, 'green' => 100, 'blue' => -1, 'alpha' => 100],
                "Invalid color attribute: 'blue'",
            ],
            'blue above 255' => [
                ['red' => 100, 'green' => 100, 'blue' => 256, 'alpha' => 100],
                "Invalid color attribute: 'blue'",
            ],
            'alpha below 0' => [
                ['red' => 100, 'green' => 100, 'blue' => 100, 'alpha' => -1],
                "Invalid color attribute: 'alpha'",
            ],
            'alpha above 255' => [
                ['red' => 100, 'green' => 100, 'blue' => 100, 'alpha' => 256],
                "Invalid color attribute: 'alpha'",
            ],
            'green as float' => [
                ['red' => 100, 'green' => 128.5, 'blue' => 100, 'alpha' => 100],
                "Invalid color attribute: 'green'",
            ],
            'non-array input' => [
                'not an array',
                'Value not in expected format',
            ],
            'empty array' => [
                [],
                "Invalid color attribute: 'red'",
            ],
        ];
    }
}
