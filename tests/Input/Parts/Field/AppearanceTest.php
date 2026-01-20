<?php

namespace DealNews\DatoCMS\CMA\Tests\Input\Parts\Field;

use DealNews\DatoCMS\CMA\Input\Parts\Field\Appearance;
use DealNews\DatoCMS\CMA\Input\Parts\Field\Appearance\AddOn;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Input\Parts\Field\Appearance class
 */
class AppearanceTest extends TestCase {
    #[Group('unit')]
    public function testDefaultValues() {
        $appearance = new Appearance();

        $this->assertSame('', $appearance->editor);
        $this->assertSame([], $appearance->parameters);
        $this->assertSame([], $appearance->addons);
        $this->assertNull($appearance->field_extension);
    }

    #[Group('unit')]
    public function testEditorCanBeSet() {
        $appearance         = new Appearance();
        $appearance->editor = 'single_line';

        $this->assertSame('single_line', $appearance->editor);
    }

    #[Group('unit')]
    public function testParametersCanBeSet() {
        $appearance             = new Appearance();
        $appearance->parameters = [
            'heading' => true,
            'toolbar' => ['bold', 'italic'],
        ];

        $this->assertSame(['heading' => true, 'toolbar' => ['bold', 'italic']], $appearance->parameters);
    }

    #[Group('unit')]
    public function testAddonsCanBeSet() {
        $appearance = new Appearance();

        $addon1             = new AddOn();
        $addon1->id         = 'addon-1';
        $addon1->parameters = ['key' => 'value'];

        $addon2     = new AddOn();
        $addon2->id = 'addon-2';

        $appearance->addons = [$addon1, $addon2];

        $this->assertCount(2, $appearance->addons);
        $this->assertSame('addon-1', $appearance->addons[0]->id);
        $this->assertSame('addon-2', $appearance->addons[1]->id);
    }

    #[Group('unit')]
    public function testFieldExtensionCanBeSet() {
        $appearance                  = new Appearance();
        $appearance->field_extension = 'extension-id';

        $this->assertSame('extension-id', $appearance->field_extension);
    }

    #[Group('unit')]
    public function testToArrayExcludesNullFieldExtension() {
        $appearance                  = new Appearance();
        $appearance->editor          = 'single_line';
        $appearance->field_extension = null;

        $result = $appearance->toArray();

        $this->assertArrayNotHasKey('field_extension', $result);
    }

    #[Group('unit')]
    public function testToArrayIncludesFieldExtensionWhenSet() {
        $appearance                  = new Appearance();
        $appearance->editor          = 'single_line';
        $appearance->field_extension = 'extension-id';

        $result = $appearance->toArray();

        $this->assertArrayHasKey('field_extension', $result);
        $this->assertSame('extension-id', $result['field_extension']);
    }

    #[Group('unit')]
    public function testFullAppearanceSerialization() {
        $appearance             = new Appearance();
        $appearance->editor     = 'wysiwyg';
        $appearance->parameters = [
            'toolbar'         => ['bold', 'italic', 'link'],
            'start_collapsed' => false,
        ];

        $addon1             = new AddOn();
        $addon1->id         = 'plugin-123';
        $addon1->parameters = ['color' => 'blue'];

        $addon2                  = new AddOn();
        $addon2->id              = 'plugin-456';
        $addon2->field_extension = 'ext-789';

        $appearance->addons          = [$addon1, $addon2];
        $appearance->field_extension = 'main-extension';

        $result = $appearance->toArray();

        $this->assertArrayHasKey('editor', $result);
        $this->assertSame('wysiwyg', $result['editor']);

        $this->assertArrayHasKey('parameters', $result);
        $this->assertSame(['toolbar' => ['bold', 'italic', 'link'], 'start_collapsed' => false], $result['parameters']);

        $this->assertArrayHasKey('addons', $result);
        $this->assertCount(2, $result['addons']);

        $this->assertArrayHasKey('field_extension', $result);
        $this->assertSame('main-extension', $result['field_extension']);
    }
}
