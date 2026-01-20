<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\Environment;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\Environment class
 */
#[Group('unit')]
class EnvironmentTest extends TestCase {

    /**
     * Creates an Environment API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Environment
     */
    protected function createEnvironmentWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Environment {
        $mock = $this->createMock(Handler::class);
        $mock->expects($this->once())
             ->method('execute')
             ->with($expected_method, $expected_path, $expected_query, $expected_data)
             ->willReturn($return_value);

        return new Environment($mock);
    }

    // =========================================================================
    // fork() tests
    // =========================================================================

    #[Group('unit')]
    public function testForkWithOnlyRequiredParameters(): void {
        $expected_response = ['data' => ['id' => 'new-env', 'type' => 'environment']];
        $environment       = $this->createEnvironmentWithMock(
            'POST',
            '/environments/original-env/fork',
            [],
            ['data' => ['id' => 'new-env', 'type' => 'environment']],
            $expected_response
        );

        $result = $environment->fork('original-env', 'new-env');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testForkWithImmediateReturn(): void {
        $expected_response = ['data' => ['id' => 'forked-env', 'type' => 'environment']];
        $environment       = $this->createEnvironmentWithMock(
            'POST',
            '/environments/source-env/fork',
            ['immediate_return' => true],
            ['data'             => ['id' => 'forked-env', 'type' => 'environment']],
            $expected_response
        );

        $result = $environment->fork('source-env', 'forked-env', true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testForkWithFastOption(): void {
        $expected_response = ['data' => ['id' => 'forked-env', 'type' => 'environment']];
        $environment       = $this->createEnvironmentWithMock(
            'POST',
            '/environments/source-env/fork',
            ['fast' => true],
            ['data' => ['id' => 'forked-env', 'type' => 'environment']],
            $expected_response
        );

        $result = $environment->fork('source-env', 'forked-env', false, true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testForkWithForceOption(): void {
        $expected_response = ['data' => ['id' => 'forked-env', 'type' => 'environment']];
        $environment       = $this->createEnvironmentWithMock(
            'POST',
            '/environments/source-env/fork',
            ['force' => true],
            ['data'  => ['id' => 'forked-env', 'type' => 'environment']],
            $expected_response
        );

        $result = $environment->fork('source-env', 'forked-env', false, false, true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testForkWithAllOptions(): void {
        $expected_response = ['data' => ['id' => 'forked-env', 'type' => 'environment']];
        $environment       = $this->createEnvironmentWithMock(
            'POST',
            '/environments/source-env/fork',
            ['immediate_return' => true, 'fast' => true, 'force' => true],
            ['data'             => ['id' => 'forked-env', 'type' => 'environment']],
            $expected_response
        );

        $result = $environment->fork('source-env', 'forked-env', true, true, true);

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // promote() tests
    // =========================================================================

    #[Group('unit')]
    public function testPromote(): void {
        $expected_response = ['data' => ['id' => 'env-123', 'type' => 'environment', 'attributes' => ['primary' => true]]];
        $environment       = $this->createEnvironmentWithMock(
            'PUT',
            '/environments/env-123/promote',
            [],
            [],
            $expected_response
        );

        $result = $environment->promote('env-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // rename() tests
    // =========================================================================

    #[Group('unit')]
    public function testRename(): void {
        $expected_response = ['data' => ['id' => 'new-name', 'type' => 'environment']];
        $environment       = $this->createEnvironmentWithMock(
            'PUT',
            '/environments/old-name/rename',
            [],
            ['data' => ['id' => 'new-name', 'type' => 'environment']],
            $expected_response
        );

        $result = $environment->rename('old-name', 'new-name');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // list() tests
    // =========================================================================

    #[Group('unit')]
    public function testList(): void {
        $expected_response = ['data' => [['id' => 'env-1'], ['id' => 'env-2']]];
        $environment       = $this->createEnvironmentWithMock(
            'GET',
            '/environments',
            [],
            [],
            $expected_response
        );

        $result = $environment->list();

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = ['data' => ['id' => 'env-123', 'type' => 'environment']];
        $environment       = $this->createEnvironmentWithMock(
            'GET',
            '/environments/env-123',
            [],
            [],
            $expected_response
        );

        $result = $environment->retrieve('env-123');

        $this->assertEquals($expected_response, $result);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete(): void {
        $expected_response = ['data' => ['job_id' => 'job-123', 'status' => 'in_progress']];
        $environment       = $this->createEnvironmentWithMock(
            'DELETE',
            '/environments/env-123',
            [],
            [],
            $expected_response
        );

        $result = $environment->delete('env-123');

        $this->assertEquals($expected_response, $result);
    }
}
