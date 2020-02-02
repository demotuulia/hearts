<?php
/**
 * A class to test the class rules
 *
 */

declare(strict_types = 1);

require_once(__DIR__ . '/../Lib/Core/Autoloader.php');

use PHPUnit\Framework\TestCase;
use Lib\Models\cards;
use Lib\Models\player;
use Lib\Models\rules;

class testRules extends TestCase
{
    /**
     * Test initialize
     *
     */
    public function testInitialize() : void
    {
        $rules = new rules();
        $this->assertEquals(true, is_object($rules) , 'Problem in initializing rules, constructor');
    }


    /**
     *  Test valid moves
     *
     */
    public function testValidMoves() : void
    {
        $useCases =
            [
                [
                    'label' => 'User has  match card and sets a match card',
                    'startCard' => 'HEART_9',
                    'userCards' => [
                        'SPADE_10' =>'DUMMY',
                        'HEART_8' =>'DUMMY',
                        'DIAMOND_11' =>'DUMMY',
                        'CLUB_8' =>'DUMMY',
                        'CLUB_13'  =>'DUMMY',
                        'SPADE_8' =>'DUMMY',
                        'SPADE_9' =>'DUMMY'
                    ],
                    'userCardToSet' => 'HEART_8',
                    'expectedResult' => true
                ],


                [
                    'label' => 'User has not match card and puts a random card.',
                    'startCard' => 'HEART_9',
                    'userCards' => [
                        'SPADE_10' =>'DUMMY',
                        'DIAMOND_8' =>'DUMMY',
                        'DIAMOND_11' =>'DUMMY',
                        'CLUB_8' =>'DUMMY',
                        'CLUB_13' =>'DUMMY',
                        'SPADE_8' =>'DUMMY',
                        'SPADE_9' =>'DUMMY'
                    ],
                    'userCardToSet' => 'DIAMOND_8',
                    'expectedResult' => true
                ],

                [
                    'label' => 'User has  match card, but  tries to  puts  random card.',
                    'startCard' => 'HEART_9',
                    'userCards' => [
                        'SPADE_10' =>'DUMMY',
                        'HEART_8' =>'DUMMY',
                        'DIAMOND_11' =>'DUMMY',
                        'CLUB_8' =>'DUMMY',
                        'CLUB_13' =>'DUMMY',
                        'SPADE_8' =>'DUMMY',
                        'SPADE_9' =>'DUMMY'
                    ],
                    'userCardToSet' => 'DIAMOND_8',
                    'expectedResult' => false
                ],
            ];

            $rules = new rules();

            foreach ($useCases as $case) {
                $playerId = 0;    // Here the user id is not needed
                $rules->setStartCard($playerId, $case['startCard']);

                $this->assertEquals(
                    $case['expectedResult'],
                    $rules->isValidMove($case['userCardToSet'], $case['userCards']),
                    'Case ' . $case['label']
                );
            }
    }


    /**
     * Test new deal
     *
     * Test when it is time to deal the cards again
     */
    public function testNewDeal() : void
    {
        $useCases = [
            [
                'label' => 'No new deal',
                'playersData' => [
                    [
                        'id' => 12 ,
                        'name' => 'Jan',
                        'cards' => ['a1', 'b1', 'c1', 'd1', 'e1'],
                    ],
                    [   'id' => 34 ,
                        'name' => 'Otto',
                        'cards' => ['a2', 'b2', 'c2', 'd2', 'e2'],
                    ],
                    [
                        'id' => 59 ,
                        'name' => 'Jane',
                        'cards' => ['a3', 'b3', 'c3', 'd3', 'e3'],
                    ],
                    [
                        'id' => 11 ,
                        'name' => 'John',
                        'cards' => ['a4', 'b4', 'c4', 'd4', 'e4'],
                    ],
                ],
                'expectedResult' => false
            ],

            [
                'label' => 'New deal is needed',
                'playersData' => [
                    [
                        'id' => 12 ,
                        'name' => 'Jan',
                        'cards' => [],
                    ],
                    [   'id' => 34 ,
                        'name' => 'Otto',
                        'cards' => [],
                    ],
                    [
                        'id' => 59 ,
                        'name' => 'Jane',
                        'cards' => [],
                    ],
                    [
                        'id' => 11 ,
                        'name' => 'John',
                        'cards' => [],
                    ],
                ],
                'expectedResult' => false
            ],
        ];

        $rules = new rules();

        foreach ($useCases as $case) {
            // Set players
            $players = [];
            foreach ($case['playersData'] as $player) {
                $playerObj  = new player($player['id'], $player['name']);
                $playerObj->setCards($player['cards']);
                $players[$player['id']] = $playerObj;
            }
            $this->assertEquals(
                $case['expectedResult'],
                $rules->isNewDeal($players),
                'Case ' . $case['label']
            );
        }
    }


    /**
     * Test Looser
     *
     * Test the looser of one round
     */
    public function testLooser() : void
    {
        $useCases = [
                [
                    'label' => 'Looser: player 2',
                    'cardsToTable' => [
                        [ 'playerId' => 1, 'card' => 'SPADE_10' ],
                        [ 'playerId' => 2, 'card' => 'SPADE_13'],
                        [ 'playerId' => 3, 'card' => 'SPADE_7'],
                        [ 'playerId' => 4, 'card' => 'HEART_13'],
                    ],
                    'expectedLooser' => 2
                ],

                [
                    'label' => 'Looser: player 1',
                    'cardsToTable' => [
                        [ 'playerId' => 1, 'card' => 'SPADE_13' ],
                        [ 'playerId' => 2, 'card' => 'SPADE_10'],
                        [ 'playerId' => 3, 'card' => 'SPADE_7'],
                        [ 'playerId' => 4, 'card' => 'HEART_13'],
                    ],
                    'expectedLooser' => 1
                ]
        ];

        $rules = new rules();

        foreach ($useCases as $case) {
            $playerId =  $case['cardsToTable'][0]['playerId'];
            $card =  $case['cardsToTable'][0]['card'];
            $rules->setStartCard($playerId, $card);
            unset($case['cardsToTable'][0]);
            foreach ($case['cardsToTable'] as $card) {
                $playerId =  $card['playerId'];
                $card =  $card['card'];
                $rules->setCardOnTheTable($card, $playerId);
            }

            $this->assertEquals(
                $case['expectedLooser'],
                $rules->getLooser(),
                'Case ' . $case['label']
            );
        }
    }


    /**
     * Test score
     *
     * Test the score of one round
     */
    public function testScore() : void
    {
        $useCases = [
            [
                'label' => 'Score 1',
                'cardsOnTable' => [
                    [ 'playerId' => 1, 'card' => 'SPADE_10' ],
                    [ 'playerId' => 2, 'card' => 'SPADE_13'],
                    [ 'playerId' => 3, 'card' => 'SPADE_7'],
                    [ 'playerId' => 4, 'card' => 'HEART_13'],
                ],
                'expectedScore' => 1
            ],

            [
                'label' => 'Score 6',
                'cardsOnTable' => [
                    [ 'playerId' => 1, 'card' => 'SPADE_13' ],
                    [ 'playerId' => 2, 'card' => 'SPADE_12'],
                    [ 'playerId' => 3, 'card' => 'SPADE_7'],
                    [ 'playerId' => 4, 'card' => 'HEART_13'],
                ],
                'expectedScore' => 6
            ],

            [
                'label' => 'Score 2',
                'cardsOnTable' => [
                    [ 'playerId' => 1, 'card' => 'CLUB_11' ],
                    [ 'playerId' => 2, 'card' => 'DIAMOND_12'],
                    [ 'playerId' => 3, 'card' => 'SPADE_7'],
                    [ 'playerId' => 4, 'card' => 'CLUB_8'],
                ],
                'expectedScore' => 2
            ]
        ];

        $rules = new rules();

        foreach ($useCases as $case) {
            $playerId =  $case['cardsOnTable'][0]['playerId'];
            $card =  $case['cardsOnTable'][0]['card'];
            $rules->setStartCard($playerId, $card);
            unset($case['cardsOnTable'][0]);
            foreach ($case['cardsOnTable'] as $card) {
                $playerId =  $card['playerId'];
                $card =  $card['card'];
                $rules->setCardOnTheTable($card, $playerId);
            }

            $this->assertEquals(
                $case['expectedScore'],
                $rules->getScore(),
                'Case ' . $case['label']
            );
        }
    }


    /**
     * Test Player who starts the round
     *
     * Note: in the tests we don't use the random start
     */
    public function testPlayerWhoStarts() : void
    {
        $rules = new rules();
        $playerIds = [ 35, 45, 23, 3];
        $this->assertEquals(35, $rules->getPlayerWhoStarts($playerIds));
        $this->assertEquals(45, $rules->getPlayerWhoStarts($playerIds));
        $this->assertEquals(23, $rules->getPlayerWhoStarts($playerIds));
        $this->assertEquals(3, $rules->getPlayerWhoStarts($playerIds));
        $this->assertEquals(35, $rules->getPlayerWhoStarts($playerIds));
        $this->assertEquals(45, $rules->getPlayerWhoStarts($playerIds));
    }


    /**
     * The when the game is over
     *
     */
    public function testGameOver() : void
    {
        $playersData = [
            [
                'id' => 12 ,
                'name' => 'Jan',
                'score' => 3,
            ],
            [   'id' => 34 ,
                'name' => 'Otto',
                'score' => 13,
            ],
            [
                'id' => 59 ,
                'name' => 'Jane',
                'score' => 8,
            ],
            [
                'id' => 11 ,
                'name' => 'John',
                'score' => 8,
            ],
        ];

        $players = [];
        foreach ($playersData as $player) {
            $playerObj  = new player($player['id'], $player['name']);
            $playerObj->setScore($player['score']);
            $players[$player['id']] = $playerObj;
        }

        $rules = new rules();
        // Total score < 50
        $this->assertEquals(false, $rules->isGameOver($players));

        // Total score > 50
        current($players)->setScore(50);
        $this->assertEquals(true, $rules->isGameOver($players));
    }


    /**
     * Test the ranking
     *
     */
    public function testRanking() : void
    {
        $playersData = [
            [
                'id' => 12 ,
                'name' => 'Jan',
                'score' => 3,
            ],
            [   'id' => 34 ,
                'name' => 'Otto',
                'score' => 13,
            ],
            [
                'id' => 59 ,
                'name' => 'Jane',
                'score' => 8,
            ],
            [
                'id' => 11 ,
                'name' => 'John',
                'score' => 18,
            ],
        ];

        $expectedRanking =
        [
            0 => 'John',
            1 => 'Otto',
            2 => 'Jane',
            3 => 'Jan'
        ];

        $players = [];
        foreach ($playersData as $player) {
            $playerObj  = new player($player['id'], $player['name']);
            $playerObj->setScore($player['score']);
            $players[$player['id']] = $playerObj;
        }

        $rules = new rules();
        $ranking = $rules->getRanking($players);
        $rankingNames = array_map(
            function ($player)
            {
                return $player->getName();
            },
            $ranking
        );

        $this->assertEquals($expectedRanking, $rankingNames);
    }
}
