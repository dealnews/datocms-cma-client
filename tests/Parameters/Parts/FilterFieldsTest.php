<?php

namespace DealNews\DatoCMS\CMA\Tests\Parameters\Parts;

use DealNews\DatoCMS\CMA\Parameters\Parts\FilterFields;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Parameters\Parts\FilterFields class
 */
class FilterFieldsTest extends TestCase {

    // =========================================================================
    // toArray() tests
    // =========================================================================

    #[Group('unit')]
    public function testToArrayWithNoFields() {
        $fields = new FilterFields();

        $this->assertEquals([], $fields->toArray());
    }

    #[Group('unit')]
    public function testToArrayWithDataOverride() {
        $fields = new FilterFields();
        $fields->addField('width', 1000, 'gte');

        $override = ['foo' => ['eq' => 'bar']];

        $this->assertEquals($override, $fields->toArray($override));
    }

    // =========================================================================
    // addField() chaining
    // =========================================================================

    #[Group('unit')]
    public function testAddFieldReturnsSelfForChaining() {
        $fields = new FilterFields();

        $result = $fields->addField('width', 1000, 'gte');

        $this->assertSame($fields, $result);
    }

    // =========================================================================
    // Non-matches operators - value stored as-is
    // =========================================================================

    #[Group('unit')]
    #[DataProvider('nonMatchesOperatorProvider')]
    public function testAddFieldWithNonMatchesOperator(string $operator, mixed $value) {
        $fields = new FilterFields();
        $fields->addField('some_field', $value, $operator);

        $this->assertEquals(
            ['some_field' => [$operator => $value]],
            $fields->toArray()
        );
    }

    public static function nonMatchesOperatorProvider(): array {
        return [
            'eq (default)' => ['eq', 'published'],
            'neq'          => ['neq', 'draft'],
            'gt'           => ['gt', '2025-01-01'],
            'gte'          => ['gte', 1000],
            'lt'           => ['lt', 5000000],
            'lte'          => ['lte', 500],
            'exists'       => ['exists', true],
            'in'           => ['in', ['a', 'b']],
            'not_in'       => ['not_in', ['c', 'd']],
        ];
    }

    #[Group('unit')]
    public function testAddFieldDefaultsToEqOperator() {
        $fields = new FilterFields();
        $fields->addField('status', 'published');

        $this->assertEquals(
            ['status' => ['eq' => 'published']],
            $fields->toArray()
        );
    }

    // =========================================================================
    // matches operator - value wrapped as a pattern object
    // =========================================================================

    #[Group('unit')]
    public function testAddFieldWithMatchesWrapsValueAsPattern() {
        $fields = new FilterFields();
        $fields->addField('title', 'Hello', 'matches');

        $this->assertEquals(
            ['title' => ['matches' => ['pattern' => 'Hello']]],
            $fields->toArray()
        );
    }

    #[Group('unit')]
    public function testAddFieldWithMatchesAndNoCaseSensitiveOmitsKey() {
        $fields = new FilterFields();
        $fields->addField('title', 'Hello', 'matches');

        $matches = $fields->toArray()['title']['matches'];

        $this->assertArrayNotHasKey('case_sensitive', $matches);
    }

    #[Group('unit')]
    public function testAddFieldWithMatchesCaseSensitiveTrue() {
        $fields = new FilterFields();
        $fields->addField('title', 'Hello', 'matches', true);

        $this->assertEquals(
            ['title' => ['matches' => ['pattern' => 'Hello', 'case_sensitive' => true]]],
            $fields->toArray()
        );
    }

    #[Group('unit')]
    public function testAddFieldWithMatchesCaseSensitiveFalse() {
        $fields = new FilterFields();
        $fields->addField('title', 'Hello', 'matches', false);

        $this->assertEquals(
            ['title' => ['matches' => ['pattern' => 'Hello', 'case_sensitive' => false]]],
            $fields->toArray()
        );
    }

    // =========================================================================
    // Multiple fields / operators
    // =========================================================================

    #[Group('unit')]
    public function testAddFieldWithMultipleOperatorsOnSameField() {
        $fields = new FilterFields();
        $fields->addField('width', 500, 'gte');
        $fields->addField('width', 2000, 'lte');

        $this->assertEquals(
            ['width' => ['gte' => 500, 'lte' => 2000]],
            $fields->toArray()
        );
    }

    #[Group('unit')]
    public function testAddFieldWithMultipleFields() {
        $fields = new FilterFields();
        $fields->addField('width', 1000, 'gte');
        $fields->addField('title', 'Hello', 'matches');
        $fields->addField('created_at', '2025-01-01', 'gt');

        $this->assertEquals(
            [
                'width'      => ['gte' => 1000],
                'title'      => ['matches' => ['pattern' => 'Hello']],
                'created_at' => ['gt' => '2025-01-01'],
            ],
            $fields->toArray()
        );
    }
}
