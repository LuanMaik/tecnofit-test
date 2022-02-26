<?php

namespace Test\EndToEnd\Movements;

use Test\EndToEnd\EndToEndTestCase;

class MovementRankRouteTest extends EndToEndTestCase
{
    public function test_should_get_success_response()
    {
        // Given
        $movementId = 1;
        $defaultPage = 1;
        $defaultPageSize = 10;
        $expectedCountRankUsers = 3;

        // When
        $httpResponse = $this->httpGet("movements/{$movementId}/rank");
        $jsonResponse = $httpResponse->getBody()->getContents();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(200, $httpResponse->getStatusCode());
        $this->assertIsArray($arrayResponse);
        $this->assertArrayHasKey('movement', $arrayResponse);
        $this->assertArrayHasKey('rank', $arrayResponse);
        $this->assertArrayHasKey('currentPage', $arrayResponse);
        $this->assertArrayHasKey('pageSize', $arrayResponse);
        $this->assertArrayHasKey('nextPage', $arrayResponse);
        $this->assertIsArray($arrayResponse['rank']);

        $this->assertEquals($movementId, $arrayResponse['movement']['id']);
        $this->assertCount($expectedCountRankUsers, $arrayResponse['rank']);
        $this->assertEquals($defaultPage, $arrayResponse['currentPage']);
        $this->assertEquals($defaultPageSize, $arrayResponse['pageSize']);
        $this->assertEquals(null, $arrayResponse['nextPage']);
    }

    public function test_should_get_404_error_when_movement_not_found()
    {
        // Given
        $movementId = 9999;

        // When
        $httpResponse = $this->httpGet("movements/{$movementId}/rank");
        $jsonResponse = $httpResponse->getBody()->getContents();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(404, $httpResponse->getStatusCode());
        $this->assertIsArray($arrayResponse);
        $this->assertArrayHasKey('error', $arrayResponse);
        $this->assertEquals('The movement requested does not exist.', $arrayResponse['error']);
    }

    /**
     * @dataProvider provideInvalidPage
     */
    public function test_should_get_400_error_when_requested_invalid_page($invalidPage)
    {
        // Given
        $movementId = 1;

        // When
        $httpResponse = $this->httpGet("movements/{$movementId}/rank", ['page' => $invalidPage]);
        $jsonResponse = $httpResponse->getBody()->getContents();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(404, $httpResponse->getStatusCode());
        $this->assertIsArray($arrayResponse);
        $this->assertArrayHasKey('error', $arrayResponse);
        $this->assertEquals('The page number must be greater than 0', $arrayResponse['error']);
    }

    public function provideInvalidPage(): array
    {
        return [[0], [-1], [-2]];
    }

    /**
     * @dataProvider provideInvalidPage
     */
    public function test_should_get_400_error_when_requested_invalid_pagesize($invalidPageSize)
    {
        // Given
        $movementId = 1;

        // When
        $httpResponse = $this->httpGet("movements/{$movementId}/rank", ['pageSize' => $invalidPageSize]);
        $jsonResponse = $httpResponse->getBody()->getContents();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(404, $httpResponse->getStatusCode());
        $this->assertIsArray($arrayResponse);
        $this->assertArrayHasKey('error', $arrayResponse);
        $this->assertEquals("The page size number must be greater than 0", $arrayResponse['error']);
    }

    public function provideInvalidPageSize(): array
    {
        return [[0], [-1], [-2]];
    }


    /**
     * @dataProvider provideInvalidMovementId
     */
    public function test_should_get_400_error_when_requested_invalid_movementid($invalidMovementId)
    {
        // When
        $httpResponse = $this->httpGet("movements/{$invalidMovementId}/rank");
        $jsonResponse = $httpResponse->getBody()->getContents();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(404, $httpResponse->getStatusCode());
        $this->assertIsArray($arrayResponse);
        $this->assertArrayHasKey('error', $arrayResponse);
        $this->assertEquals("The movementId it's required", $arrayResponse['error']);
    }

    public function provideInvalidMovementId(): array
    {
        return [[0], [-1], [-2]];
    }
}