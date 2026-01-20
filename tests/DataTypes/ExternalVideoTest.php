<?php

namespace DealNews\DatoCMS\CMA\Tests\DataTypes;

use DealNews\DatoCMS\CMA\DataTypes\ExternalVideo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class ExternalVideoTest extends TestCase {

    #[Group('unit')]
    #[DataProvider('validExternalVideoProvider')]
    public function testValidExternalVideoValues(array $value, array $expected) {
        $video = ExternalVideo::init();
        $video->set($value);

        $this->assertEquals($expected, $video->jsonSerialize());
    }

    #[Group('unit')]
    #[DataProvider('invalidExternalVideoProvider')]
    public function testInvalidExternalVideoValues(mixed $value, string $expectedMessage) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $video = ExternalVideo::init();
        $video->set($value);
    }

    #[Group('unit')]
    public function testSetExternalVideoHelperMethod() {
        $video  = ExternalVideo::init();
        $result = $video->setExternalVideo(
            'youtube',
            'dQw4w9WgXcQ',
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            1280,
            720,
            'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
            'Sample Video'
        );

        $this->assertInstanceOf(ExternalVideo::class, $result);
        $this->assertEquals([
            'provider'      => 'youtube',
            'provider_uid'  => 'dQw4w9WgXcQ',
            'url'           => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'width'         => 1280,
            'height'        => 720,
            'thumbnail_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
            'title'         => 'Sample Video',
        ], $video->jsonSerialize());
    }

    #[Group('unit')]
    public function testSetExternalVideoMethodChaining() {
        $video  = ExternalVideo::init();
        $result = $video->setExternalVideo('youtube', 'abc123', 'https://youtube.com/watch?v=abc123', 640, 480, 'https://img.youtube.com/vi/abc123/default.jpg', 'Test');

        $this->assertInstanceOf(ExternalVideo::class, $result);
        $this->assertSame($video, $result);
    }

    #[Group('unit')]
    public function testSetMethodReturnsStatic() {
        $video  = ExternalVideo::init();
        $result = $video->set([
            'provider'      => 'youtube',
            'provider_uid'  => 'abc123',
            'url'           => 'https://youtube.com/watch?v=abc123',
            'width'         => 640,
            'height'        => 480,
            'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
            'title'         => 'Test',
        ]);

        $this->assertInstanceOf(ExternalVideo::class, $result);
        $this->assertSame($video, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithValidExternalVideo() {
        $video = ExternalVideo::init();
        $video->set([
            'provider'      => 'youtube',
            'provider_uid'  => 'default123',
            'url'           => 'https://youtube.com/watch?v=default123',
            'width'         => 640,
            'height'        => 480,
            'thumbnail_url' => 'https://img.youtube.com/vi/default123/default.jpg',
            'title'         => 'Default Title',
        ]);
        $video->addLocale('en', [
            'provider'      => 'youtube',
            'provider_uid'  => 'en123',
            'url'           => 'https://youtube.com/watch?v=en123',
            'width'         => 1280,
            'height'        => 720,
            'thumbnail_url' => 'https://img.youtube.com/vi/en123/maxresdefault.jpg',
            'title'         => 'English Title',
        ]);

        $result = $video->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertEquals('English Title', $result['en']['title']);
    }

    #[Group('unit')]
    public function testAddLocaleReturnsStatic() {
        $video  = ExternalVideo::init();
        $result = $video->addLocale('en', [
            'provider'      => 'youtube',
            'provider_uid'  => 'abc123',
            'url'           => 'https://youtube.com/watch?v=abc123',
            'width'         => 640,
            'height'        => 480,
            'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
            'title'         => 'Test',
        ]);

        $this->assertInstanceOf(ExternalVideo::class, $result);
        $this->assertSame($video, $result);
    }

    #[Group('unit')]
    public function testAddLocaleWithInvalidExternalVideo() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value not in expected format');

        $video = ExternalVideo::init();
        $video->addLocale('en', 'invalid');
    }

    #[Group('unit')]
    public function testJsonSerializeReturnsNullWhenEmpty() {
        $video = ExternalVideo::init();

        $this->assertNull($video->jsonSerialize());
    }

    #[Group('unit')]
    public function testJsonSerializePrioritizesLocalizedValues() {
        $video = ExternalVideo::init();
        $video->set([
            'provider'      => 'youtube',
            'provider_uid'  => 'default123',
            'url'           => 'https://youtube.com/watch?v=default123',
            'width'         => 640,
            'height'        => 480,
            'thumbnail_url' => 'https://img.youtube.com/vi/default123/default.jpg',
            'title'         => 'Default',
        ]);
        $video->addLocale('en', [
            'provider'      => 'vimeo',
            'provider_uid'  => 'en123',
            'url'           => 'https://vimeo.com/en123',
            'width'         => 1280,
            'height'        => 720,
            'thumbnail_url' => 'https://i.vimeocdn.com/video/en123_640.jpg',
            'title'         => 'English',
        ]);

        $result = $video->jsonSerialize();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('en', $result);
        $this->assertEquals('English', $result['en']['title']);
    }

    #[Group('unit')]
    public function testNullValueIsValid() {
        $video = ExternalVideo::init();
        $video->set(null);

        $this->assertNull($video->jsonSerialize());
    }

    public static function validExternalVideoProvider(): array {
        return [
            'youtube video' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'dQw4w9WgXcQ',
                    'url'           => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'width'         => 1280,
                    'height'        => 720,
                    'thumbnail_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                    'title'         => 'Sample Video',
                ],
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'dQw4w9WgXcQ',
                    'url'           => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'width'         => 1280,
                    'height'        => 720,
                    'thumbnail_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                    'title'         => 'Sample Video',
                ],
            ],
            'vimeo video' => [
                [
                    'provider'      => 'vimeo',
                    'provider_uid'  => '123456789',
                    'url'           => 'https://vimeo.com/123456789',
                    'width'         => 1920,
                    'height'        => 1080,
                    'thumbnail_url' => 'https://i.vimeocdn.com/video/123456789_640.jpg',
                    'title'         => 'Vimeo Video',
                ],
                [
                    'provider'      => 'vimeo',
                    'provider_uid'  => '123456789',
                    'url'           => 'https://vimeo.com/123456789',
                    'width'         => 1920,
                    'height'        => 1080,
                    'thumbnail_url' => 'https://i.vimeocdn.com/video/123456789_640.jpg',
                    'title'         => 'Vimeo Video',
                ],
            ],
            'facebook video' => [
                [
                    'provider'      => 'facebook',
                    'provider_uid'  => '987654321',
                    'url'           => 'https://www.facebook.com/watch?v=987654321',
                    'width'         => 640,
                    'height'        => 360,
                    'thumbnail_url' => 'https://scontent.xx.fbcdn.net/v/t1.0-9/987654321.jpg',
                    'title'         => 'Facebook Video',
                ],
                [
                    'provider'      => 'facebook',
                    'provider_uid'  => '987654321',
                    'url'           => 'https://www.facebook.com/watch?v=987654321',
                    'width'         => 640,
                    'height'        => 360,
                    'thumbnail_url' => 'https://scontent.xx.fbcdn.net/v/t1.0-9/987654321.jpg',
                    'title'         => 'Facebook Video',
                ],
            ],
        ];
    }

    public static function invalidExternalVideoProvider(): array {
        return [
            'missing provider' => [
                [
                    'provider_uid'  => 'abc123',
                    'url'           => 'https://youtube.com/watch?v=abc123',
                    'width'         => 640,
                    'height'        => 480,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'Value not in expected format',
            ],
            'missing provider_uid' => [
                [
                    'provider'      => 'youtube',
                    'url'           => 'https://youtube.com/watch?v=abc123',
                    'width'         => 640,
                    'height'        => 480,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'Value not in expected format',
            ],
            'missing url' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'abc123',
                    'width'         => 640,
                    'height'        => 480,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'Value not in expected format',
            ],
            'missing width' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'abc123',
                    'url'           => 'https://youtube.com/watch?v=abc123',
                    'height'        => 480,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'Value not in expected format',
            ],
            'missing height' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'abc123',
                    'url'           => 'https://youtube.com/watch?v=abc123',
                    'width'         => 640,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'Value not in expected format',
            ],
            'missing thumbnail_url' => [
                [
                    'provider'     => 'youtube',
                    'provider_uid' => 'abc123',
                    'url'          => 'https://youtube.com/watch?v=abc123',
                    'width'        => 640,
                    'height'       => 480,
                    'title'        => 'Test',
                ],
                'Value not in expected format',
            ],
            'missing title' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'abc123',
                    'url'           => 'https://youtube.com/watch?v=abc123',
                    'width'         => 640,
                    'height'        => 480,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                ],
                'Value not in expected format',
            ],
            'invalid provider' => [
                [
                    'provider'      => 'dailymotion',
                    'provider_uid'  => 'abc123',
                    'url'           => 'https://dailymotion.com/video/abc123',
                    'width'         => 640,
                    'height'        => 480,
                    'thumbnail_url' => 'https://dailymotion.com/thumbnail/abc123',
                    'title'         => 'Test',
                ],
                'provider must be "youtube" or "vimeo" or "facebook"',
            ],
            'width not integer (float)' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'abc123',
                    'url'           => 'https://youtube.com/watch?v=abc123',
                    'width'         => 640.5,
                    'height'        => 480,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'width must be an integer',
            ],
            'height not integer (float)' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'abc123',
                    'url'           => 'https://youtube.com/watch?v=abc123',
                    'width'         => 640,
                    'height'        => 480.5,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'height must be an integer',
            ],
            'provider_uid not string' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 123,
                    'url'           => 'https://youtube.com/watch?v=abc123',
                    'width'         => 640,
                    'height'        => 480,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'provider_uid must be a string',
            ],
            'url not string' => [
                [
                    'provider'      => 'youtube',
                    'provider_uid'  => 'abc123',
                    'url'           => ['not', 'string'],
                    'width'         => 640,
                    'height'        => 480,
                    'thumbnail_url' => 'https://img.youtube.com/vi/abc123/default.jpg',
                    'title'         => 'Test',
                ],
                'url must be a string',
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
