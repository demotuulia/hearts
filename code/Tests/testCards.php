<?php
/**
 * A class to test the class cards
 *
 */

declare(strict_types = 1);

require_once(__DIR__ . '/../Lib/Core/Autoloader.php');

use PHPUnit\Framework\TestCase;
use Lib\Models\cards;

class testCards extends TestCase
{
    /**
     * Test initialize
     *
     */
    public function testInitialize() : void
    {
        $cards = new cards();
        $this->assertEquals(true, is_object($cards) , 'Problem in initializing cars');

        // check the first and tle last card as code
        $this->assertEquals(
            'HEART_7',
            $cards->getCardByIndex(0),
            'First card in the stack should be "HEART_7" '
        );
        $this->assertEquals(
            'SPADE_14',
            $cards->getCardByIndex(31),
            'Last card in the stack should be "SPADE_14" '
        );

        // check the first and tle last card as html code
        $html = true;
        $this->assertEquals(
            '&hearts;7',
            $cards->getCardByIndex(0, $html),
            'First card in the stack should be "&hearts;7" '
        );
        $this->assertEquals(
            '&spades;A',
            $cards->getCardByIndex(31, $html),
            'Last card in the stack should be "&spades;A" '
        );
    }

    /**
     * Test shuffle
     *
     * Because this makes the cards in random order we just check the amount
     */
    public function testShuffle() : void
    {
        $cards = new cards();
        $cards->shuffle();
        $stack = $cards->getStack();

        // Check amount
        $this->assertEquals(32, count(array_unique($stack)), 'Shuffle failed" ');
    }


    /**
     * Test deal
     *
     */
    public function testDeal() : void
    {
        $cards = new cards();
        $cards->shuffle();
        $playerIds = [34, 45, 37, 4];
        $deal = $cards->dealCards($playerIds);

        // Check that each one have 7 cards
        $counts = array_values(
                array_map(
                    function ($items)
                    {
                        return count($items['codes']);
                    },
                    $deal
                )
        );

        $this->assertEquals([7,7,7,7], $counts, 'Deal failed" ');
    }
}
