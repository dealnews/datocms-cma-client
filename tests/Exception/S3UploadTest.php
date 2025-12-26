<?php

namespace DealNews\DatoCMS\CMA\Tests\Exception;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\Exception\S3Upload;

/**
 * Tests for the Exception\S3Upload class
 */
class S3UploadTest extends TestCase {

    #[Group('unit')]
    public function testConstructorSetsMessage() {
        $exception = new S3Upload('Upload failed');

        $this->assertEquals('Upload failed', $exception->getMessage());
    }

    #[Group('unit')]
    public function testConstructorSetsCode() {
        $exception = new S3Upload('Upload failed', 403);

        $this->assertEquals(403, $exception->getCode());
    }

    #[Group('unit')]
    public function testConstructorSetsPreviousException() {
        $previous = new \Exception('Previous error');
        $exception = new S3Upload('Upload failed', 500, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Group('unit')]
    public function testConstructorSetsResponseBody() {
        $response_body = '<?xml version="1.0" encoding="UTF-8"?><Error><Code>AccessDenied</Code></Error>';
        $exception = new S3Upload('Upload failed', 403, null, $response_body);

        $this->assertEquals($response_body, $exception->getResponseBody());
    }

    #[Group('unit')]
    public function testGetResponseBodyReturnsNullWhenNotSet() {
        $exception = new S3Upload('Upload failed');

        $this->assertNull($exception->getResponseBody());
    }

    #[Group('unit')]
    public function testExtendsRuntimeException() {
        $exception = new S3Upload('Upload failed');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    #[Group('unit')]
    public function testAllParametersSetCorrectly() {
        $previous = new \Exception('Previous');
        $response_body = 'S3 error response';
        $exception = new S3Upload('S3 upload failed', 500, $previous, $response_body);

        $this->assertEquals('S3 upload failed', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertEquals($response_body, $exception->getResponseBody());
    }
}
