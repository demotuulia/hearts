<?php
/**
 *  Abstract base controller for all controllers
 *
 */

declare(strict_types = 1);

namespace Lib\Controllers;

use Lib\Models\cards;
use Lib\Models\player;
use Lib\Models\rules;

abstract class baseController {

    /**
     * @var cards
     */
    protected $cards;


    /**
     * @var rules
     */
    protected $rules;


    /**
     * @var array
     */
    protected $players;


    /**
     * baseController constructor.
     *
     */
    public function __construct()
    {
        $this->cards = new cards();
        $this->rules = new rules();
        $this->players = player::readFromSession();
    }


    /**
     * Return template variables
     *
     * @param array $params
     * @return array
     */
    protected function templateVars(array $params) : array
    {
        $this->writeToSession();

        return array_merge(
            $params,
            [
                'rules' => $this->rules,
                'cards' => $this->cards,
                'players' => $this->players,
                'ranking' => $this->getRanking()
            ]
        );
    }


    /**
     * Get ranking
     *
     * @return array
     */
    private function getRanking(): array
    {
        return $this->rules->getRanking($this->players);
    }


    /**
     * Write variables to session
     *
     * writeToSession
     */
    protected function writeToSession() :void
    {
        player::writeToSession($this->players);
    }


    /**
     * A function which every controller must have
     *
     * @return array
     */
    abstract function action() :array;
}