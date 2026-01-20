<?php

namespace DealNews\DatoCMS\CMA\Tests\DataTypes;

use DealNews\DatoCMS\CMA\DataTypes\Asset;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class AssetTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('validAssetProvider')]
    public function testValidAssetValues(array $value, array $expected) {
        $asset = Asset::init();
        $asset->set($value);

        $this->assertEquals($expected, $asset->jsonSerialize());
    }

    #[Group('unit')]
    #[DataProvider('invalidAssetProvider')]
    public function testInvalidAssetValues(mixed $value, string $expectedMessage) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $asset = Asset::init();
        $asset->set($value);
    }

    #[Group('unit')]
    public function testSetAssetHelperMethodMinimal() {
        $asset  = Asset::init();
        $result = $asset->setAsset('upload123');

        $this->assertInstanceOf(Asset::class, $result);
        $this->assertEquals([
            'upload_id' => 'upload123',
        ], $asset->jsonSerialize());
    }

    #[Group('unit')]
    public function testSetAssetHelperMethodWithAllFields() {
        $asset  = Asset::init();
        $result = $asset->setAsset(
            'upload123',
            'My Title',
            'Alt text',
            0.5,
            0.75,
            ['key1' => 'value1']
        );

        $this->assertInstanceOf(Asset::class, $result);
        $this->assertEquals([
            'upload_id'   => 'upload123',
            'title'       => 'My Title',
            'alt'         => 'Alt text',
            'focal_point' => [
                'x' => 0.5,
                'y' => 0.75,
            ],
            'custom_data' => ['key1' => 'value1'],
        ], $asset->jsonSerialize());
    }

    #[Group('unit')]
    public function testSetAssetMethodChaining() {
        $asset  = Asset::init();
        $result = $asset->setAsset('upload123');

        $this->assertInstanceOf(Asset::class, $result);
        $this->assertSame($asset, $result);
    }

    #[Group('unit')]
    public function testSetMethodReturnsStatic() {
        $asset  = Asset::init();
        $result = $asset->set(['upload_id' => 'upload123']);

        $this->assertInstanceOf(Asset::class, $result);
        $this->assertSame($asset, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithValidAsset() {
        $asset = Asset::init();
        $asset->set(['upload_id' => 'upload123', 'title' => 'Default']);
        $asset->addLocale('en', ['upload_id' => 'upload456', 'title' => 'English']);
        $asset->addLocale('es', ['upload_id' => 'upload789', 'title' => 'Spanish']);

        $result = $asset->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEquals(['upload_id' => 'upload456', 'title' => 'English'], $result['en']);
        $this->assertEquals(['upload_id' => 'upload789', 'title' => 'Spanish'], $result['es']);
    }

    #[Group('unit')]
    public function testAddLocaleReturnsStatic() {
        $asset  = Asset::init();
        $result = $asset->addLocale('en', ['upload_id' => 'upload123']);

        $this->assertInstanceOf(Asset::class, $result);
        $this->assertSame($asset, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithInvalidAsset() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in expected format');

        $asset = Asset::init();
        $asset->addLocale('en', 'invalid');
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsNullWhenEmpty() {
        $asset = Asset::init();

        $this->assertNull($asset->jsonSerialize());
    }

    #[Group('unit')]
    public function testJsonSerializePrioritizesLocalizedValues() {
        $asset = Asset::init();
        $asset->set(['upload_id' => 'upload123']);
        $asset->addLocale('en', ['upload_id' => 'upload456']);

        $result = $asset->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('en', $result);
        $this->assertEquals(['upload_id' => 'upload456'], $result['en']);
    }

    #[Group('unit')]
    public function testNullValueIsValid() {
        $asset = Asset::init();
        $asset->set(null);

        $this->assertNull($asset->jsonSerialize());
    }

    public static function validAssetProvider(): array {
        return [
            'minimal required' => [
                ['upload_id' => 'upload123'],
                ['upload_id' => 'upload123'],
            ],
            'with title' => [
                ['upload_id' => 'upload123', 'title' => 'My Title'],
                ['upload_id' => 'upload123', 'title' => 'My Title'],
            ],
            'with alt' => [
                ['upload_id' => 'upload123', 'alt' => 'Alt text'],
                ['upload_id' => 'upload123', 'alt' => 'Alt text'],
            ],
            'with focal_point center' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 0.5, 'y' => 0.5]],
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 0.5, 'y' => 0.5]],
            ],
            'with focal_point boundaries (0,0)' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 0.0, 'y' => 0.0]],
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 0.0, 'y' => 0.0]],
            ],
            'with focal_point boundaries (1,1)' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 1.0, 'y' => 1.0]],
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 1.0, 'y' => 1.0]],
            ],
            'with custom_data' => [
                ['upload_id' => 'upload123', 'custom_data' => ['key1' => 'value1', 'key2' => 'value2']],
                ['upload_id' => 'upload123', 'custom_data' => ['key1' => 'value1', 'key2' => 'value2']],
            ],
            'with all fields' => [
                [
                    'upload_id'   => 'upload123',
                    'title'       => 'My Title',
                    'alt'         => 'Alt text',
                    'focal_point' => ['x' => 0.75, 'y' => 0.25],
                    'custom_data' => ['key1' => 'value1'],
                ],
                [
                    'upload_id'   => 'upload123',
                    'title'       => 'My Title',
                    'alt'         => 'Alt text',
                    'focal_point' => ['x' => 0.75, 'y' => 0.25],
                    'custom_data' => ['key1' => 'value1'],
                ],
            ],
        ];
    }

    public static function invalidAssetProvider(): array {
        return [
            'missing upload_id' => [
                ['title' => 'My Title'],
                'Value not in expected format',
            ],
            'upload_id not a string' => [
                ['upload_id' => 123],
                'upload_id is not a string',
            ],
            'title not a string' => [
                ['upload_id' => 'upload123', 'title' => 123],
                'title is not a string',
            ],
            'alt not a string' => [
                ['upload_id' => 'upload123', 'alt' => ['not', 'string']],
                'alt is not a string',
            ],
            'focal_point not an array' => [
                ['upload_id' => 'upload123', 'focal_point' => 'not array'],
                'focal_point not in expected format',
            ],
            'focal_point missing x' => [
                ['upload_id' => 'upload123', 'focal_point' => ['y' => 0.5]],
                'focal_point not in expected format',
            ],
            'focal_point missing y' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 0.5]],
                'focal_point not in expected format',
            ],
            'focal_point x below 0' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => -0.1, 'y' => 0.5]],
                'focal_point not in expected format',
            ],
            'focal_point x above 1' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 1.1, 'y' => 0.5]],
                'focal_point not in expected format',
            ],
            'focal_point y below 0' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 0.5, 'y' => -0.1]],
                'focal_point not in expected format',
            ],
            'focal_point y above 1' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 0.5, 'y' => 1.1]],
                'focal_point not in expected format',
            ],
            'focal_point x not numeric' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 'invalid', 'y' => 0.5]],
                'focal_point not in expected format',
            ],
            'focal_point y not numeric' => [
                ['upload_id' => 'upload123', 'focal_point' => ['x' => 0.5, 'y' => 'invalid']],
                'focal_point not in expected format',
            ],
            'custom_data not an array' => [
                ['upload_id' => 'upload123', 'custom_data' => 'not array'],
                'custom_data not in expected format',
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
