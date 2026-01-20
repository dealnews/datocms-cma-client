<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts;

use DealNews\DatoCMS\CMA\Input\Parts\Meta;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class MetaTest extends TestCase {

    #[Group('unit')]
    public function testDefaultState() {
        $meta = new Meta();

        $this->assertNull($meta->created_at);
        $this->assertFalse($meta->first_published_at);
        $this->assertNull($meta->current_version);
        $this->assertFalse($meta->stage);
    }

    #[Group('unit')]
    #[DataProvider('validMetaValuesProvider')]
    public function testValidMetaValues(array $properties, array $expectedArray) {
        $meta = new Meta();
        foreach ($properties as $key => $value) {
            $meta->$key = $value;
        }

        $this->assertEquals($expectedArray, $meta->toArray());
    }

    #[Group('unit')]
    public function testCreatedAtWithISODateString() {
        $meta             = new Meta();
        $meta->created_at = '2025-12-19T10:30:00Z';

        $array = $meta->toArray();
        $this->assertArrayHasKey('created_at', $array);
        $this->assertEquals('2025-12-19T10:30:00Z', $array['created_at']);
    }

    #[Group('unit')]
    public function testFirstPublishedAtAsNull() {
        $meta                     = new Meta();
        $meta->first_published_at = null;

        $array = $meta->toArray();
        $this->assertArrayHasKey('first_published_at', $array);
        $this->assertNull($array['first_published_at']);
    }

    #[Group('unit')]
    public function testFirstPublishedAtAsFalseExcludedFromArray() {
        $meta                     = new Meta();
        $meta->first_published_at = false;

        $array = $meta->toArray();
        $this->assertArrayNotHasKey('first_published_at', $array);
    }

    #[Group('unit')]
    public function testFirstPublishedAtWithISODateString() {
        $meta                     = new Meta();
        $meta->first_published_at = '2025-12-19T10:30:00Z';

        $array = $meta->toArray();
        $this->assertArrayHasKey('first_published_at', $array);
        $this->assertEquals('2025-12-19T10:30:00Z', $array['first_published_at']);
    }

    #[Group('unit')]
    public function testCurrentVersionWithString() {
        $meta                  = new Meta();
        $meta->current_version = 'v123456';

        $array = $meta->toArray();
        $this->assertArrayHasKey('current_version', $array);
        $this->assertEquals('v123456', $array['current_version']);
    }

    #[Group('unit')]
    public function testEmptyCurrentVersionExcludedFromArray() {
        $meta                  = new Meta();
        $meta->current_version = null;

        $array = $meta->toArray();
        $this->assertArrayNotHasKey('current_version', $array);
    }

    #[Group('unit')]
    public function testStageWithString() {
        $meta        = new Meta();
        $meta->stage = 'published';

        $array = $meta->toArray();
        $this->assertArrayHasKey('stage', $array);
        $this->assertEquals('published', $array['stage']);
    }

    #[Group('unit')]
    public function testStageAsNullIncludedInArray() {
        $meta        = new Meta();
        $meta->stage = null;

        $array = $meta->toArray();
        $this->assertArrayHasKey('stage', $array);
        $this->assertNull($array['stage']);
    }

    #[Group('unit')]
    public function testStageAsFalseExcludedFromArray() {
        $meta        = new Meta();
        $meta->stage = false;

        $array = $meta->toArray();
        $this->assertArrayNotHasKey('stage', $array);
    }

    public static function validMetaValuesProvider(): array {
        return [
            'all properties null (default state)' => [
                'properties' => [
                    'created_at'         => null,
                    'first_published_at' => false,
                    'current_version'    => null,
                    'stage'              => false,
                ],
                'expectedArray' => [],
            ],
            'created_at with ISO 8601 date' => [
                'properties' => [
                    'created_at' => '2025-12-19T10:30:00Z',
                ],
                'expectedArray' => [
                    'created_at' => '2025-12-19T10:30:00Z',
                ],
            ],
            'first_published_at with ISO 8601 date' => [
                'properties' => [
                    'first_published_at' => '2025-12-19T10:30:00Z',
                ],
                'expectedArray' => [
                    'first_published_at' => '2025-12-19T10:30:00Z',
                ],
            ],
            'first_published_at as null (unset value)' => [
                'properties' => [
                    'first_published_at' => null,
                ],
                'expectedArray' => [
                    'first_published_at' => null,
                ],
            ],
            'first_published_at as false (auto value)' => [
                'properties' => [
                    'first_published_at' => false,
                ],
                'expectedArray' => [],
            ],
            'current_version with string' => [
                'properties' => [
                    'current_version' => 'v123456',
                ],
                'expectedArray' => [
                    'current_version' => 'v123456',
                ],
            ],
            'stage with string' => [
                'properties' => [
                    'stage' => 'published',
                ],
                'expectedArray' => [
                    'stage' => 'published',
                ],
            ],
            'stage as null (unset value)' => [
                'properties' => [
                    'stage' => null,
                ],
                'expectedArray' => [
                    'stage' => null,
                ],
            ],
            'stage as false (auto value)' => [
                'properties' => [
                    'stage' => false,
                ],
                'expectedArray' => [],
            ],
            'all properties populated' => [
                'properties' => [
                    'created_at'         => '2025-12-19T10:30:00Z',
                    'first_published_at' => '2025-12-19T11:00:00Z',
                    'current_version'    => 'v123456',
                    'stage'              => 'published',
                ],
                'expectedArray' => [
                    'created_at'         => '2025-12-19T10:30:00Z',
                    'first_published_at' => '2025-12-19T11:00:00Z',
                    'current_version'    => 'v123456',
                    'stage'              => 'published',
                ],
            ],
        ];
    }
}
