<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Field;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Validators;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Appearance;

/**
 * Tests for the Input\Parts\Field\Attributes class
 */
class AttributesTest extends TestCase {

    #[Group('unit')]
    public function testDefaultValues(): void {
        $attributes = new Attributes();

        $this->assertNull($attributes->label);
        $this->assertNull($attributes->field_type);
        $this->assertNull($attributes->api_key);
        $this->assertNull($attributes->localized);
        $this->assertNull($attributes->validators);
        $this->assertNull($attributes->appearance);
        $this->assertNull($attributes->position);
        $this->assertFalse($attributes->hint);
        $this->assertEquals([], $attributes->default_value);
        $this->assertNull($attributes->deep_filtering_enabled);
    }

    #[Group('unit')]
    #[DataProvider('validFieldTypesProvider')]
    public function testFieldTypeAcceptsValidTypes(string $fieldType): void {
        $attributes = new Attributes();
        $attributes->field_type = $fieldType;

        $this->assertEquals($fieldType, $attributes->field_type);
    }

    public static function validFieldTypesProvider(): array {
        return array_map(fn($type) => [$type], Attributes::VALID_FIELD_TYPES);
    }

    #[Group('unit')]
    #[Group('unit')]
    public function testFieldTypeCanBeSetToNull(): void {
        $attributes = new Attributes();
        $attributes->field_type = 'string';
        $attributes->field_type = null;

        $this->assertNull($attributes->field_type);
    }

    #[Group('unit')]
    public function testStringPropertiesCanBeSet(): void {
        $attributes = new Attributes();
        $attributes->label = 'Product Title';
        $attributes->api_key = 'product_title';
        $attributes->hint = 'Enter the product title here';

        $this->assertEquals('Product Title', $attributes->label);
        $this->assertEquals('product_title', $attributes->api_key);
        $this->assertEquals('Enter the product title here', $attributes->hint);
    }

    #[Group('unit')]
    public function testValidatorsCanBeSetAsArray(): void {
        $attributes = new Attributes();
        $attributes->validators = [
            'required' => true,
            'length' => ['min' => 1, 'max' => 100],
        ];

        $this->assertIsArray($attributes->validators);
        $this->assertTrue($attributes->validators['required']);
        $this->assertEquals(['min' => 1, 'max' => 100], $attributes->validators['length']);
    }

    #[Group('unit')]
    public function testValidatorsCanBeSetAsObject(): void {
        $validators = new Validators();

        $attributes = new Attributes();
        $attributes->validators = $validators;

        $this->assertInstanceOf(Validators::class, $attributes->validators);
    }

    #[Group('unit')]
    public function testAppearanceCanBeSetAsArray(): void {
        $attributes = new Attributes();
        $attributes->appearance = [
            'editor' => 'single_line',
            'parameters' => ['heading' => false],
        ];

        $this->assertIsArray($attributes->appearance);
        $this->assertEquals('single_line', $attributes->appearance['editor']);
    }

    #[Group('unit')]
    public function testAppearanceCanBeSetAsObject(): void {
        $appearance = new Appearance();
        $appearance->editor = 'single_line';

        $attributes = new Attributes();
        $attributes->appearance = $appearance;

        $this->assertInstanceOf(Appearance::class, $attributes->appearance);
        $this->assertEquals('single_line', $attributes->appearance->editor);
    }

    #[Group('unit')]
    public function testToArraySerialization(): void {
        $validators = new Validators();
        $appearance = new Appearance();
        $appearance->editor = 'wysiwyg';

        $attributes = new Attributes();
        $attributes->label = 'Content';
        $attributes->field_type = 'text';
        $attributes->api_key = 'content';
        $attributes->localized = true;
        $attributes->validators = $validators;
        $attributes->appearance = $appearance;
        $attributes->position = 5;
        $attributes->hint = 'Main content field';
        $attributes->default_value = 'Default text';
        $attributes->deep_filtering_enabled = false;

        $array = $attributes->toArray();

        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('Content', $array['label']);
        $this->assertArrayHasKey('field_type', $array);
        $this->assertEquals('text', $array['field_type']);
        $this->assertArrayHasKey('api_key', $array);
        $this->assertEquals('content', $array['api_key']);
        $this->assertArrayHasKey('localized', $array);
        $this->assertTrue($array['localized']);
        $this->assertArrayHasKey('validators', $array);
        $this->assertArrayHasKey('appearance', $array);
        $this->assertArrayHasKey('position', $array);
        $this->assertEquals(5, $array['position']);
        $this->assertArrayHasKey('hint', $array);
        $this->assertEquals('Main content field', $array['hint']);
        $this->assertArrayHasKey('default_value', $array);
        $this->assertEquals('Default text', $array['default_value']);
        $this->assertArrayHasKey('deep_filtering_enabled', $array);
        $this->assertFalse($array['deep_filtering_enabled']);
    }
}
