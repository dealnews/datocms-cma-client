<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\ScheduledUnpublishing;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\ScheduledUnpublishing as ScheduledUnpublishingInput;

/**
 * Tests for the API\ScheduledUnpublishing class
 */
class ScheduledUnpublishingTest extends TestCase {

    /**
     * Creates a ScheduledUnpublishing API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return ScheduledUnpublishing
     */
    protected function createScheduledUnpublishingWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): ScheduledUnpublishing {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new ScheduledUnpublishing($mock_handler);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArray() {
        $data = [
            'type' => 'scheduled_unpublishing',
            'attributes' => ['unpublishing_scheduled_at' => '2030-09-01T12:00:00Z'],
        ];
        $expected_response = ['data' => ['id' => 'scheduled-id', 'type' => 'scheduled_unpublishing']];
        $scheduled_unpublishing = $this->createScheduledUnpublishingWithMock(
            'POST',
            '/items/record-123/scheduled-unpublishing',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $scheduled_unpublishing->create('record-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithScheduledUnpublishingInput() {
        $input = new ScheduledUnpublishingInput();
        $input->attributes['unpublishing_scheduled_at'] = '2030-09-01T12:00:00Z';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'scheduled-id', 'type' => 'scheduled_unpublishing']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/items/record-123/scheduled-unpublishing', [], $expected_data)
            ->willReturn($expected_response);

        $scheduled_unpublishing = new ScheduledUnpublishing($mock_handler);
        $result = $scheduled_unpublishing->create('record-123', $input);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithContentInLocales() {
        $data = [
            'type' => 'scheduled_unpublishing',
            'attributes' => [
                'unpublishing_scheduled_at' => '2030-09-01T12:00:00Z',
                'content_in_locales' => ['en', 'it'],
            ],
        ];
        $expected_response = ['data' => ['id' => 'scheduled-id']];
        $scheduled_unpublishing = $this->createScheduledUnpublishingWithMock(
            'POST',
            '/items/record-123/scheduled-unpublishing',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $scheduled_unpublishing->create('record-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateThrowsWhenUnpublishingScheduledAtMissing() {
        $mock_handler = $this->createMock(Handler::class);
        $scheduled_unpublishing = new ScheduledUnpublishing($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('unpublishing_scheduled_at must be set to an ISO 8601 date/time in the \'attributes\'');

        $data = [
            'type' => 'scheduled_unpublishing',
            'attributes' => [],
        ];
        $scheduled_unpublishing->create('record-123', $data);
    }

    #[Group('unit')]
    public function testCreateThrowsWhenUnpublishingScheduledAtEmpty() {
        $mock_handler = $this->createMock(Handler::class);
        $scheduled_unpublishing = new ScheduledUnpublishing($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('unpublishing_scheduled_at must be set to an ISO 8601 date/time in the \'attributes\'');

        $data = [
            'type' => 'scheduled_unpublishing',
            'attributes' => ['unpublishing_scheduled_at' => ''],
        ];
        $scheduled_unpublishing->create('record-123', $data);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete() {
        $expected_response = ['data' => ['id' => 'record-123', 'type' => 'item']];
        $scheduled_unpublishing = $this->createScheduledUnpublishingWithMock(
            'DELETE',
            '/items/record-123/scheduled-unpublishing',
            [],
            [],
            $expected_response
        );

        $result = $scheduled_unpublishing->delete('record-123');

        $this->assertEquals($expected_response, $result);
    }
}
