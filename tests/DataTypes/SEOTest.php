<?php

namespace DealNews\DatoCMS\CMA\Tests\DataTypes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\DataTypes\SEO;

class SEOTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('validSEOProvider')]
    public function testValidSEOValues(array $value, array $expected) {
        $seo = SEO::init();
        $seo->set($value);
        
        $this->assertEquals($expected, $seo->jsonSerialize());
    }

    #[Group('unit')]
    #[DataProvider('invalidSEOProvider')]
    public function testInvalidSEOValues(mixed $value, string $expectedMessage) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        
        $seo = SEO::init();
        $seo->set($value);
    }

    #[Group('unit')]
    public function testSetSEOHelperMethod() {
        $seo = SEO::init();
        $result = $seo->setSEO(
            'My Page Title',
            'This is a description of my page',
            'image123',
            'summary',
            false
        );
        
        $this->assertInstanceOf(SEO::class, $result);
        $this->assertEquals([
            'title' => 'My Page Title',
            'description' => 'This is a description of my page',
            'image' => 'image123',
            'twitter_card' => 'summary',
            'no_index' => false,
        ], $seo->jsonSerialize());
    }

    #[Group('unit')]
    public function testSetSEOMethodChaining() {
        $seo = SEO::init();
        $result = $seo->setSEO('Title', 'Description', 'image123', 'summary', true);
        
        $this->assertInstanceOf(SEO::class, $result);
        $this->assertSame($seo, $result);
    }

    #[Group('unit')]
    public function testSetMethodReturnsStatic() {
        $seo = SEO::init();
        $result = $seo->set([
            'title' => 'Title',
            'description' => 'Description',
            'image' => 'image123',
            'twitter_card' => 'summary',
            'no_index' => false,
        ]);
        
        $this->assertInstanceOf(SEO::class, $result);
        $this->assertSame($seo, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithValidSEO() {
        $seo = SEO::init();
        $seo->set([
            'title' => 'Default Title',
            'description' => 'Default Description',
            'image' => 'default123',
            'twitter_card' => 'summary',
            'no_index' => false,
        ]);
        $seo->addLocale('en', [
            'title' => 'English Title',
            'description' => 'English Description',
            'image' => 'en123',
            'twitter_card' => 'summary_large_image',
            'no_index' => true,
        ]);
        $seo->addLocale('es', [
            'title' => 'Spanish Title',
            'description' => 'Spanish Description',
            'image' => 'es123',
            'twitter_card' => 'summary',
            'no_index' => false,
        ]);
        
        $result = $seo->jsonSerialize();
        
        $this->assertIsArray($result);
        $this->assertEquals('English Title', $result['en']['title']);
        $this->assertEquals('Spanish Title', $result['es']['title']);
    }

    #[Group('unit')]
    public function testAddLocaleReturnsStatic() {
        $seo = SEO::init();
        $result = $seo->addLocale('en', [
            'title' => 'Title',
            'description' => 'Description',
            'image' => 'image123',
            'twitter_card' => 'summary',
            'no_index' => false,
        ]);
        
        $this->assertInstanceOf(SEO::class, $result);
        $this->assertSame($seo, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithInvalidSEO() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in expected format');
        
        $seo = SEO::init();
        $seo->addLocale('en', 'invalid');
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsNullWhenEmpty() {
        $seo = SEO::init();
        
        $this->assertNull($seo->jsonSerialize());
    }

    #[Group('unit')]
    public function testJsonSerializePrioritizesLocalizedValues() {
        $seo = SEO::init();
        $seo->set([
            'title' => 'Default',
            'description' => 'Default Description',
            'image' => 'default123',
            'twitter_card' => 'summary',
            'no_index' => false,
        ]);
        $seo->addLocale('en', [
            'title' => 'English',
            'description' => 'English Description',
            'image' => 'en123',
            'twitter_card' => 'summary_large_image',
            'no_index' => true,
        ]);
        
        $result = $seo->jsonSerialize();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('en', $result);
        $this->assertEquals('English', $result['en']['title']);
    }

    #[Group('unit')]
    public function testNullValueIsValid() {
        $seo = SEO::init();
        $seo->set(null);
        
        $this->assertNull($seo->jsonSerialize());
    }

    public static function validSEOProvider(): array {
        return [
            'all fields with summary' => [
                [
                    'title' => 'My Page Title',
                    'description' => 'This is a description of my page',
                    'image' => 'image123',
                    'twitter_card' => 'summary',
                    'no_index' => false,
                ],
                [
                    'title' => 'My Page Title',
                    'description' => 'This is a description of my page',
                    'image' => 'image123',
                    'twitter_card' => 'summary',
                    'no_index' => false,
                ],
            ],
            'all fields with summary_large_image' => [
                [
                    'title' => 'Large Image Title',
                    'description' => 'Description for large image',
                    'image' => 'large_image_123',
                    'twitter_card' => 'summary_large_image',
                    'no_index' => true,
                ],
                [
                    'title' => 'Large Image Title',
                    'description' => 'Description for large image',
                    'image' => 'large_image_123',
                    'twitter_card' => 'summary_large_image',
                    'no_index' => true,
                ],
            ],
            'no_index false' => [
                [
                    'title' => 'Indexed Page',
                    'description' => 'This page should be indexed',
                    'image' => 'indexed123',
                    'twitter_card' => 'summary',
                    'no_index' => false,
                ],
                [
                    'title' => 'Indexed Page',
                    'description' => 'This page should be indexed',
                    'image' => 'indexed123',
                    'twitter_card' => 'summary',
                    'no_index' => false,
                ],
            ],
            'no_index true' => [
                [
                    'title' => 'Hidden Page',
                    'description' => 'This page should not be indexed',
                    'image' => 'hidden123',
                    'twitter_card' => 'summary',
                    'no_index' => true,
                ],
                [
                    'title' => 'Hidden Page',
                    'description' => 'This page should not be indexed',
                    'image' => 'hidden123',
                    'twitter_card' => 'summary',
                    'no_index' => true,
                ],
            ],
        ];
    }

    public static function invalidSEOProvider(): array {
        return [
            'missing title' => [
                [
                    'description' => 'Description',
                    'image' => 'image123',
                    'twitter_card' => 'summary',
                    'no_index' => false,
                ],
                'Value not in expected format',
            ],
            'missing description' => [
                [
                    'title' => 'Title',
                    'image' => 'image123',
                    'twitter_card' => 'summary',
                    'no_index' => false,
                ],
                'Value not in expected format',
            ],
            'missing image' => [
                [
                    'title' => 'Title',
                    'description' => 'Description',
                    'twitter_card' => 'summary',
                    'no_index' => false,
                ],
                'Value not in expected format',
            ],
            'missing twitter_card' => [
                [
                    'title' => 'Title',
                    'description' => 'Description',
                    'image' => 'image123',
                    'no_index' => false,
                ],
                'Value not in expected format',
            ],
            'missing no_index' => [
                [
                    'title' => 'Title',
                    'description' => 'Description',
                    'image' => 'image123',
                    'twitter_card' => 'summary',
                ],
                'Value not in expected format',
            ],
            'invalid twitter_card' => [
                [
                    'title' => 'Title',
                    'description' => 'Description',
                    'image' => 'image123',
                    'twitter_card' => 'player',
                    'no_index' => false,
                ],
                'twitter_card must be "summary" or "summary_large_image"',
            ],
            'no_index not boolean (string)' => [
                [
                    'title' => 'Title',
                    'description' => 'Description',
                    'image' => 'image123',
                    'twitter_card' => 'summary',
                    'no_index' => 'false',
                ],
                'no_index must be boolean',
            ],
            'no_index not boolean (integer)' => [
                [
                    'title' => 'Title',
                    'description' => 'Description',
                    'image' => 'image123',
                    'twitter_card' => 'summary',
                    'no_index' => 0,
                ],
                'no_index must be boolean',
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
