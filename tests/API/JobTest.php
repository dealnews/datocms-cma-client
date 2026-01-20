<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use DealNews\DatoCMS\CMA\API\Job;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the API\Job class
 */
#[Group('unit')]
class JobTest extends TestCase {

    /**
     * Creates a Job API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return Job
     */
    protected function createJobWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Job {
        $mock = $this->createMock(Handler::class);
        $mock->expects($this->once())
             ->method('execute')
             ->with($expected_method, $expected_path, $expected_query, $expected_data)
             ->willReturn($return_value);

        return new Job($mock);
    }

    // =========================================================================
    // retrieve() tests
    // =========================================================================

    #[Group('unit')]
    public function testRetrieve(): void {
        $expected_response = ['data' => ['id' => 'job-123', 'type' => 'job']];
        $job               = $this->createJobWithMock(
            'GET',
            '/job-results/job-123',
            [],
            [],
            $expected_response
        );

        $result = $job->retrieve('job-123');

        $this->assertEquals($expected_response, $result);
    }
}
