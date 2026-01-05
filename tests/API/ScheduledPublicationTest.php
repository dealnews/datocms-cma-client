<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\ScheduledPublication;
use DealNews\DatoCMS\CMA\HTTP\Handler;
use DealNews\DatoCMS\CMA\Input\ScheduledPublication as ScheduledPublicationInput;

/**
 * Tests for the API\ScheduledPublication class
 */
class ScheduledPublicationTest extends TestCase {

    /**
     * Creates a ScheduledPublication API with a mocked Handler
     *
     * @param string               $expected_method HTTP method the handler should expect
     * @param string               $expected_path   API path the handler should expect
     * @param array<string, mixed> $expected_query  Expected query parameters
     * @param array<string, mixed> $expected_data   Expected POST/PUT data
     * @param array<string, mixed> $return_value    Value the mock should return
     *
     * @return ScheduledPublication
     */
    protected function createScheduledPublicationWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): ScheduledPublication {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new ScheduledPublication($mock_handler);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    #[Group('unit')]
    public function testCreateWithArrayMinimalData() {
        $data = [
            'type' => 'scheduled_publication',
            'attributes' => ['publication_scheduled_at' => '2030-09-01T12:00:00Z'],
        ];
        $expected_response = ['data' => ['id' => 'scheduled-pub-123', 'type' => 'scheduled_publication']];
        $scheduled_publication = $this->createScheduledPublicationWithMock(
            'POST',
            '/items/record-123/scheduled-publication',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $scheduled_publication->create('record-123', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithArrayFullData() {
        $data = [
            'type' => 'scheduled_publication',
            'attributes' => [
                'publication_scheduled_at' => '2030-09-01T12:00:00Z',
                'selective_publication' => [
                    'content_in_locales' => ['en', 'es'],
                    'non_localized_content' => true,
                ],
            ],
        ];
        $expected_response = ['data' => ['id' => 'scheduled-pub-456']];
        $scheduled_publication = $this->createScheduledPublicationWithMock(
            'POST',
            '/items/record-456/scheduled-publication',
            [],
            ['data' => $data],
            $expected_response
        );

        $result = $scheduled_publication->create('record-456', $data);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateWithScheduledPublicationInput() {
        $input = new ScheduledPublicationInput();
        $input->attributes['publication_scheduled_at'] = '2030-09-01T12:00:00Z';

        $expected_data = ['data' => $input->toArray()];
        $expected_response = ['data' => ['id' => 'scheduled-pub-789']];

        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with('POST', '/items/record-789/scheduled-publication', [], $expected_data)
            ->willReturn($expected_response);

        $scheduled_publication = new ScheduledPublication($mock_handler);
        $result = $scheduled_publication->create('record-789', $input);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testCreateThrowsWhenPublicationScheduledAtIsMissing() {
        $data = [
            'type' => 'scheduled_publication',
            'attributes' => [],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $scheduled_publication = new ScheduledPublication($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('publication_scheduled_at must be set to an ISO 8601 date/time in the \'attributes\'');

        $scheduled_publication->create('record-123', $data);
    }

    #[Group('unit')]
    public function testCreateThrowsWhenPublicationScheduledAtIsEmpty() {
        $data = [
            'type' => 'scheduled_publication',
            'attributes' => ['publication_scheduled_at' => ''],
        ];

        $mock_handler = $this->createMock(Handler::class);
        $scheduled_publication = new ScheduledPublication($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('publication_scheduled_at must be set to an ISO 8601 date/time in the \'attributes\'');

        $scheduled_publication->create('record-123', $data);
    }

    #[Group('unit')]
    public function testCreateThrowsWhenPublicationScheduledAtNotInAttributes() {
        $data = [
            'type' => 'scheduled_publication',
        ];

        $mock_handler = $this->createMock(Handler::class);
        $scheduled_publication = new ScheduledPublication($mock_handler);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('publication_scheduled_at must be set to an ISO 8601 date/time in the \'attributes\'');

        $scheduled_publication->create('record-123', $data);
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    #[Group('unit')]
    public function testDelete() {
        $expected_response = ['data' => ['id' => 'record-123', 'type' => 'item']];
        $scheduled_publication = $this->createScheduledPublicationWithMock(
            'DELETE',
            '/items/record-123/scheduled-publication',
            [],
            [],
            $expected_response
        );

        $result = $scheduled_publication->delete('record-123');

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testDeleteWithDifferentRecordId() {
        $expected_response = ['data' => ['id' => 'record-999', 'type' => 'item']];
        $scheduled_publication = $this->createScheduledPublicationWithMock(
            'DELETE',
            '/items/record-999/scheduled-publication',
            [],
            [],
            $expected_response
        );

        $result = $scheduled_publication->delete('record-999');

        $this->assertEquals($expected_response, $result);
    }
}
