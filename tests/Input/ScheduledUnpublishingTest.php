<?php

namespace DealNews\DatoCMS\CMA\Tests\Input;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Input\ScheduledUnpublishing;

class ScheduledUnpublishingTest extends TestCase {

    #[Group('unit')]
    public function testDefaultTypeIsScheduledUnpublishing() {
        $scheduled_unpublishing = new ScheduledUnpublishing();

        $this->assertEquals('scheduled_unpublishing', $scheduled_unpublishing->type);
    }

    #[Group('unit')]
    public function testSettingUnpublishingScheduledAt() {
        $scheduled_unpublishing = new ScheduledUnpublishing();
        $scheduled_unpublishing->attributes['unpublishing_scheduled_at'] = '2030-09-01T12:00:00Z';

        $this->assertEquals('2030-09-01T12:00:00Z', $scheduled_unpublishing->attributes['unpublishing_scheduled_at']);
    }

    #[Group('unit')]
    public function testSettingContentInLocales() {
        $scheduled_unpublishing = new ScheduledUnpublishing();
        $scheduled_unpublishing->attributes['content_in_locales'] = ['en', 'it', 'fr'];

        $this->assertEquals(['en', 'it', 'fr'], $scheduled_unpublishing->attributes['content_in_locales']);
    }

    #[Group('unit')]
    public function testToArrayTypeProperlySet() {
        $scheduled_unpublishing = new ScheduledUnpublishing();
        $scheduled_unpublishing->attributes['unpublishing_scheduled_at'] = '2030-09-01T12:00:00Z';

        $array = $scheduled_unpublishing->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertEquals('scheduled_unpublishing', $array['type']);
    }

    #[Group('unit')]
    public function testToArrayRemovesEmptyContentInLocales() {
        $scheduled_unpublishing = new ScheduledUnpublishing();
        $scheduled_unpublishing->attributes['unpublishing_scheduled_at'] = '2030-09-01T12:00:00Z';
        $scheduled_unpublishing->attributes['content_in_locales'] = [];

        $array = $scheduled_unpublishing->toArray();

        $this->assertArrayNotHasKey('content_in_locales', $array['attributes']);
    }

    #[Group('unit')]
    public function testToArrayPreservesContentInLocales() {
        $scheduled_unpublishing = new ScheduledUnpublishing();
        $scheduled_unpublishing->attributes['unpublishing_scheduled_at'] = '2030-09-01T12:00:00Z';
        $scheduled_unpublishing->attributes['content_in_locales'] = ['en', 'es'];

        $array = $scheduled_unpublishing->toArray();

        $this->assertArrayHasKey('content_in_locales', $array['attributes']);
        $this->assertEquals(['en', 'es'], $array['attributes']['content_in_locales']);
    }

    #[Group('unit')]
    public function testToArrayIncludesTypeAndAttributes() {
        $scheduled_unpublishing = new ScheduledUnpublishing();
        $scheduled_unpublishing->attributes['unpublishing_scheduled_at'] = '2030-09-01T12:00:00Z';

        $array = $scheduled_unpublishing->toArray();

        $this->assertEquals('scheduled_unpublishing', $array['type']);
        $this->assertArrayHasKey('attributes', $array);
        $this->assertEquals('2030-09-01T12:00:00Z', $array['attributes']['unpublishing_scheduled_at']);
    }

    #[Group('unit')]
    public function testFullScheduledUnpublishingWithAllFields() {
        $scheduled_unpublishing = new ScheduledUnpublishing();
        $scheduled_unpublishing->attributes['unpublishing_scheduled_at'] = '2025-12-31T23:59:59Z';
        $scheduled_unpublishing->attributes['content_in_locales'] = ['en', 'it', 'de'];

        $array = $scheduled_unpublishing->toArray();

        $this->assertEquals([
            'type' => 'scheduled_unpublishing',
            'attributes' => [
                'unpublishing_scheduled_at' => '2025-12-31T23:59:59Z',
                'content_in_locales' => ['en', 'it', 'de'],
            ],
        ], $array);
    }

    #[Group('unit')]
    public function testToArrayWithOnlyRequiredField() {
        $scheduled_unpublishing = new ScheduledUnpublishing();
        $scheduled_unpublishing->attributes['unpublishing_scheduled_at'] = '2030-01-01T00:00:00Z';

        $array = $scheduled_unpublishing->toArray();

        $this->assertEquals([
            'type' => 'scheduled_unpublishing',
            'attributes' => [
                'unpublishing_scheduled_at' => '2030-01-01T00:00:00Z',
            ],
        ], $array);
    }
}
