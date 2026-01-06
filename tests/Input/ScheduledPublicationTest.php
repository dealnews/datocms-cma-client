<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\ScheduledPublication;

/**
 * Tests for the Input\ScheduledPublication class
 */
class ScheduledPublicationTest extends TestCase {

    #[Group('unit')]
    public function testDefaultTypeIsScheduledPublication() {
        $scheduled_publication = new ScheduledPublication();
        
        $this->assertEquals('scheduled_publication', $scheduled_publication->type);
    }

    #[Group('unit')]
    public function testCannotChangeTypeFromScheduledPublication() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type must be "scheduled_publication"');
        
        $scheduled_publication = new ScheduledPublication();
        $scheduled_publication->type = 'not_scheduled_publication';
    }

    #[Group('unit')]
    public function testDefaultAttributesIsEmptyArray() {
        $scheduled_publication = new ScheduledPublication();
        
        $this->assertIsArray($scheduled_publication->attributes);
        $this->assertEmpty($scheduled_publication->attributes);
    }

    #[Group('unit')]
    public function testSettingPublicationScheduledAt() {
        $scheduled_publication = new ScheduledPublication();
        $scheduled_publication->attributes['publication_scheduled_at'] = '2030-09-01T12:00:00Z';
        
        $this->assertEquals('2030-09-01T12:00:00Z', $scheduled_publication->attributes['publication_scheduled_at']);
    }

    #[Group('unit')]
    public function testSettingSelectivePublicationContentInLocales() {
        $scheduled_publication = new ScheduledPublication();
        $scheduled_publication->attributes['selective_publication']['content_in_locales'] = ['en', 'es'];
        
        $this->assertEquals(['en', 'es'], $scheduled_publication->attributes['selective_publication']['content_in_locales']);
    }

    #[Group('unit')]
    public function testSettingSelectivePublicationNonLocalizedContent() {
        $scheduled_publication = new ScheduledPublication();
        $scheduled_publication->attributes['selective_publication']['non_localized_content'] = true;
        
        $this->assertTrue($scheduled_publication->attributes['selective_publication']['non_localized_content']);
    }

    #[Group('unit')]
    public function testToArrayWithMinimalData() {
        $scheduled_publication = new ScheduledPublication();
        $scheduled_publication->attributes['publication_scheduled_at'] = '2030-09-01T12:00:00Z';
        
        $result = $scheduled_publication->toArray();
        
        $this->assertEquals('scheduled_publication', $result['type']);
        $this->assertEquals('2030-09-01T12:00:00Z', $result['attributes']['publication_scheduled_at']);
    }

    #[Group('unit')]
    public function testToArrayIncludesTypeAndAttributes() {
        $scheduled_publication = new ScheduledPublication();
        $scheduled_publication->attributes['publication_scheduled_at'] = '2030-09-01T12:00:00Z';
        
        $result = $scheduled_publication->toArray();
        
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('attributes', $result);
    }

    #[Group('unit')]
    public function testToArrayExcludesEmptyNonLocalizedContent() {
        $scheduled_publication = new ScheduledPublication();
        $scheduled_publication->attributes['publication_scheduled_at'] = '2030-09-01T12:00:00Z';
        $scheduled_publication->attributes['non_localized_content'] = null;
        
        $result = $scheduled_publication->toArray();
        
        $this->assertArrayNotHasKey('non_localized_content', $result['attributes']);
    }

    #[Group('unit')]
    public function testToArrayWithFullSelectivePublication() {
        $scheduled_publication = new ScheduledPublication();
        $scheduled_publication->attributes['publication_scheduled_at'] = '2030-09-01T12:00:00Z';
        $scheduled_publication->attributes['selective_publication']['content_in_locales'] = ['en', 'fr'];
        $scheduled_publication->attributes['non_localized_content'] = true;
        
        $result = $scheduled_publication->toArray();
        
        $this->assertEquals('scheduled_publication', $result['type']);
        $this->assertEquals('2030-09-01T12:00:00Z', $result['attributes']['publication_scheduled_at']);
        $this->assertEquals(['en', 'fr'], $result['attributes']['selective_publication']['content_in_locales']);
        $this->assertTrue($result['attributes']['non_localized_content']);
    }
}
