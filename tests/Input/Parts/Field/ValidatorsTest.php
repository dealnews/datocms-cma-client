<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Field;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Validators;

/**
 * Tests for the Input\Parts\Field\Validators class
 */
class ValidatorsTest extends TestCase {

    #[Group('unit')]
    public function testInitFactoryMethod(): void {
        $validators = Validators::init();

        $this->assertInstanceOf(Validators::class, $validators);
    }

    #[Group('unit')]
    public function testIsRequiredSetsFlag(): void {
        $validators = Validators::init()->isRequired();

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('required', $result);
        $this->assertEquals('{}', $result['required']);
    }

    #[Group('unit')]
    public function testIsUniqueSetsFlag(): void {
        $validators = Validators::init()->isUnique();

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('unique', $result);
        $this->assertEquals('{}', $result['unique']);
    }

    #[Group('unit')]
    public function testMethodChaining(): void {
        $validators = Validators::init()
            ->isRequired()
            ->isUnique();

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('required', $result);
        $this->assertEquals('{}', $result['required']);
        $this->assertArrayHasKey('unique', $result);
        $this->assertEquals('{}', $result['unique']);
    }

    #[Group('unit')]
    public function testSetDateRange(): void {
        $validators = Validators::init()
            ->setDateRange('2024-01-01', '2024-12-31');

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('date_range', $result);
        $this->assertEquals('2024-01-01', $result['date_range']['min']);
        $this->assertEquals('2024-12-31', $result['date_range']['max']);
    }

    #[Group('unit')]
    public function testSetDateTimeRange(): void {
        $validators = Validators::init()
            ->setDateTimeRange('2024-01-01T00:00:00Z', '2024-12-31T23:59:59Z');

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('date_time_range', $result);
        $this->assertEquals('2024-01-01T00:00:00Z', $result['date_time_range']['min']);
        $this->assertEquals('2024-12-31T23:59:59Z', $result['date_time_range']['max']);
    }

    #[Group('unit')]
    public function testSetEnum(): void {
        $validators = Validators::init()
            ->setEnum(['option1', 'option2', 'option3']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('enum', $result);
        $this->assertEquals(['values' => ['option1', 'option2', 'option3']], $result['enum']);
    }

    #[Group('unit')]
    public function testSetFileExtensions(): void {
        $validators = Validators::init()
            ->setFileExtensions(['jpg', 'png', 'gif']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('extension', $result);
        $this->assertArrayHasKey('extensions', $result['extension']);
        $this->assertEquals(['jpg', 'png', 'gif'], $result['extension']['extensions']);
    }

    #[Group('unit')]
    public function testSetFileSizeMin(): void {
        $validators = Validators::init()
            ->setFileSizeMin(1, 'MB');

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('file_size', $result);
        $this->assertEquals(1, $result['file_size']['min_value']);
        $this->assertEquals('MB', $result['file_size']['min_unit']);
    }

    #[Group('unit')]
    public function testSetFileSizeMax(): void {
        $validators = Validators::init()
            ->setFileSizeMax(10, 'MB');

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('file_size', $result);
        $this->assertEquals(10, $result['file_size']['max_value']);
        $this->assertEquals('MB', $result['file_size']['max_unit']);
    }

    #[Group('unit')]
    public function testSetPredefinedFormat(): void {
        $validators = Validators::init()
            ->setPredefinedFormat('email');

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('format', $result);
        $this->assertEquals(['predefined_pattern' => 'email'], $result['format']);
    }

    #[Group('unit')]
    public function testSetSlugPredefinedFormat(): void {
        $validators = Validators::init()
            ->setSlugPredefinedFormat('webpage_slug');

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('slug_format', $result);
        $this->assertEquals(['predefined_pattern' => 'webpage_slug'], $result['slug_format']);
    }

    #[Group('unit')]
    public function testSetImageDimensions(): void {
        $validators = Validators::init()
            ->setImageDimensions(100, 1920, 100, 1080);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('image_dimensions', $result);
        $this->assertEquals(100, $result['image_dimensions']['width_min_value']);
        $this->assertEquals(1920, $result['image_dimensions']['width_max_value']);
        $this->assertEquals(100, $result['image_dimensions']['height_min_value']);
        $this->assertEquals(1080, $result['image_dimensions']['height_max_value']);
    }

    #[Group('unit')]
    public function testSetImageAspectRatio(): void {
        $validators = Validators::init()
            ->setImageAspectRatio(16, 9, null, null, 4, 3);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('image_aspect_ratio', $result);
        $this->assertEquals(16, $result['image_aspect_ratio']['min_ar_numerator']);
        $this->assertEquals(9, $result['image_aspect_ratio']['min_ar_denominator']);
        $this->assertEquals(4, $result['image_aspect_ratio']['max_ar_numerator']);
        $this->assertEquals(3, $result['image_aspect_ratio']['max_ar_denominator']);
    }

    #[Group('unit')]
    public function testSetItemTypesSingle(): void {
        $validators = Validators::init()
            ->setItemTypesSingle(['blog_post', 'page']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('item_item_type', $result);
        $this->assertEquals(['item_types' => ['blog_post', 'page']], $result['item_item_type']);
    }

    #[Group('unit')]
    public function testSetItemTypesMultiple(): void {
        $validators = Validators::init()
            ->setItemTypesMultiple(['blog_post', 'page']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('items_item_type', $result);
        $this->assertEquals(['item_types' => ['blog_post', 'page']], $result['items_item_type']);
    }

    #[Group('unit')]
    public function testSetLengthRange(): void {
        $validators = Validators::init()
            ->setLengthRange(1, 100);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('length', $result);
        $this->assertEquals(1, $result['length']['min']);
        $this->assertEquals(100, $result['length']['max']);
    }

    #[Group('unit')]
    public function testSetLength(): void {
        $validators = Validators::init()
            ->setLength(50);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('length', $result);
        $this->assertEquals(50, $result['length']['eq']);
    }

    #[Group('unit')]
    public function testSetNumberRange(): void {
        $validators = Validators::init()
            ->setNumberRange(0, 999);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('number_range', $result);
        $this->assertEquals(0, $result['number_range']['min']);
        $this->assertEquals(999, $result['number_range']['max']);
    }

    #[Group('unit')]
    public function testRequiresAlt(): void {
        $validators = Validators::init()
            ->requiresAlt();

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('required_alt_title', $result);
        $this->assertTrue($result['required_alt_title']['alt']);
    }

    #[Group('unit')]
    public function testRequiresTitle(): void {
        $validators = Validators::init()
            ->requiresTitle();

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('required_alt_title', $result);
        $this->assertTrue($result['required_alt_title']['title']);
    }

    #[Group('unit')]
    public function testRequiresSEOTitle(): void {
        $validators = Validators::init()
            ->requiresSEOTitle();

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('required_seo_fields', $result);
        $this->assertTrue($result['required_seo_fields']['title']);
    }

    #[Group('unit')]
    public function testRequiresSEODescription(): void {
        $validators = Validators::init()
            ->requiresSEODescription();

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('required_seo_fields', $result);
        $this->assertTrue($result['required_seo_fields']['description']);
    }

    #[Group('unit')]
    public function testSetTitleLength(): void {
        $validators = Validators::init()
            ->setTitleLength(10, 60);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('title_length', $result);
        $this->assertEquals(10, $result['title_length']['min']);
        $this->assertEquals(60, $result['title_length']['max']);
    }

    #[Group('unit')]
    public function testSetDescriptionLength(): void {
        $validators = Validators::init()
            ->setDescriptionLength(50, 160);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('description_length', $result);
        $this->assertEquals(50, $result['description_length']['min']);
        $this->assertEquals(160, $result['description_length']['max']);
    }

    #[Group('unit')]
    public function testSetRichTextBlocks(): void {
        $validators = Validators::init()
            ->setRichTextBlocks(['block_quote', 'code_block']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('rich_text_blocks', $result);
        $this->assertEquals(['item_types' => ['block_quote', 'code_block']], $result['rich_text_blocks']);
    }

    #[Group('unit')]
    public function testSetSingleBlockBlocks(): void {
        $validators = Validators::init()
            ->setSingleBlockBlocks(['hero_block', 'cta_block']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('single_block_blocks', $result);
        $this->assertEquals(['item_types' => ['hero_block', 'cta_block']], $result['single_block_blocks']);
    }

    #[Group('unit')]
    public function testSetSanitizedHtml(): void {
        $validators = Validators::init()
            ->setSanitizedHtml(true);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('sanitized_html', $result);
        $this->assertEquals(['sanitize_before_validation' => true], $result['sanitized_html']);
    }

    #[Group('unit')]
    public function testSetStructuredTextBlocks(): void {
        $validators = Validators::init()
            ->setStructuredTextBlocks(['text_block', 'media_block']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('structured_text_blocks', $result);
        $this->assertEquals(['item_types' => ['text_block', 'media_block']], $result['structured_text_blocks']);
    }

    #[Group('unit')]
    public function testSetStructuredTextInlineBlocks(): void {
        $validators = Validators::init()
            ->setStructuredTextInlineBlocks(['inline_code', 'inline_link']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('structured_text_inline_blocks', $result);
        $this->assertEquals(['item_types' => ['inline_code', 'inline_link']], $result['structured_text_inline_blocks']);
    }

    #[Group('unit')]
    public function testSetStructuredTextLinks(): void {
        $validators = Validators::init()
            ->setStructuredTextLinks(['blog_post', 'page']);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('structured_text_links', $result);
        $this->assertEquals(['item_types' => ['blog_post', 'page']], $result['structured_text_links']);
    }

    #[Group('unit')]
    public function testSetSizeRange(): void {
        $validators = Validators::init()
            ->setSizeRange(1, 10);

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('size', $result);
        $this->assertEquals(1, $result['size']['min']);
        $this->assertEquals(10, $result['size']['max']);
    }

    #[Group('unit')]
    public function testSetSlugTitleField(): void {
        $validators = Validators::init()
            ->setSlugTitleField('field123');

        $result = $validators->jsonSerialize();

        $this->assertArrayHasKey('slug_title_field', $result);
        $this->assertEquals(['title_field_id' => 'field123'], $result['slug_title_field']);
    }

    #[Group('unit')]
    public function testJsonSerializeFormat(): void {
        $validators = Validators::init()
            ->isRequired()
            ->isUnique()
            ->setLengthRange(5, 50)
            ->setEnum(['red', 'green', 'blue']);

        $result = $validators->jsonSerialize();

        // Should only include set validators (no empty/null values)
        $this->assertIsArray($result);
        $this->assertArrayHasKey('required', $result);
        $this->assertArrayHasKey('unique', $result);
        $this->assertArrayHasKey('length', $result);
        $this->assertArrayHasKey('enum', $result);

        // Should not include unset validators
        $this->assertArrayNotHasKey('date_range', $result);
        $this->assertArrayNotHasKey('file_size', $result);
    }
}
