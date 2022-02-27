<?php

namespace Test\EndToEnd\Movements;

use App\Infrastructure\Database\Database;
use Test\EndToEnd\EndToEndTestCase;

class MovementRankRouteTest extends EndToEndTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $db = Database::getConnection();
        $db->prepare('DELETE FROM personal_record')->execute();
        $db->prepare('DELETE FROM user')->execute();
        $db->prepare('DELETE FROM movement')->execute();
    }


    public function createDefaultDataTest()
    {
        $db = Database::getConnection();

        // Create 5 users
        $db->prepare("INSERT INTO `user` (id,name) VALUES (1,'Joao');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (2,'Jose');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (3,'Paulo');")->execute();

        // Create 2 movements
        $db->prepare("INSERT INTO movement (id,name) VALUES (1,'Deadlift');")->execute();
        $db->prepare("INSERT INTO movement (id,name) VALUES (2,'Back Squat');")->execute();
        $db->prepare("INSERT INTO movement (id,name) VALUES (3,'Bench Press');")->execute();

        // Create some personal records with same record value
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,1,100.0,'2021-01-01 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,1,180.0,'2021-01-02 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,1,150.0,'2021-01-03 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,1,110.0,'2021-01-04 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (2,1,110.0,'2021-01-04 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (2,1,140.0,'2021-01-05 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (2,1,190.0,'2021-01-06 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (3,1,170.0,'2021-01-01 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (3,1,120.0,'2021-01-02 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (3,1,130.0,'2021-01-03 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,2,130.0,'2021-01-03 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (2,2,130.0,'2021-01-03 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (3,2,125.0,'2021-01-03 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,2,110.0,'2021-01-05 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,2,100.0,'2021-01-01 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (2,2,120.0,'2021-01-01 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (3,2,120.0,'2021-01-01 00:00:00.0');")->execute();
    }

    public function test_should_get_success_response()
    {
        // Given
        $this->createDefaultDataTest();
        $movementId = 1;
        $defaultPage = 1;
        $defaultPageSize = 10;
        $expectedCountRankUsers = 3;

        // When
        $httpResponse = $this->httpGet("movements/{$movementId}/rank");
        $jsonResponse = (string) $httpResponse->getBody();
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
        $jsonResponse = (string) $httpResponse->getBody();
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
        $jsonResponse = (string) $httpResponse->getBody();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(400, $httpResponse->getStatusCode());
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
        $jsonResponse = (string) $httpResponse->getBody();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(400, $httpResponse->getStatusCode());
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
        $jsonResponse = (string) $httpResponse->getBody();
        $arrayResponse = json_decode($jsonResponse, true);

        // Then
        $this->assertEquals(400, $httpResponse->getStatusCode());
        $this->assertIsArray($arrayResponse);
        $this->assertArrayHasKey('error', $arrayResponse);
        $this->assertEquals("The movementId it's required", $arrayResponse['error']);
    }

    public function provideInvalidMovementId(): array
    {
        return [[0], [-1], [-2]];
    }
}