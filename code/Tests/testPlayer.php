<?php
/**
 * A class to test the class player
 *
 */

declare(strict_types = 1);

require_once(__DIR__ . '/../Lib/Core/Autoloader.php');

use PHPUnit\Framework\TestCase;
use Lib\Models\cards;
use Lib\Models\player;

class testPlayer extends TestCase
{
    /**
     * Test initialize
     *
     */
    public function testInitialize() : void
    {
        $name = 'Jan';
        $id = 23;
        $player = new player($id, $name );
        $this->assertEquals(true, is_object($player) , 'Problem in initializing player, constructor');
        $this->assertEquals($name, $player->getName() , 'Problem in initializing player, name');
        $this->assertEquals($id, $player->getId() , 'Problem in initializing player, id');
    }


    /**
     *  Test deal cards  and get one card from hand of a player
     *
     */
    public function testDealAndGetCard() : void
    {
        $playersData = [
            ['id' => 12 , 'name' => 'Jan'],
            ['id' => 34 , 'name' => 'Otto'],
            ['id' => 59 , 'name' => 'Jane'],
            ['id' => 11 , 'name' => 'John'],
        ];

        $players = [];
        foreach ($playersData as $player) {
            $players[$player['id']] = new player($player['id'], $player['name']);
        }

        // Set one player to test
        $testPlayer = $players[12];

        // Deal the cards
        $cards = new cards();
        // note: we don't shuffle because we need the exact content to test
        $deal = $cards->dealCardsToPlayerObjects($players);

        //
        // Check the deal is correct, by checking the cards in the hand of the test player
        //
        $expectedCards = [
            'HEART_7' => '&hearts;7',
            'HEART_8' => '&hearts;8',
            'HEART_9' => '&hearts;9',
            'HEART_10' => '&hearts;10',
            'HEART_11' => '&hearts;J',
            'HEART_12' => '&hearts;12',
            'HEART_13' => '&hearts;K',
        ];
        $this->assertEquals($expectedCards, $testPlayer->getCards() , 'Problem deal cards');

        //
        // Take on card out
        //
        $testPlayer->takeCard('HEART_11');
        $expectedCards = [
            'HEART_7' => '&hearts;7',
            'HEART_8' => '&hearts;8',
            'HEART_9' => '&hearts;9',
            'HEART_10' => '&hearts;10',
            'HEART_12' => '&hearts;12',
            'HEART_13' => '&hearts;K',
        ];
        $this->assertEquals($expectedCards, $testPlayer->getCards() , 'Problem take card HEART_11');

        //
        // Take the first card out
        //
        $testPlayer->takeCard('HEART_7');
        $expectedCards = [
            'HEART_8' => '&hearts;8',
            'HEART_9' => '&hearts;9',
            'HEART_10' => '&hearts;10',
            'HEART_12' => '&hearts;12',
            'HEART_13' => '&hearts;K',
        ];
        $this->assertEquals($expectedCards, $testPlayer->getCards() , 'Problem take card HEART_7');
    }


    /**
     * Test set and get score
     *
     */
    public function testSetAndGetScore() : void
    {
        $name = 'Jan';
        $id = 23;
        $player = new player($id, $name );

        $score1 = 45;
        $player->setScore($score1);

        $this->assertEquals($score1, $player->getScore() , 'testSetAndGetScore');

        $score2 = 23;
        $player->setScore($score2);
        $this->assertEquals($score1 + $score2, $player->getScore() , 'testSetAndGetScore2');
    }


    /**
     * Test json
     *
     * Test the generation of the Json string from players array
     * en encoding it back to an array.
     *
     * The json string is used to save the user data as a session variable.
     *
     */
    public function testJson() : void
    {
        $playersData = [
            [
                'id' => 12 ,
                'name' => 'Jan',
                'score' => 23,
                'cards' => ['a1', 'b1', 'c1', 'd1', 'e1'],
            ],
            [   'id' => 34 ,
                'name' => 'Otto',
                'score' => 23,
                'cards' => ['a2', 'b2', 'c2', 'd2', 'e2'],
            ],
            [
                'id' => 59 ,
                'name' => 'Jane',
                'score' => 23,
                'cards' => ['a3', 'b3', 'c3', 'd3', 'e3'],
            ],
            [
                'id' => 11 ,
                'name' => 'John',
                'score' => 23,
                'cards' => ['a4', 'b4', 'c4', 'd4', 'e4'],
            ],
        ];

        // Set players
        $players = [];
        foreach ($playersData as $player) {
             $playerObj  = new player($player['id'], $player['name']);
             $playerObj->setCards($player['cards']);
             $playerObj->setScore($player['score']);
             $players[$player['id']] = $playerObj;
        }

        // Convert to json and then back to object array
        $json = player::jsonEncode($players);
        $playersFromJson = player::jsonDecode($json);

        $this->assertEquals($players, $playersFromJson , 'Problem with json conversion');
    }
}
