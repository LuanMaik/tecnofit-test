<?php

namespace Test\Integration\Infrastructure\Database\Repositories;

use App\Domain\DTOs\RankUsersByMovementPaginate;
use App\Domain\Exceptions\MovementNotFoundException;
use App\Infrastructure\Database\Database;
use App\Infrastructure\Database\Repositories\MysqlMovementRepository;
use PHPUnit\Framework\TestCase;

class MysqlMovementRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $db = Database::getConnection();
        $db->prepare('DELETE FROM personal_record')->execute();
        $db->prepare('DELETE FROM user')->execute();
        $db->prepare('DELETE FROM movement')->execute();
    }


    public function createDataTestFiveUsersWithSameRecordInDeadlift()
    {
        $db = Database::getConnection();

        // Create 5 users
        $db->prepare("INSERT INTO `user` (id,name) VALUES (1,'Joao');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (2,'Jose');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (3,'Paulo');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (4,'Joana');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (5,'Maria');")->execute();

        // Create 2 movements
        $db->prepare("INSERT INTO movement (id,name) VALUES (1,'Deadlift');")->execute();
        $db->prepare("INSERT INTO movement (id,name) VALUES (2,'Back Squat');")->execute();

        // Create some personal records with same record value
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,1,180.0,'2021-01-01 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (2,1,180.0,'2021-01-02 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (3,1,180.0,'2021-01-03 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (4,1,180.0,'2021-01-04 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (5,1,180.0,'2021-01-04 00:00:00.0');")->execute();
    }

    /**
     * Scenario: There are 5 users with the same record value (180) in Deadlift movement
     * Conclusion: All the 5 users are rank one in Deadlift movement
     */
    public function test_should_has_all_users_are_in_rank_one()
    {
        // Given
        $this->createDataTestFiveUsersWithSameRecordInDeadlift();
        $repository = new MysqlMovementRepository();
        $deadliftMovementId = 1;

        // When
        $rankPaginate = $repository->getRankUsersByMovementId($deadliftMovementId, 1, 10);

        // Then
        $this->assertInstanceOf(RankUsersByMovementPaginate::class, $rankPaginate);
        $this->assertCount(5, $rankPaginate->getRank());
        foreach ($rankPaginate->getRank() as $userRank) {
            $this->assertEquals(1, $userRank->getRank(), 'All the users should be rank number one');
        }
    }







    public function createDataTestUserJoanaIsRankOneAndJoseRankTwoInDeadlift()
    {
        $db = Database::getConnection();

        // Create 5 users
        $db->prepare("INSERT INTO `user` (id,name) VALUES (1,'Joao');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (2,'Jose');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (3,'Paulo');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (4,'Joana');")->execute();
        $db->prepare("INSERT INTO `user` (id,name) VALUES (5,'Maria');")->execute();

        // Create 2 movements
        $db->prepare("INSERT INTO movement (id,name) VALUES (1,'Deadlift');")->execute();
        $db->prepare("INSERT INTO movement (id,name) VALUES (2,'Back Squat');")->execute();

        // Create some personal records with same record value
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (1,1,170.0,'2021-01-01 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (2,1,190.0,'2021-01-02 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (3,1,150.0,'2021-01-03 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (4,1,200.0,'2021-01-04 00:00:00.0');")->execute();
        $db->query("INSERT INTO personal_record (user_id,movement_id,value,`date`) VALUES (5,1,130.0,'2021-01-04 00:00:00.0');")->execute();
    }



    /**
     * Scenario: There are 5 users with different record value in Deadlift movement,
     * Joana is rank one and Jose is rank two
     */
    public function test_should_has_joana_rank_one_and_jose_rank_two()
    {
        // Given
        $this->createDataTestUserJoanaIsRankOneAndJoseRankTwoInDeadlift();
        $repository = new MysqlMovementRepository();
        $deadliftMovementId = 1;

        // When
        $rankPaginate = $repository->getRankUsersByMovementId($deadliftMovementId, 1, 10);

        // Then
        $this->assertInstanceOf(RankUsersByMovementPaginate::class, $rankPaginate);
        $this->assertCount(5, $rankPaginate->getRank());
        $this->assertEquals(1, $rankPaginate->getRank()[0]->getRank(), 'Joana should be rank one');
        $this->assertEquals('Joana', $rankPaginate->getRank()[0]->getName());
        $this->assertEquals(2, $rankPaginate->getRank()[1]->getRank(), 'Jose should be rank two');
        $this->assertEquals('Jose', $rankPaginate->getRank()[1]->getName());
    }



    /**
     * Scenario: There are 5 users with different record value in Deadlift movement,
     * Joana is rank one and Jose is rank two, and it's requested the page 1 with 2 register.
     */
    public function test_should_has_only_joana_and_jose_in_page_one()
    {
        // Given
        $this->createDataTestUserJoanaIsRankOneAndJoseRankTwoInDeadlift();
        $repository = new MysqlMovementRepository();
        $deadliftMovementId = 1;
        $page = 1;
        $pageSize = 2;

        // When
        $rankPaginate = $repository->getRankUsersByMovementId($deadliftMovementId, $page, $pageSize);

        // Then
        $this->assertInstanceOf(RankUsersByMovementPaginate::class, $rankPaginate);
        $this->assertCount($pageSize, $rankPaginate->getRank());
        $this->assertEquals(1, $rankPaginate->getRank()[0]->getRank(), 'Joana should be rank one');
        $this->assertEquals('Joana', $rankPaginate->getRank()[0]->getName());
        $this->assertEquals(2, $rankPaginate->getRank()[1]->getRank(), 'Jose should be rank two');
        $this->assertEquals('Jose', $rankPaginate->getRank()[1]->getName());
    }

    /**
     * Scenario: There are 5 users with different record value in Deadlift movement,
     * Joao is rank three and Paulo is rank four, and it's requested the page 2 with 2 register.
     */
    public function test_should_has_only_joao_and_paulo_in_page_two()
    {
        // Given
        $this->createDataTestUserJoanaIsRankOneAndJoseRankTwoInDeadlift();
        $repository = new MysqlMovementRepository();
        $deadliftMovementId = 1;
        $page = 2;
        $pageSize = 2;

        // When
        $rankPaginate = $repository->getRankUsersByMovementId($deadliftMovementId, $page, $pageSize);

        // Then
        $this->assertInstanceOf(RankUsersByMovementPaginate::class, $rankPaginate);
        $this->assertCount($pageSize, $rankPaginate->getRank());
        $this->assertEquals('Joao', $rankPaginate->getRank()[0]->getName());
        $this->assertEquals(3, $rankPaginate->getRank()[0]->getRank(), 'Joao should be rank one');
        $this->assertEquals('Paulo', $rankPaginate->getRank()[1]->getName());
        $this->assertEquals(4, $rankPaginate->getRank()[1]->getRank(), 'Paulo should be rank two');
    }




    public function test_should_throw_exception_when_not_found_movement()
    {
        // Given
        $repository = new MysqlMovementRepository();
        $movementId = 99999;

        // Expected
        $this->expectException(MovementNotFoundException::class);

        // When
        $repository->getRankUsersByMovementId($movementId, 1, 10);
    }
}