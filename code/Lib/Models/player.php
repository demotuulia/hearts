<?php
/**
 * A class to hold the data of one player like name, score and current cards in the hand
 *
 */

declare(strict_types=1);

namespace Lib\Models;

final class player {


    /**
     * @var int
     */
    private $id;


    /**
     * @var array
     */
    private $cards = [];


    /**
     * @var int
     */
    private $score = 0;


    /**s
     * @var string
     */
    private $name = '';


    /**
     * player constructor.
     *
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }


    /**
     * Get name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }


    /**
     * Get id
     *
     * @param  int
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }


    /**
     * Set cards to the hand when dealing them
     *
     * @param array $cards
     */
    public function setCards(array $cards) :void
    {
        $this->cards = $cards;
    }


    /**
     * Get cards all  in hand
     *
     * @param array $cards
     */
    public function getCards() :array
    {
        return $this->cards;
    }


    /**
     * Get a card from the player stack to set to the table
     *
     * @param string $code
     */
    public function takeCard(string $code) : void
    {
        if (isset($this->cards[$code])) {
            unset($this->cards[$code]);
        }
    }


    /**
     * Get score
     *
     * @return int
     */
    public function getScore() :int
    {
        return $this->score;
    }


    /**
     * Set more score points
     *
     * @param int $score
     */
    public function setScore(int $score) : void
    {
        $this->score=  $this->score + $score;
    }


    /**
    * Convert all player data to a json string
    *
    * This is needed to create one session variable
    *
    * @param array $players
    * @return string
    */
    public static function jsonEncode(array $players) : string
    {
            $playersArr = [];
            foreach ($players as $player) {
                $playersArr[] = [
                    'id' => $player->getId() ,
                    'name' => $player->getName(),
                    'score' => $player->getScore(),
                    'cards' => $player->getCards(),
                ];
            }
            return json_encode($playersArr);
    }


    /**
     * Convert json string to player array
     *
     * This is needed to create one session variable
     *
     * @param string $playersJson
     * @return array
     */
    public static function jsonDecode(string $playersJson) : array
    {
        $playersArr = json_decode($playersJson, true);

        $playersObjectArr = [];
        foreach ($playersArr as $player) {
            $playerObj  = new player($player['id'], $player['name']);
            $playerObj->setCards($player['cards']);
            $playerObj->setScore($player['score'] );
            $playersObjectArr[$player['id']] = $playerObj;
        }
        return $playersObjectArr;
    }


    /**
     * Write to session
     *
     * All player dat is written to session before opening a new page
     *
     * @param array $players
     */
    public static function writeToSession(array $players) : void
    {
        $_SESSION['players'] = player::jsonEncode($players);
    }


    /**
     * Read from session
     *
     * @return array
     */
    public static function readFromSession() :array
    {
        return isset($_SESSION['players']) ? player::jsonDecode($_SESSION['players']) : [];
    }


    /**
     *  Clean up all user data to start a new game
     *
     */
    public static function clearPlayers() : void
    {
        unset( $_SESSION['players']);
    }
}
