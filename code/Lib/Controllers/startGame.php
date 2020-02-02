<?php
/**
 * A controller ot start a new game
 *
 */

declare(strict_types = 1);

namespace Lib\Controllers;

use Lib\Models\player;
use Lib\Controllers\baseController;

final class startGame extends  baseController
{
    /**
     * Action
     *
     * @return array
     */
    public function action() :array
    {
        $playersData = [
            ['id' => 1 , 'name' => 'Jan'],
            ['id' => 2 , 'name' => 'Otto'],
            ['id' => 3 , 'name' => 'Jane'],
            ['id' => 4 , 'name' => 'John'],
        ];

        foreach ($playersData as $player) {
            $players[$player['id']] = new player($player['id'], $player['name']);
        }
        $this->cards->shuffle();
        $this->cards->dealCardsToPlayerObjects($players);
        $this->players = $players;

        return parent::templateVars(
            [
                'playerToStart' => $this->rules->getPlayerWhoStarts(array_keys($players)),
                'template'  => 'startRound.php'
            ]
        );
    }
}
