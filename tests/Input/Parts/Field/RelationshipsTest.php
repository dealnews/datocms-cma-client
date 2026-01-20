<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Field;

use DealNews\DatoCMS\CMA\Input\Parts\Field\Relationships;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\FieldSet;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Input\Parts\Field\Relationships class
 */
class RelationshipsTest extends TestCase {
    #[Group('unit')]
    public function testDefaultInitializationCreatesFieldSetObject() {
        $relationships = new Relationships();

        $this->assertInstanceOf(FieldSet::class, $relationships->fieldset);
    }

    #[Group('unit')]
    public function testFieldsetCanBeSet() {
        $relationships = new Relationships();

        $fieldset     = new FieldSet();
        $fieldset->id = '123';

        $relationships->fieldset = $fieldset;

        $this->assertSame($fieldset, $relationships->fieldset);
        $this->assertSame('123', $relationships->fieldset->id);
    }

    #[Group('unit')]
    public function testToArraySerialization() {
        $relationships               = new Relationships();
        $relationships->fieldset->id = '456';

        $result = $relationships->toArray();

        $this->assertArrayHasKey('fieldset', $result);
        $this->assertIsArray($result['fieldset']);
    }
}
