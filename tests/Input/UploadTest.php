<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\Upload;
use DealNews\DatoCMS\CMA\Input\Parts\Upload\Attributes;

/**
 * Tests for the Input\Upload class
 */
class UploadTest extends TestCase {

    // =========================================================================
    // Constructor tests
    // =========================================================================

    #[Group('unit')]
    public function testConstructorInitializesAttributes() {
        $upload = new Upload();

        $this->assertInstanceOf(Attributes::class, $upload->attributes);
    }

    #[Group('unit')]
    public function testConstructorInitializesRelationships() {
        $upload = new Upload();

        $this->assertInstanceOf(\DealNews\DatoCMS\CMA\Input\Parts\Upload\Relationships::class, $upload->relationships);
    }

    #[Group('unit')]
    public function testConstructorSetsDefaults() {
        $upload = new Upload();

        $this->assertNull($upload->id);
        $this->assertEquals('upload', $upload->type);
    }

    // =========================================================================
    // Type enforcement tests
    // =========================================================================

    #[Group('unit')]
    public function testTypeCanBeSetToUpload() {
        $upload = new Upload();

        $this->assertEquals('upload', $upload->type);
    }

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithMinimalData() {
        $upload = new Upload();
        $upload->attributes->path = '/45/image.jpg';

        $array = $upload->toArray();

        $this->assertEquals('upload', $array['type']);
        $this->assertArrayHasKey('attributes', $array);
        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayNotHasKey('relationships', $array);
    }

    #[Group('unit')]
    public function testToArrayTypeProperlySet() {
        $upload = new Upload();
        $upload->id = 'upload-123';

        $array = $upload->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertEquals('upload', $array['type']);
    }

    #[Group('unit')]
    public function testToArrayIncludesIdWhenSet() {
        $upload = new Upload();
        $upload->id = 'upload-123';
        $upload->attributes->path = '/45/image.jpg';

        $array = $upload->toArray();

        $this->assertEquals('upload-123', $array['id']);
    }

    #[Group('unit')]
    public function testToArrayIncludesUploadCollectionRelationship() {
        $upload = new Upload();
        $upload->attributes->path = '/45/image.jpg';
        $upload->relationships->upload_collection->id = 'collection-456';

        $array = $upload->toArray();

        $this->assertArrayHasKey('relationships', $array);
        $this->assertArrayHasKey('upload_collection', $array['relationships']);
        $this->assertEquals([
            'data' => [
                'type' => 'upload_collection',
                'id'   => 'collection-456',
            ],
        ], $array['relationships']['upload_collection']);
    }

    #[Group('unit')]
    public function testToArrayWithAllFields() {
        $upload = new Upload();
        $upload->id = 'upload-123';
        $upload->attributes->path = '/45/image.jpg';
        $upload->attributes->author = 'John Doe';
        $upload->attributes->copyright = '© 2025';
        $upload->attributes->notes = 'Some notes';
        $upload->attributes->tags = ['banner'];
        $upload->attributes->default_field_metadata->addLocale('en', 'Alt text', 'Title');
        $upload->relationships->upload_collection->id = 'collection-456';

        $array = $upload->toArray();

        $this->assertEquals('upload-123', $array['id']);
        $this->assertEquals('upload', $array['type']);
        $this->assertEquals('/45/image.jpg', $array['attributes']['path']);
        $this->assertEquals('John Doe', $array['attributes']['author']);
        $this->assertEquals('© 2025', $array['attributes']['copyright']);
        $this->assertEquals('Some notes', $array['attributes']['notes']);
        $this->assertEquals(['banner'], $array['attributes']['tags']);
        $this->assertArrayHasKey('default_field_metadata', $array['attributes']);
        $this->assertArrayHasKey('relationships', $array);
    }

    #[Group('unit')]
    public function testToArrayAttributesSerializesCorrectly() {
        $upload = new Upload();
        $upload->attributes->path = '/path/to/file.jpg';
        $upload->attributes->default_field_metadata->addLocale('en', 'English alt');
        $upload->attributes->default_field_metadata->addLocale('es', 'Spanish alt');

        $array = $upload->toArray();

        $this->assertEquals([
            'en' => ['alt' => 'English alt'],
            'es' => ['alt' => 'Spanish alt'],
        ], $array['attributes']['default_field_metadata']);
    }
}
