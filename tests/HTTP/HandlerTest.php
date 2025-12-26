<?php

namespace DealNews\DatoCMS\CMA\Tests\HTTP;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Exception\API;
use DealNews\DatoCMS\CMA\Exception\Decode;
use DealNews\DatoCMS\CMA\Exception\Unknown;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Tests for the HTTP Handler class
 */
class HandlerTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        Handler::reset();
    }

    protected function tearDown(): void {
        parent::tearDown();
        Handler::reset();
    }

    /**
     * Creates a Handler with a mocked Guzzle client
     *
     * @param array<Response|RequestException> $responses Queue of responses
     *
     * @return Handler
     */
    protected function createHandlerWithMock(array $responses): Handler {
        $mock = new MockHandler($responses);
        $handler_stack = HandlerStack::create($mock);
        // Important: http_errors => false matches the production Handler config
        $client = new Client([
            'handler' => $handler_stack,
            'http_errors' => false,
        ]);

        return new Handler(
            'test-token',
            'test-environment',
            null,
            LogLevel::INFO,
            null,
            $client
        );
    }

    #[Group('unit')]
    public function testExecuteSuccessfulGetRequest() {
        $response_body = ['data' => ['id' => '123', 'type' => 'item']];
        $handler = $this->createHandlerWithMock([
            new Response(200, [], json_encode($response_body)),
        ]);

        $result = $handler->execute('GET', '/items');

        $this->assertEquals($response_body, $result);
    }

    #[Group('unit')]
    public function testExecuteSuccessfulPostRequest() {
        $response_body = ['data' => ['id' => '456', 'type' => 'item']];
        $handler = $this->createHandlerWithMock([
            new Response(201, [], json_encode($response_body)),
        ]);

        $result = $handler->execute('POST', '/items', [], ['data' => ['type' => 'item']]);

        $this->assertEquals($response_body, $result);
    }

    #[Group('unit')]
    public function testExecuteWithQueryParameters() {
        $response_body = ['data' => []];
        $handler = $this->createHandlerWithMock([
            new Response(200, [], json_encode($response_body)),
        ]);

        $result = $handler->execute('GET', '/items', ['filter' => ['type' => 'article']]);

        $this->assertEquals($response_body, $result);
    }

    #[Group('unit')]
    public function testExecuteThrowsAPIExceptionOn4xxError() {
        $error_body = ['errors' => [['message' => 'Not found']]];
        $handler = $this->createHandlerWithMock([
            new Response(404, [], json_encode($error_body)),
        ]);

        $this->expectException(API::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Content Management API returned with error');

        $handler->execute('GET', '/items/invalid-id');
    }

    #[Group('unit')]
    public function testExecuteThrowsAPIExceptionOn5xxError() {
        $error_body = ['errors' => [['message' => 'Internal server error']]];
        $handler = $this->createHandlerWithMock([
            new Response(500, [], json_encode($error_body)),
        ]);

        $this->expectException(API::class);
        $this->expectExceptionCode(500);

        $handler->execute('GET', '/items');
    }

    #[Group('unit')]
    public function testExecuteAPIExceptionContainsResponseBody() {
        $error_body = '{"errors": [{"message": "Validation failed"}]}';
        $handler = $this->createHandlerWithMock([
            new Response(422, [], $error_body),
        ]);

        try {
            $handler->execute('POST', '/items', [], ['data' => []]);
            $this->fail('Expected API exception was not thrown');
        } catch (API $e) {
            $this->assertEquals($error_body, $e->getResponseBody());
            $this->assertEquals(422, $e->getCode());
        }
    }

    #[Group('unit')]
    public function testExecuteAcceptsCustomStatusCodes() {
        $response_body = ['data' => null];
        $handler = $this->createHandlerWithMock([
            new Response(404, [], json_encode($response_body)),
        ]);

        // 404 should not throw when explicitly accepted
        $result = $handler->execute('GET', '/items/maybe-exists', [], [], [404]);

        $this->assertEquals($response_body, $result);
    }

    #[Group('unit')]
    public function testExecuteThrowsDecodeExceptionOnInvalidJson() {
        $handler = $this->createHandlerWithMock([
            new Response(200, [], 'not valid json'),
        ]);

        $this->expectException(Decode::class);
        $this->expectExceptionMessage('Failed to decode JSON response');

        $handler->execute('GET', '/items');
    }

    #[Group('unit')]
    public function testExecuteDecodeExceptionContainsRawJson() {
        $invalid_json = '{incomplete json';
        $handler = $this->createHandlerWithMock([
            new Response(200, [], $invalid_json),
        ]);

        try {
            $handler->execute('GET', '/items');
            $this->fail('Expected Decode exception was not thrown');
        } catch (Decode $e) {
            $this->assertEquals($invalid_json, $e->getRawJson());
        }
    }

    #[Group('unit')]
    public function testExecuteThrowsUnknownExceptionOnConnectionError() {
        $mock = new MockHandler([
            new ConnectException(
                'Connection timeout',
                new Request('GET', '/items')
            ),
        ]);
        $handler_stack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handler_stack,
            'http_errors' => false,
        ]);
        $handler = new Handler('token', null, null, LogLevel::INFO, null, $client);

        $this->expectException(Unknown::class);
        $this->expectExceptionMessage('Encountered an unexpected error');
        $this->expectExceptionCode(1000);

        $handler->execute('GET', '/items');
    }

    #[Group('unit')]
    public function testAutoRetryDeciderReturnsTrueOn429() {
        $request = new Request('GET', '/items');
        $response = new Response(429);

        $result = Handler::autoRetryDecider(0, $request, $response);

        $this->assertTrue($result);
    }

    #[Group('unit')]
    public function testAutoRetryDeciderReturnsFalseOnOtherErrors() {
        $request = new Request('GET', '/items');
        $response = new Response(500);

        $result = Handler::autoRetryDecider(0, $request, $response);

        $this->assertFalse($result);
    }

    #[Group('unit')]
    public function testAutoRetryDeciderReturnsFalseOnSuccess() {
        $request = new Request('GET', '/items');
        $response = new Response(200);

        $result = Handler::autoRetryDecider(0, $request, $response);

        $this->assertFalse($result);
    }

    #[Group('unit')]
    public function testAutoRetryDeciderReturnsFalseWhenMaxRetriesExceeded() {
        $request = new Request('GET', '/items');
        $response = new Response(429);

        $result = Handler::autoRetryDecider(Handler::MAX_RETRIES, $request, $response);

        $this->assertFalse($result);
    }

    #[Group('unit')]
    public function testAutoRetryDeciderReturnsFalseWithNoResponse() {
        $request = new Request('GET', '/items');

        $result = Handler::autoRetryDecider(0, $request, null);

        $this->assertFalse($result);
    }

    #[Group('unit')]
    #[DataProvider('retryCountProvider')]
    public function testAutoRetryDeciderRetriesUpToMaxRetries(int $retries, bool $expected) {
        $request = new Request('GET', '/items');
        $response = new Response(429);

        $result = Handler::autoRetryDecider($retries, $request, $response);

        $this->assertEquals($expected, $result);
    }

    public static function retryCountProvider(): array {
        return [
            'retry 0' => [0, true],
            'retry 1' => [1, true],
            'retry 2' => [2, true],
            'retry 3' => [3, true],
            'retry 4' => [4, true],
            'retry 5 (max)' => [5, false],
            'retry 6' => [6, false],
        ];
    }

    #[Group('unit')]
    public function testConstructorWithInjectedClient() {
        $mock = new MockHandler([
            new Response(200, [], '{"data": []}'),
        ]);
        $handler_stack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handler_stack,
            'http_errors' => false,
        ]);

        $handler = new Handler('token', null, null, LogLevel::INFO, null, $client);

        // Execute a request to verify the mock client is used
        $result = $handler->execute('GET', '/items');

        $this->assertEquals(['data' => []], $result);
    }

    #[Group('unit')]
    public function testConstructorCreatesClientWithDefaultBaseUri() {
        // This test verifies construction doesn't throw
        $handler = new Handler('test-token');

        $this->assertInstanceOf(Handler::class, $handler);
    }

    #[Group('unit')]
    public function testConstructorCreatesClientWithCustomBaseUri() {
        $handler = new Handler('test-token', null, null, LogLevel::INFO, 'https://custom.api.com');

        $this->assertInstanceOf(Handler::class, $handler);
    }

    #[Group('unit')]
    public function testConstructorCreatesClientWithEnvironment() {
        $handler = new Handler('test-token', 'production');

        $this->assertInstanceOf(Handler::class, $handler);
    }

    #[Group('unit')]
    public function testConstructorCreatesClientWithLogger() {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = new Handler('test-token', null, $logger, LogLevel::DEBUG);

        $this->assertInstanceOf(Handler::class, $handler);
    }

    #[Group('unit')]
    public function testInitReturnsCachedInstance() {
        $handler1 = Handler::init('same-token', 'same-env');
        $handler2 = Handler::init('same-token', 'same-env');

        $this->assertSame($handler1, $handler2);
    }

    #[Group('unit')]
    public function testInitReturnsDifferentInstancesForDifferentTokens() {
        $handler1 = Handler::init('token-1');
        $handler2 = Handler::init('token-2');

        $this->assertNotSame($handler1, $handler2);
    }

    #[Group('unit')]
    public function testInitReturnsDifferentInstancesForDifferentEnvironments() {
        $handler1 = Handler::init('same-token', 'env-1');
        $handler2 = Handler::init('same-token', 'env-2');

        $this->assertNotSame($handler1, $handler2);
    }

    #[Group('unit')]
    public function testMaxRetriesConstant() {
        $this->assertEquals(5, Handler::MAX_RETRIES);
    }

    #[Group('unit')]
    public function testDefaultBaseUriConstant() {
        $this->assertEquals('https://site-api.datocms.com', Handler::DEFAULT_BASE_URI);
    }

    #[Group('unit')]
    public function testResetClearsInstanceCache() {
        $handler1 = Handler::init('cache-test-token');
        Handler::reset();
        $handler2 = Handler::init('cache-test-token');

        $this->assertNotSame($handler1, $handler2);
    }
}
