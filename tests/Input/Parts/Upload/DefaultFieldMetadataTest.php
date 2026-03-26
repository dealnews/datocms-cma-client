<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Upload;

use DealNews\DatoCMS\CMA\Input\Parts\Upload\DefaultFieldMetadata;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Input\Parts\Upload\DefaultFieldMetadata class
 */
class DefaultFieldMetadataTest extends TestCase {

    // =========================================================================
    // addLocale() tests
    // =========================================================================

    #[Group('unit')]
    public function testAddLocaleWithAllFields() {
        $metadata    = new DefaultFieldMetadata();
        $focal_point = ['x' => 0.5, 'y' => 0.3];
        $custom_data = ['key' => 'value'];

        $result = $metadata->addLocale('en', 'Alt text', 'Title', $focal_point, $custom_data);

        $this->assertSame($metadata, $result);
        $locale_data = $metadata->getLocale('en');
        $this->assertEquals('Alt text', $locale_data['alt']);
        $this->assertEquals('Title', $locale_data['title']);
        $this->assertEquals($focal_point, $locale_data['focal_point']);
        $this->assertEquals($custom_data, $locale_data['custom_data']);
    }

    #[Group('unit')]
    public function testAddLocaleWithOnlyAlt() {
        $metadata = new DefaultFieldMetadata();

        $metadata->addLocale('en', 'Alt text');

        $locale_data = $metadata->getLocale('en');
        $this->assertEquals('Alt text', $locale_data['alt']);
        $this->assertNull($locale_data['title']);
        $this->assertNull($locale_data['focal_point']);
        $this->assertNull($locale_data['custom_data']);
    }

    #[Group('unit')]
    public function testAddLocaleMultipleLocales() {
        $metadata = new DefaultFieldMetadata();

        $metadata->addLocale('en', 'English alt', 'English title');
        $metadata->addLocale('es', 'Texto alternativo', 'Título');

        $this->assertEquals('English alt', $metadata->getLocale('en')['alt']);
        $this->assertEquals('Texto alternativo', $metadata->getLocale('es')['alt']);
    }

    #[Group('unit')]
    public function testAddLocaleOverwritesExisting() {
        $metadata = new DefaultFieldMetadata();

        $metadata->addLocale('en', 'First alt');
        $metadata->addLocale('en', 'Updated alt', 'Updated title');

        $locale_data = $metadata->getLocale('en');
        $this->assertEquals('Updated alt', $locale_data['alt']);
        $this->assertEquals('Updated title', $locale_data['title']);
    }

    // =========================================================================
    // Focal point validation tests
    // =========================================================================

    #[Group('unit')]
    public function testAddLocaleWithValidFocalPointBoundaries() {
        $metadata = new DefaultFieldMetadata();

        // Test minimum values
        $metadata->addLocale('en', null, null, ['x' => 0, 'y' => 0]);
        $this->assertEquals(['x' => 0, 'y' => 0], $metadata->getLocale('en')['focal_point']);

        // Test maximum values
        $metadata->addLocale('es', null, null, ['x' => 1, 'y' => 1]);
        $this->assertEquals(['x' => 1, 'y' => 1], $metadata->getLocale('es')['focal_point']);
    }

    #[Group('unit')]
    public function testAddLocaleThrowsOnMissingFocalPointX() {
        $metadata = new DefaultFieldMetadata();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('focal_point must contain "x" and "y" keys');

        $metadata->addLocale('en', null, null, ['y' => 0.5]);
    }

    #[Group('unit')]
    public function testAddLocaleThrowsOnMissingFocalPointY() {
        $metadata = new DefaultFieldMetadata();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('focal_point must contain "x" and "y" keys');

        $metadata->addLocale('en', null, null, ['x' => 0.5]);
    }

    #[Group('unit')]
    public function testAddLocaleThrowsOnNonNumericFocalPoint() {
        $metadata = new DefaultFieldMetadata();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('focal_point x and y values must be numeric');

        $metadata->addLocale('en', null, null, ['x' => 'invalid', 'y' => 0.5]);
    }

    #[Group('unit')]
    public function testAddLocaleThrowsOnFocalPointXBelowZero() {
        $metadata = new DefaultFieldMetadata();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('focal_point x and y values must be between 0 and 1');

        $metadata->addLocale('en', null, null, ['x' => -0.1, 'y' => 0.5]);
    }

    #[Group('unit')]
    public function testAddLocaleThrowsOnFocalPointXAboveOne() {
        $metadata = new DefaultFieldMetadata();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('focal_point x and y values must be between 0 and 1');

        $metadata->addLocale('en', null, null, ['x' => 1.1, 'y' => 0.5]);
    }

    #[Group('unit')]
    public function testAddLocaleThrowsOnFocalPointYBelowZero() {
        $metadata = new DefaultFieldMetadata();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('focal_point x and y values must be between 0 and 1');

        $metadata->addLocale('en', null, null, ['x' => 0.5, 'y' => -0.1]);
    }

    #[Group('unit')]
    public function testAddLocaleThrowsOnFocalPointYAboveOne() {
        $metadata = new DefaultFieldMetadata();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('focal_point x and y values must be between 0 and 1');

        $metadata->addLocale('en', null, null, ['x' => 0.5, 'y' => 1.1]);
    }

    // =========================================================================
    // getLocale() tests
    // =========================================================================

    #[Group('unit')]
    public function testGetLocaleReturnsNullForUnsetLocale() {
        $metadata = new DefaultFieldMetadata();

        $this->assertNull($metadata->getLocale('en'));
    }

    // =========================================================================
    // hasLocales() tests
    // =========================================================================

    #[Group('unit')]
    public function testHasLocalesReturnsFalseWhenEmpty() {
        $metadata = new DefaultFieldMetadata();

        $this->assertFalse($metadata->hasLocales());
    }

    #[Group('unit')]
    public function testHasLocalesReturnsTrueWhenSet() {
        $metadata = new DefaultFieldMetadata();
        $metadata->addLocale('en', 'Alt');

        $this->assertTrue($metadata->hasLocales());
    }

    // =========================================================================
    // getLocaleCodes() tests
    // =========================================================================

    #[Group('unit')]
    public function testGetLocaleCodesReturnsEmptyWhenNoLocales() {
        $metadata = new DefaultFieldMetadata();

        $this->assertEquals([], $metadata->getLocaleCodes());
    }

    #[Group('unit')]
    public function testGetLocaleCodesReturnsAllCodes() {
        $metadata = new DefaultFieldMetadata();
        $metadata->addLocale('en', 'English');
        $metadata->addLocale('es', 'Spanish');
        $metadata->addLocale('fr', 'French');

        $codes = $metadata->getLocaleCodes();

        $this->assertCount(3, $codes);
        $this->assertContains('en', $codes);
        $this->assertContains('es', $codes);
        $this->assertContains('fr', $codes);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayReturnsEmptyWhenNoLocales() {
        $metadata = new DefaultFieldMetadata();

        $this->assertEquals([], $metadata->toArray());
    }

    #[Group('unit')]
    public function testToArrayFiltersNullValues() {
        $metadata = new DefaultFieldMetadata();
        $metadata->addLocale('en', 'Alt text', null, null, null);

        $array = $metadata->toArray();

        $this->assertArrayHasKey('en', $array);
        $this->assertArrayHasKey('alt', $array['en']);
        $this->assertArrayNotHasKey('title', $array['en']);
        $this->assertArrayNotHasKey('focal_point', $array['en']);
        // custom_data should be stdClass when null/empty
        $this->assertArrayHasKey('custom_data', $array['en']);
        $this->assertInstanceOf(\stdClass::class, $array['en']['custom_data']);
    }

    #[Group('unit')]
    public function testToArrayIncludesAllNonNullValues() {
        $metadata    = new DefaultFieldMetadata();
        $focal_point = ['x' => 0.5, 'y' => 0.5];
        $custom_data = ['custom' => 'value'];
        $metadata->addLocale('en', 'Alt', 'Title', $focal_point, $custom_data);

        $array = $metadata->toArray();

        $this->assertEquals([
            'en' => [
                'alt'         => 'Alt',
                'title'       => 'Title',
                'focal_point' => $focal_point,
                'custom_data' => $custom_data,
            ],
        ], $array);
    }

    #[Group('unit')]
    public function testToArrayWithMultipleLocales() {
        $metadata = new DefaultFieldMetadata();
        $metadata->addLocale('en', 'English alt');
        $metadata->addLocale('es', 'Texto alternativo');

        $array = $metadata->toArray();

        $this->assertArrayHasKey('en', $array);
        $this->assertArrayHasKey('es', $array);
        $this->assertEquals('English alt', $array['en']['alt']);
        $this->assertEquals('Texto alternativo', $array['es']['alt']);
    }

    #[Group('unit')]
    public function testToArrayReturnsStdClassForEmptyCustomData() {
        $metadata = new DefaultFieldMetadata();
        // Add locale with no custom_data (null)
        $metadata->addLocale('en', 'Alt text', 'Title', null, null);

        $array = $metadata->toArray();

        $this->assertArrayHasKey('custom_data', $array['en']);
        $this->assertInstanceOf(\stdClass::class, $array['en']['custom_data']);
    }

    #[Group('unit')]
    public function testToArrayReturnsStdClassForEmptyCustomDataArray() {
        $metadata = new DefaultFieldMetadata();
        // Add locale with empty array for custom_data
        $metadata->addLocale('en', 'Alt text', 'Title', null, []);

        $array = $metadata->toArray();

        $this->assertArrayHasKey('custom_data', $array['en']);
        $this->assertInstanceOf(\stdClass::class, $array['en']['custom_data']);
    }

    #[Group('unit')]
    public function testToArrayPreservesNonEmptyCustomData() {
        $metadata = new DefaultFieldMetadata();
        $custom_data = ['key' => 'value', 'foo' => 'bar'];
        $metadata->addLocale('en', 'Alt text', null, null, $custom_data);

        $array = $metadata->toArray();

        $this->assertArrayHasKey('custom_data', $array['en']);
        $this->assertIsArray($array['en']['custom_data']);
        $this->assertEquals($custom_data, $array['en']['custom_data']);
    }
}
