<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\Upload;
use DealNews\DatoCMS\CMA\API\UploadRequest;
use DealNews\DatoCMS\CMA\Exception\S3Upload;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\Upload as UploadInput;
use DealNews\DatoCMS\CMA\Parameters\Upload as UploadParameter;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\Upload class
 */
class UploadTest extends TestCase {

    /**
     * Creates an Upload API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Upload
     */
    protected function createUploadWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Upload {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new Upload($mock_handler);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testListWithoutParameters() {
        $expected_response = [
            'data' => [
                ['id' => 'upload-1', 'type' => 'upload'],
                ['id' => 'upload-2', 'type' => 'upload'],
            ],
        ];
        $upload = $this->createUploadWithMock('GET', '/uploads', [], [], $expected_response);

        $result = $upload->list();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testListWithParameters() {
        $params               = new UploadParameter();
        $params->filter->type = 'image';

        $expected_query    = $params->toArray();
        $expected_response = ['data' => []];
        $upload            = $this->createUploadWithMock('GET', '/uploads', $expected_query, [], $expected_response);

        $result = $upload->list($params);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // listAll() tests
    // =========================================================================

    #[Group('unit')]
    public function testListAllWithNullParameters() {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with(
                'GET',
                '/uploads',
                $this->callback(function ($query) {
                    return isset($query['page']['limit']) &&
                           $query['page']['limit'] === 500;
                }),
                []
            )
            ->willReturn(['data' => [['id' => '1'], ['id' => '2']]]);

        $upload = new Upload($mock_handler);
        $result = $upload->listAll();

        $this->assertEquals(
            ['data' => [['id' => '1'], ['id' => '2']]],
            $result
        );
    }

    #[Group('unit')]
    public function testListAllWithProvidedParameters() {
        $params               = new UploadParameter();
        $params->filter->type = 'image';
        $params->page->limit  = 100;
        $params->page->offset = 50;

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with(
                'GET',
                '/uploads',
                $this->callback(function ($query) {
                    return isset($query['page']['limit'])  &&
                           $query['page']['limit'] === 500 &&
                           isset($query['filter']['type']) &&
                           $query['filter']['type'] === 'image';
                }),
                []
            )
            ->willReturn(['data' => [['id' => '1']]]);

        $upload = new Upload($mock_handler);
        $result = $upload->listAll($params);

        $this->assertEquals(['data' => [['id' => '1']]], $result);
    }

    #[Group('unit')]
    public function testListAllSinglePage() {
        $first_page = array_fill(0, 250, ['id' => 'upload']);

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->willReturn(['data' => $first_page]);

        $upload = new Upload($mock_handler);
        $result = $upload->listAll();

        $this->assertCount(250, $result['data']);
    }

    #[Group('unit')]
    public function testListAllMultiplePages() {
        $first_page  = array_fill(0, 500, ['id' => 'page1']);
        $second_page = array_fill(0, 250, ['id' => 'page2']);

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->exactly(2))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(
                ['data' => $first_page],
                ['data' => $second_page]
            );

        $upload = new Upload($mock_handler);
        $result = $upload->listAll();

        $this->assertCount(750, $result['data']);
    }

    #[Group('unit')]
    public function testListAllExactly500OnLastPage() {
        $first_page  = array_fill(0, 500, ['id' => 'page1']);
        $second_page = array_fill(0, 500, ['id' => 'page2']);
        $third_page  = [];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->exactly(3))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(
                ['data' => $first_page],
                ['data' => $second_page],
                ['data' => $third_page]
            );

        $upload = new Upload($mock_handler);
        $result = $upload->listAll();

        $this->assertCount(1000, $result['data']);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve() {
        $expected_response = [
            'data' => [
                'id'         => 'upload-123',
                'type'       => 'upload',
                'attributes' => ['filename' => 'image.jpg'],
            ],
        ];
        $upload = $this->createUploadWithMock('GET', '/uploads/upload-123', [], [], $expected_response);

        $result = $upload->retrieve('upload-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray() {
        $data = [
            'type'       => 'upload',
            'attributes' => ['path' => '/45/image.jpg'],
        ];
        $expected_response = [
            'data' => [
                'id'   => 'new-upload-id',
                'type' => 'upload',
            ],
        ];
        $upload = $this->createUploadWithMock('POST', '/uploads', [], ['data' => $data], $expected_response);

        $result = $upload->create($data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithInput() {
        $input                   = new UploadInput();
        $input->attributes->path = '/45/image.jpg';

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = [
            'data' => [
                'id'   => 'new-upload-id',
                'type' => 'upload',
            ],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/uploads', [], $expected_data)
            ->willReturn($expected_response);

        $upload = new Upload($mock_handler);
        $result = $upload->create($input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // update() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateWithArray() {
        $data = [
            'type'       => 'upload',
            'attributes' => ['author' => 'Updated Author'],
        ];
        $expected_response = [
            'data' => [
                'id'   => 'upload-123',
                'type' => 'upload',
            ],
        ];
        $upload = $this->createUploadWithMock('PUT', '/uploads/upload-123', [], ['data' => $data], $expected_response);

        $result = $upload->update('upload-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testUpdateWithInput() {
        $input                     = new UploadInput();
        $input->attributes->author = 'New Author';

        $expected_data     = ['data' => $input->toArray()];
        $expected_response = [
            'data' => [
                'id'   => 'upload-456',
                'type' => 'upload',
            ],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('PUT', '/uploads/upload-456', [], $expected_data)
            ->willReturn($expected_response);

        $upload = new Upload($mock_handler);
        $result = $upload->update('upload-456', $input);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete() {
        $expected_response = ['data' => []];
        $upload            = $this->createUploadWithMock('DELETE', '/uploads/upload-to-delete', [], [], $expected_response);

        $result = $upload->delete('upload-to-delete');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // references() tests
    // =========================================================================

    #[Group('unit')]
    public function testReferences() {
        $expected_response = [
            'data' => [
                ['id' => 'record-1', 'type' => 'item'],
                ['id' => 'record-2', 'type' => 'item'],
            ],
        ];
        $upload = $this->createUploadWithMock('GET', '/uploads/upload-123/references', [], [], $expected_response);

        $result = $upload->references('upload-123');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesWithNested() {
        $expected_response = ['data' => []];
        $upload            = $this->createUploadWithMock('GET', '/uploads/upload-123/references', ['nested' => true], [], $expected_response);

        $result = $upload->references('upload-123', true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesWithVersion() {
        $expected_response = ['data' => []];
        $upload            = $this->createUploadWithMock('GET', '/uploads/upload-123/references', ['version' => 'published'], [], $expected_response);

        $result = $upload->references('upload-123', false, 'published');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesWithNestedAndVersion() {
        $expected_response = ['data' => []];
        $upload            = $this->createUploadWithMock('GET', '/uploads/upload-123/references', ['nested' => true, 'version' => 'current'], [], $expected_response);

        $result = $upload->references('upload-123', true, 'current');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesWithPublishedOrCurrent() {
        $expected_response = ['data' => []];
        $upload            = $this->createUploadWithMock('GET', '/uploads/upload-123/references', ['version' => 'published-or-current'], [], $expected_response);

        $result = $upload->references('upload-123', false, 'published-or-current');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testReferencesThrowsExceptionForInvalidVersion() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('version must be "published", "current", or "published-or-current"');

        $upload = new Upload($this->createMock(Handler::class));
        $upload->references('upload-123', false, 'invalid');
    }

    // =========================================================================
    // deleteBulk() tests
    // =========================================================================

    #[Group('unit')]
    public function testDeleteBulk() {
        $expected_data = [
            'data' => [
                'type'          => 'upload_bulk_destroy_operation',
                'relationships' => [
                    'uploads' => [
                        'data' => [
                            ['type' => 'upload', 'id' => 'id-1'],
                            ['type' => 'upload', 'id' => 'id-2'],
                        ],
                    ],
                ],
            ],
        ];
        $expected_response = ['data' => ['id' => 'job-123', 'type' => 'job']];
        $upload            = $this->createUploadWithMock('POST', '/uploads/bulk/destroy', [], $expected_data, $expected_response);

        $result = $upload->deleteBulk(['id-1', 'id-2']);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // updateBulk() tests
    // =========================================================================

    #[Group('unit')]
    public function testUpdateBulk() {
        $attributes    = ['author' => 'Bulk Author', 'copyright' => '© 2025'];
        $expected_data = [
            'data' => [
                'type'          => 'upload_bulk_update_operation',
                'attributes'    => $attributes,
                'relationships' => [
                    'uploads' => [
                        'data' => [
                            ['type' => 'upload', 'id' => 'id-1'],
                            ['type' => 'upload', 'id' => 'id-2'],
                            ['type' => 'upload', 'id' => 'id-3'],
                        ],
                    ],
                ],
            ],
        ];
        $expected_response = ['data' => ['id' => 'job-456', 'type' => 'job']];
        $upload            = $this->createUploadWithMock('PUT', '/uploads/bulk/update', [], $expected_data, $expected_response);

        $result = $upload->updateBulk(['id-1', 'id-2', 'id-3'], $attributes);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // uploadFile() tests
    // =========================================================================

    #[Group('unit')]
    public function testUploadFileThrowsOnNonExistentFile() {
        $mock_handler = $this->createMock(Handler::class);
        $upload       = new Upload($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File does not exist');

        $upload->uploadFile('/nonexistent/path/to/file.jpg');
    }

    #[Group('unit')]
    public function testUploadFileSuccess() {
        // Create a temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'test_upload_');
        file_put_contents($temp_file, 'test file contents');

        try {
            // Mock the upload request
            $mock_upload_request = $this->createMock(UploadRequest::class);
            $mock_upload_request->expects($this->once())
                ->method('create')
                ->with(basename($temp_file))
                ->willReturn([
                    'data' => [
                        'id'         => '/path/to/s3/file',
                        'type'       => 'upload_request',
                        'attributes' => [
                            'url'             => 'https://s3.example.com/bucket/file',
                            'request_headers' => ['Content-Type' => 'application/octet-stream'],
                        ],
                    ],
                ]);

            // Mock the S3 client
            $mock_s3 = new MockHandler([
                new Response(200, [], ''),
            ]);
            $s3_handler_stack = HandlerStack::create($mock_s3);
            $s3_client        = new Client(['handler' => $s3_handler_stack, 'http_errors' => false]);

            // Mock the main handler for create()
            $mock_handler = $this->createMock(Handler::class);
            $mock_handler->expects($this->once())
                ->method('execute')
                ->with('POST', '/uploads', [], $this->anything())
                ->willReturn([
                    'data' => [
                        'id'   => 'created-upload-id',
                        'type' => 'upload',
                    ],
                ]);

            $upload = new Upload($mock_handler, $mock_upload_request, $s3_client);
            $result = $upload->uploadFile($temp_file);

            $this->assertEquals('created-upload-id', $result['data']['id']);
        } finally {
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
        }
    }

    #[Group('unit')]
    public function testUploadFileWithMetadata() {
        // Create a temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'test_upload_');
        file_put_contents($temp_file, 'test file contents');

        try {
            // Mock the upload request
            $mock_upload_request = $this->createMock(UploadRequest::class);
            $mock_upload_request->expects($this->once())
                ->method('create')
                ->willReturn([
                    'data' => [
                        'id'         => '/path/to/s3/file',
                        'type'       => 'upload_request',
                        'attributes' => [
                            'url'             => 'https://s3.example.com/bucket/file',
                            'request_headers' => [],
                        ],
                    ],
                ]);

            // Mock the S3 client
            $mock_s3 = new MockHandler([
                new Response(200, [], ''),
            ]);
            $s3_handler_stack = HandlerStack::create($mock_s3);
            $s3_client        = new Client(['handler' => $s3_handler_stack, 'http_errors' => false]);

            // Mock the main handler and capture the input
            $captured_data = null;
            $mock_handler  = $this->createMock(Handler::class);
            $mock_handler->expects($this->once())
                ->method('execute')
                ->with('POST', '/uploads', [], $this->callback(function ($data) use (&$captured_data) {
                    $captured_data = $data;

                    return true;
                }))
                ->willReturn([
                    'data' => [
                        'id'   => 'created-upload-id',
                        'type' => 'upload',
                    ],
                ]);

            $upload = new Upload($mock_handler, $mock_upload_request, $s3_client);
            $result = $upload->uploadFile($temp_file, [
                'author'    => 'Test Author',
                'copyright' => '© 2025',
                'tags'      => ['test', 'unit'],
            ]);

            // Verify metadata was passed
            $this->assertEquals('Test Author', $captured_data['data']['attributes']['author']);
            $this->assertEquals('© 2025', $captured_data['data']['attributes']['copyright']);
            $this->assertEquals(['test', 'unit'], $captured_data['data']['attributes']['tags']);
        } finally {
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
        }
    }

    #[Group('unit')]
    public function testUploadFileThrowsOnS3Failure() {
        // Create a temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'test_upload_');
        file_put_contents($temp_file, 'test file contents');

        try {
            // Mock the upload request
            $mock_upload_request = $this->createMock(UploadRequest::class);
            $mock_upload_request->expects($this->once())
                ->method('create')
                ->willReturn([
                    'data' => [
                        'id'         => '/path/to/s3/file',
                        'type'       => 'upload_request',
                        'attributes' => [
                            'url'             => 'https://s3.example.com/bucket/file',
                            'request_headers' => [],
                        ],
                    ],
                ]);

            // Mock the S3 client to return error
            $mock_s3 = new MockHandler([
                new Response(403, [], 'Access Denied'),
            ]);
            $s3_handler_stack = HandlerStack::create($mock_s3);
            $s3_client        = new Client(['handler' => $s3_handler_stack, 'http_errors' => false]);

            $mock_handler = $this->createMock(Handler::class);
            $upload       = new Upload($mock_handler, $mock_upload_request, $s3_client);

            $this->expectException(S3Upload::class);
            $this->expectExceptionMessage('S3 upload failed with status 403');

            $upload->uploadFile($temp_file);
        } finally {
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
        }
    }

    // =========================================================================
    // uploadFromUrl() tests
    // =========================================================================

    #[Group('unit')]
    public function testUploadFromUrlSuccess() {
        // Mock the upload request
        $mock_upload_request = $this->createMock(UploadRequest::class);
        $mock_upload_request->expects($this->once())
            ->method('create')
            ->willReturn([
                'data' => [
                    'id'         => '/path/to/s3/file',
                    'type'       => 'upload_request',
                    'attributes' => [
                        'url'             => 'https://s3.example.com/bucket/file',
                        'request_headers' => [],
                    ],
                ],
            ]);

        // Mock the S3/HTTP client for both download and upload
        $mock_s3 = new MockHandler([
            new Response(200, [], 'downloaded file contents'), // Download
            new Response(200, [], ''),                          // S3 upload
        ]);
        $s3_handler_stack = HandlerStack::create($mock_s3);
        $s3_client        = new Client(['handler' => $s3_handler_stack, 'http_errors' => false]);

        // Mock the main handler for create()
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/uploads', [], $this->anything())
            ->willReturn([
                'data' => [
                    'id'   => 'created-upload-id',
                    'type' => 'upload',
                ],
            ]);

        $upload = new Upload($mock_handler, $mock_upload_request, $s3_client);
        $result = $upload->uploadFromUrl('https://example.com/image.jpg');

        $this->assertEquals('created-upload-id', $result['data']['id']);
    }

    #[Group('unit')]
    public function testUploadFromUrlWithCustomFilename() {
        // Mock the upload request - use callback to verify filename ends with custom name
        $mock_upload_request = $this->createMock(UploadRequest::class);
        $mock_upload_request->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($filename) {
                // The temp file includes a prefix, but should contain custom_name.jpg
                return str_contains($filename, 'custom_name.jpg');
            }))
            ->willReturn([
                'data' => [
                    'id'         => '/path/to/s3/file',
                    'type'       => 'upload_request',
                    'attributes' => [
                        'url'             => 'https://s3.example.com/bucket/file',
                        'request_headers' => [],
                    ],
                ],
            ]);

        // Mock the S3/HTTP client
        $mock_s3 = new MockHandler([
            new Response(200, [], 'downloaded file contents'),
            new Response(200, [], ''),
        ]);
        $s3_handler_stack = HandlerStack::create($mock_s3);
        $s3_client        = new Client(['handler' => $s3_handler_stack, 'http_errors' => false]);

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->willReturn(['data' => ['id' => 'upload-id', 'type' => 'upload']]);

        $upload = new Upload($mock_handler, $mock_upload_request, $s3_client);
        $result = $upload->uploadFromUrl('https://example.com/image.jpg', 'custom_name.jpg');

        $this->assertEquals('upload-id', $result['data']['id']);
    }

    #[Group('unit')]
    public function testUploadFromUrlThrowsOnDownloadFailure() {
        // Mock the S3/HTTP client to fail download
        $mock_s3 = new MockHandler([
            new Response(404, [], 'Not Found'),
        ]);
        $s3_handler_stack = HandlerStack::create($mock_s3);
        $s3_client        = new Client(['handler' => $s3_handler_stack, 'http_errors' => false]);

        $mock_handler = $this->createMock(Handler::class);
        $upload       = new Upload($mock_handler, null, $s3_client);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Failed to download file');

        $upload->uploadFromUrl('https://example.com/nonexistent.jpg');
    }

    #[Group('unit')]
    public function testUploadFileWithCollection() {
        // Create a temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'test_upload_');
        file_put_contents($temp_file, 'test file contents');

        try {
            // Mock the upload request
            $mock_upload_request = $this->createMock(UploadRequest::class);
            $mock_upload_request->expects($this->once())
                ->method('create')
                ->willReturn([
                    'data' => [
                        'id'         => '/path/to/s3/file',
                        'type'       => 'upload_request',
                        'attributes' => [
                            'url'             => 'https://s3.example.com/bucket/file',
                            'request_headers' => [],
                        ],
                    ],
                ]);

            // Mock the S3 client
            $mock_s3 = new MockHandler([
                new Response(200, [], ''),
            ]);
            $s3_handler_stack = HandlerStack::create($mock_s3);
            $s3_client        = new Client(['handler' => $s3_handler_stack, 'http_errors' => false]);

            // Capture the data sent to create()
            $captured_data = null;
            $mock_handler  = $this->createMock(Handler::class);
            $mock_handler->expects($this->once())
                ->method('execute')
                ->with('POST', '/uploads', [], $this->callback(function ($data) use (&$captured_data) {
                    $captured_data = $data;

                    return true;
                }))
                ->willReturn(['data' => ['id' => 'upload-id', 'type' => 'upload']]);

            $upload = new Upload($mock_handler, $mock_upload_request, $s3_client);
            $result = $upload->uploadFile($temp_file, null, 'collection-123');

            // Verify collection relationship was set
            $this->assertArrayHasKey('relationships', $captured_data['data']);
            $this->assertEquals('collection-123', $captured_data['data']['relationships']['upload_collection']['data']['id']);
        } finally {
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
        }
    }
}
