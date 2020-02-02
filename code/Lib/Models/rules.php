<?php
/**
 * A class to check the rules and control the game process
 *
 * like:
 *  .Whose turn it is to start
 *  .when a new round is to be started,
 *  .which cards are set to the table ,
 *  .allowed moves
 *  .when the game is over
 *
 *  Note
 *  For some terms I use the following words:
 *
 *  1 'round'
 *  One session to to put the cards on
 *  the table. (First the random start card by one player and the the rest 3 cards after
 *  the rules of this game)
 *
 *  2 'symbol'
 *  I mean the symbol group of the card : Heart, Diamond, Club or Spade
 *
 */

declare(strict_types = 1);

namespace Lib\Models;

final class rules {


    /**
     * Start card, the card which the first player sets to the table
     *
     * @var string
     */
    private $startCard;


    /**
     * Cards on the table on the current round
     *
     * @var array
     */
    private $cardsOnTable = [];


    /**
     * A an array to emulate session in the unit tests
     *
     * @var array
     */
    private static $cliSession = [];


    /**
     * rules constructor.
     *
     * @param int $id
     * @param string $name
     */
    public function __construct()
    {
    }


    /**
     * Set start card to the table
     *
     * @param int $playerId
     * @param string $startCard
     */
    public function setStartCard(int $playerId, string $startCard) :void
    {
        $this->startCard = $startCard;
        $this->setCardOnTheTable($startCard, $playerId, true);
    }


    /**
     *  Clean up table from all cards
     */
    private function cleanUpTable() :void
    {
        $this->cardsOnTable= [];
    }


    /**
     * Set one card of a certain player to the table
     *
     * @param string $cardCode
     * @param int $playerId
     * @param bool $isStartCard
     */
    public function setCardOnTheTable(string $cardCode, int $playerId, bool $isStartCard = false) : void
    {
        if ($isStartCard) {
            $this->cleanUpTable();
        }
        $this->cardsOnTable[] = [
                'playerId' => $playerId,
                'cardCode' => $cardCode
        ];
    }


    /**
     * Get all cards on the table
     *
     * @return array
     */
    public function getCardsOnTheTable() :array
    {
        return $this->cardsOnTable;
    }


    /**
     * Get start card (first card set to the table in one round)
     *
     * @return string
     */
    public function getStartCard() : string
    {
        return $this->startCard;
    }


    /**
     * Validate the move when a player sets a card to the table
     *
     * @param string $cardToMove
     * @param array $playerCardsinHand
     * @return bool
     */
    public function isValidMove(string $cardToSet, array $playerCardsinHand) : bool
    {
        list($startCardSymbol) = $this->getSymbolAndIndex($this->startCard);
        list($playerCardSymbol) = $this->getSymbolAndIndex($cardToSet);

        $symbolsInHand = array_unique(
            array_map(
                function ($card) {

                    list($playerCardSymbol) = $this->getSymbolAndIndex($card);
                    return $playerCardSymbol;
                },
                array_keys($playerCardsinHand)
            )
        );

        /**
         * Case: player puts a card which does not match the start card
         *
         * Rule: he must not have matching cards in his hand
         */
        if ($startCardSymbol != $playerCardSymbol) {
                // Check if player as a matching symbol with the start card in is hand
                if (is_numeric(array_search($startCardSymbol, $symbolsInHand))) {
                    return false;
                }
        }
        return true;
    }


    /**
     * Get the symbol and the index of one card
     *
     * @param string $card
     * @return array
     */
    private function getSymbolAndIndex(string $card) : array
    {
        return explode('_' , $card);
    }


    /**
     * Get looser of one round
     *
     * The looser is the one who has the matching card symbol and the highest index
     *
     * @return int
     */
    public function getLooser() : int
    {
        list($startCardSymbol ) = $this->getSymbolAndIndex($this->startCard);

        $minIndex = 0;
        $looserId = false;

        foreach ($this->cardsOnTable as $card) {
            list($symbol, $index ) = $this->getSymbolAndIndex($card['cardCode']);
            if ($symbol == $startCardSymbol && $index > $minIndex) {
                $minIndex = $index;
                $looserId = $card['playerId'];
            }
        }

        return $looserId;
    }


    /**
     * Get score of one round
     *
     * @return int
     */
    public function getScore() : int
    {
        $score = 0;
        foreach ($this->cardsOnTable as $card) {

            list($symbol ) = $this->getSymbolAndIndex($card['cardCode']);
            if ($symbol == 'HEART') {
                $score ++;
            }
            if ($card['cardCode'] == 'SPADE_12') {
                $score = $score + 5;
            }
            if ($card['cardCode'] == 'CLUB_11') {
                $score = $score + 2;
            }
        }
        return $score;
    }


    /**
     * Get the player who starts the current round
     *
     * @param array $playerIds
     * @return int
     */
    public function getPlayerWhoStarts(array $playerIds) :int
    {
        $playerWhoStarts = $this->getSessionVar('playerWhoStarts');
        if ($playerWhoStarts) {
            $playerIndex = array_search($playerWhoStarts, $playerIds) + 1;
            if($playerIndex == count($playerIds) ) $playerIndex = 0;
            $playerWhoStarts = $playerIds[$playerIndex];
        } else {
            // for testing we dont use random values
            $playerWhoStarts = (PHP_SAPI === 'cli') ? current($playerIds) : random_int(1,4);
        }
        $this->setSessionVar('playerWhoStarts', $playerWhoStarts);
        return $playerWhoStarts;
    }


    /**
     * Set Session variable
     *
     * In unit tests we use a member variable instead of $_SESSION
     *
     * @param string $variableName
     * @param $value
     */
    private function setSessionVar(string $variableName, $value) : void
    {
        if (PHP_SAPI === 'cli')
        {
            self::$cliSession[$variableName] = (string)$value;
        }
        else {
            $_SESSION[$variableName] = (string)$value;
        }
    }


    /**
     * Get Session variable
     *
     * In unit tests we use a member variable instead of $_SESSION
     *
     * @param string $variableName
     * @return string
     */
    private function getSessionVar(string $variableName) : string
    {
        if (PHP_SAPI === 'cli')
        {
           return isset(self::$cliSession[$variableName]) ? self::$cliSession[$variableName] : '';
        }
        else {
            return isset($_SESSION[$variableName])  ? $_SESSION[$variableName] : '';
        }
    }


    /**
    * Is game over
    *
    * The game is over when the total score of all players is bigger than 50
    *
    * @param array $players
    * @return bool
    */
    public function isGameOver(array $players) :bool
    {
        $scores = array_sum(
            array_map(
                function ($player) {
                 return $player->getScore();
                },
                $players
            )
        );
        return $scores > 50;
    }

    /**
     * Check if it time to deal the cards again
     *
     * If any the players has no more cards in their hands it is true
     *
     * @param array $
     * @return bool
     */
    public function isNewDeal(array $players) : bool
    {
       foreach ($players as $player) {
           if (!count($player->getCards())) {
               return true;
           }
       }
       return false;
    }

    
    /**
     * Get the current ranking
     *
     * @param array $players
     * @return array
     */
    public function getRanking(array $players) : array
    {
        usort(
            $players,
            function ($a, $b) {
                $aScore= $a->getScore();
                $bScore= $b->getScore();

                if ($aScore == $bScore) {
                    return 0;
                }
                return ($aScore > $bScore) ? -1 : 1;
            }
        );

        return $players;
    }
}