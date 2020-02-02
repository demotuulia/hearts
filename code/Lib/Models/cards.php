<?php
/**
 * A class to hold the data and the business rules of the card stack
 *
 *
 */

declare(strict_types=1);

namespace Lib\Models;

final class cards {

    /**
     * All cards
     *
     * @var array
     */
    private $cardDeck = [];


    /**
     * Card symbols as code amd html code
     *
     * We use 2 types of code
     * code for the program to recognize the cards
     * and code to render them on the screen
     *
     * @var array
     */
    private $symbols = [
        'HEART' => ['code' =>'&hearts;'],
        'CLUB' => ['code' =>'&clubs;'],
        'DIAMOND' => ['code' =>'&#9670;'],
        'SPADE'=> ['code' =>'&spades;'],
    ];


    /**
     * cards constructor.
     *
     */
    public function __construct()
    {
        $this->defineCardDeck();
    }


    /**
     * Define card deck, all cards with their indexes and symbols
     *
     * Like: HEART_7, CLUB_8, SPADE_13, DIAMOND_14
     */
    private function defineCardDeck() : void
    {
        foreach (array_keys($this->symbols)  as $symbol) {
            $symbolDeck = array_map(
                function ($index) use ($symbol) {
                    return $symbol . '_' . $index;
                },
                range(7, 14)
            );
            $this->cardDeck = array_merge($this->cardDeck, $symbolDeck);
        }
    }


    /**
     * Get card in the stack by  index in html format
     *
     * @param int $index
     * @return string
     */
    public function getCardByIndex(int $index, $html = false) : string
    {
        $code =  $this->cardDeck[$index];
        if ($html) {
            $code = $this->getHtmlSymbol($code);
        }
        return $code;
    }


    /**
     * Get convert the card code to a html symbol
     *
     * @param string $code
     * @return string
     */
    public function getHtmlSymbol(string $code) : string
    {
        list($symbol, $index) = explode('_' , $code);
        switch ($index) {
            case  11 : {
                $index = 'J';
                break;
            }
            case  11 : {
                $index = 'Q';
                break;
            }
            case  13 : {
                $index = 'K';
                break;
            }
            case  14 : {
                $index = 'A';
                break;
            }
        }
       return $this->symbols[$symbol]['code'] . $index;
    }


    /**
     * Get Html symbols from codes
     *
     *
     * @param array $codes
     * @return array
     */
    public function getHtmlSymbols(array $codes) :array
    {
       $htmlCodes = [];
       foreach ($codes as $code) {
           $htmlCodes[$code] =  $this->getHtmlSymbol($code);
       }

       return $htmlCodes;
    }


    /**
     * Shuffle
     *
     */
    public function shuffle() : void
    {
        shuffle($this->cardDeck);
    }


    /**
     * Get stack
     *
     * @return array
     */
    public function getStack() :array
    {
        return $this->cardDeck;
    }

    /**
     * Deal the cards to the players
     *
     * @param array $playerIds
     * @return array
     */
    public function dealCards(array $playerIds) : array
    {
        $playerIndex = 0;
        $deal = [];
        for ($sliceIndex = 0; $sliceIndex <= 21; $sliceIndex = $sliceIndex + 7) {
            $slice = array_slice($this->cardDeck, $sliceIndex, 7);
            $deal[$playerIds[$playerIndex]] = ['codes' =>  $this->getHtmlSymbols($slice)];
            $playerIndex ++;
        }

        return $deal;
    }


    /**
     * Deal cards to player objects
     *
     * @param array $players
     */
    public function dealCardsToPlayerObjects(array &$players) : void
    {
        $deal = $this->dealCards(array_keys($players));
        foreach ($deal as $id => $cards) {
            $players[$id]->setCards($cards['codes']);
        }
    }
}