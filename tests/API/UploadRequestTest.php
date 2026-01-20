<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\UploadRequest;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\UploadRequest class
 */
class UploadRequestTest extends TestCase {

    /**
     * Creates an UploadRequest API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return UploadRequest
     */
    protected function createUploadRequestWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): UploadRequest {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new UploadRequest($mock_handler);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreate() {
        $expected_data = [
            'data' => [
                'type'       => 'upload_request',
                'attributes' => [
                    'filename' => 'image.jpg',
                ],
            ],
        ];
        $expected_response = [
            'data' => [
                'id'         => 'request-123',
                'type'       => 'upload_request',
                'attributes' => [
                    'url'             => 'https://s3.amazonaws.com/...',
                    'request_headers' => [
                        'Content-Type' => 'image/jpeg',
                    ],
                ],
            ],
        ];
        $upload_request = $this->createUploadRequestWithMock(
            'POST',
            '/upload-requests',
            [],
            $expected_data,
            $expected_response
        );

        $result = $upload_request->create('image.jpg');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithDifferentFilename() {
        $expected_data = [
            'data' => [
                'type'       => 'upload_request',
                'attributes' => [
                    'filename' => 'document.pdf',
                ],
            ],
        ];
        $expected_response = [
            'data' => [
                'id'   => 'request-456',
                'type' => 'upload_request',
            ],
        ];
        $upload_request = $this->createUploadRequestWithMock(
            'POST',
            '/upload-requests',
            [],
            $expected_data,
            $expected_response
        );

        $result = $upload_request->create('document.pdf');

        $this->assertEquals($expected_response, $result);
    }
}
