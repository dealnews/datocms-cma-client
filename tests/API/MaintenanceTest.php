<?php

namespace DealNews\DatoCMS\CMA\Tests\API;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use DealNews\DatoCMS\CMA\API\Maintenance;
use DealNews\DatoCMS\CMA\HTTP\Handler;

/**
 * Tests for the API\Maintenance class
 */
class MaintenanceTest extends TestCase
{
    protected function createMaintenanceWithMock(
        string $expected_method,
        string $expected_path,
        array $expected_query = [],
        array $expected_data = [],
        array $return_value = []
    ): Maintenance {
        $mock_handler = $this->createMock(Handler::class);
        $mock_handler->expects($this->once())
            ->method('execute')
            ->with($expected_method, $expected_path, $expected_query, $expected_data)
            ->willReturn($return_value);

        return new Maintenance($mock_handler);
    }

    #[Group('unit')]
    public function testRetrieve(): void
    {
        $expected_response = [
            'data' => [
                'id' => '1',
                'type' => 'maintenance_mode',
                'attributes' => ['active' => false]
            ]
        ];

        $maintenance = $this->createMaintenanceWithMock(
            'GET',
            '/maintenance-mode',
            [],
            [],
            $expected_response
        );

        $result = $maintenance->retrieve();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testActivateWithoutForceParameter(): void
    {
        $expected_response = [
            'data' => [
                'id' => '1',
                'type' => 'maintenance_mode',
                'attributes' => ['active' => true]
            ]
        ];

        $maintenance = $this->createMaintenanceWithMock(
            'PUT',
            '/maintenance-mode/activate',
            [],
            [],
            $expected_response
        );

        $result = $maintenance->activate();

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testActivateWithForceFalse(): void
    {
        $expected_response = [
            'data' => [
                'id' => '1',
                'type' => 'maintenance_mode',
                'attributes' => ['active' => true]
            ]
        ];

        $maintenance = $this->createMaintenanceWithMock(
            'PUT',
            '/maintenance-mode/activate',
            [],
            [],
            $expected_response
        );

        $result = $maintenance->activate(false);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testActivateWithForceTrue(): void
    {
        $expected_response = [
            'data' => [
                'id' => '1',
                'type' => 'maintenance_mode',
                'attributes' => ['active' => true]
            ]
        ];

        $maintenance = $this->createMaintenanceWithMock(
            'PUT',
            '/maintenance-mode/activate',
            ['force' => true],
            [],
            $expected_response
        );

        $result = $maintenance->activate(true);

        $this->assertEquals($expected_response, $result);
    }

    #[Group('unit')]
    public function testDeactivate(): void
    {
        $expected_response = [
            'data' => [
                'id' => '1',
                'type' => 'maintenance_mode',
                'attributes' => ['active' => false]
            ]
        ];

        $maintenance = $this->createMaintenanceWithMock(
            'PUT',
            '/maintenance-mode/deactivate',
            [],
            [],
            $expected_response
        );

        $result = $maintenance->deactivate();

        $this->assertEquals($expected_response, $result);
    }
}
