<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Upload;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Parts\Upload\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Upload\DefaultFieldMetadata;

/**
 * Tests for the Input\Parts\Upload\Attributes class
 */
class AttributesTest extends TestCase {

    // =========================================================================
    // Constructor tests
    // =========================================================================

    #[Group('unit')]
    public function testConstructorInitializesDefaultFieldMetadata() {
        $attributes = new Attributes();

        $this->assertInstanceOf(DefaultFieldMetadata::class, $attributes->default_field_metadata);
    }

    #[Group('unit')]
    public function testConstructorSetsNullDefaults() {
        $attributes = new Attributes();

        $this->assertNull($attributes->path);
        $this->assertNull($attributes->copyright);
        $this->assertNull($attributes->author);
        $this->assertNull($attributes->notes);
        $this->assertEquals([], $attributes->tags);
    }

    // =========================================================================
    // Property assignment tests
    // =========================================================================

    #[Group('unit')]
    public function testPathCanBeSet() {
        $attributes = new Attributes();

        $attributes->path = '/45/1496845848-image.jpg';

        $this->assertEquals('/45/1496845848-image.jpg', $attributes->path);
    }

    #[Group('unit')]
    public function testCopyrightCanBeSet() {
        $attributes = new Attributes();

        $attributes->copyright = '© 2025 DealNews';

        $this->assertEquals('© 2025 DealNews', $attributes->copyright);
    }

    #[Group('unit')]
    public function testAuthorCanBeSet() {
        $attributes = new Attributes();

        $attributes->author = 'John Doe';

        $this->assertEquals('John Doe', $attributes->author);
    }

    #[Group('unit')]
    public function testNotesCanBeSet() {
        $attributes = new Attributes();

        $attributes->notes = 'Internal notes about this upload';

        $this->assertEquals('Internal notes about this upload', $attributes->notes);
    }

    #[Group('unit')]
    public function testTagsCanBeSet() {
        $attributes = new Attributes();

        $attributes->tags = ['banner', 'hero', 'homepage'];

        $this->assertEquals(['banner', 'hero', 'homepage'], $attributes->tags);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithEmptyAttributesReturnsEmpty() {
        $attributes = new Attributes();

        $array = $attributes->toArray();

        $this->assertEquals([], $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesPath() {
        $attributes = new Attributes();
        $attributes->path = '/45/1496845848-image.jpg';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('path', $array);
        $this->assertEquals('/45/1496845848-image.jpg', $array['path']);
    }

    #[Group('unit')]
    public function testToArrayIncludesCopyright() {
        $attributes = new Attributes();
        $attributes->copyright = '© 2025';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('copyright', $array);
        $this->assertEquals('© 2025', $array['copyright']);
    }

    #[Group('unit')]
    public function testToArrayIncludesAuthor() {
        $attributes = new Attributes();
        $attributes->author = 'Jane Doe';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('author', $array);
        $this->assertEquals('Jane Doe', $array['author']);
    }

    #[Group('unit')]
    public function testToArrayIncludesNotes() {
        $attributes = new Attributes();
        $attributes->notes = 'Some notes';

        $array = $attributes->toArray();

        $this->assertArrayHasKey('notes', $array);
        $this->assertEquals('Some notes', $array['notes']);
    }

    #[Group('unit')]
    public function testToArrayIncludesTags() {
        $attributes = new Attributes();
        $attributes->tags = ['tag1', 'tag2'];

        $array = $attributes->toArray();

        $this->assertArrayHasKey('tags', $array);
        $this->assertEquals(['tag1', 'tag2'], $array['tags']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyTags() {
        $attributes = new Attributes();
        $attributes->path = '/path/to/file.jpg';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('tags', $array);
    }

    #[Group('unit')]
    public function testToArrayIncludesDefaultFieldMetadata() {
        $attributes = new Attributes();
        $attributes->default_field_metadata->addLocale('en', 'Alt text', 'Title');

        $array = $attributes->toArray();

        $this->assertArrayHasKey('default_field_metadata', $array);
        $this->assertEquals('Alt text', $array['default_field_metadata']['en']['alt']);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyDefaultFieldMetadata() {
        $attributes = new Attributes();
        $attributes->path = '/path/to/file.jpg';

        $array = $attributes->toArray();

        $this->assertArrayNotHasKey('default_field_metadata', $array);
    }

    #[Group('unit')]
    public function testToArrayWithAllFields() {
        $attributes = new Attributes();
        $attributes->path = '/45/image.jpg';
        $attributes->copyright = '© 2025';
        $attributes->author = 'Author';
        $attributes->notes = 'Notes';
        $attributes->tags = ['tag1'];
        $attributes->default_field_metadata->addLocale('en', 'Alt');

        $array = $attributes->toArray();

        $this->assertArrayHasKey('path', $array);
        $this->assertArrayHasKey('copyright', $array);
        $this->assertArrayHasKey('author', $array);
        $this->assertArrayHasKey('notes', $array);
        $this->assertArrayHasKey('tags', $array);
        $this->assertArrayHasKey('default_field_metadata', $array);
    }
}
