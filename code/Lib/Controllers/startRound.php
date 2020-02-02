<?php
/**
 * A controller to start a new round. Here the first player sets his random card to the table
 *
 *  Note: with my English I use term 'round' for one session to to put the cards on
 *  the table. (First the random start card by one player and the the rest 3 cards after
 *  the rules of this game)
 */
declare(strict_types = 1);

namespace Lib\Controllers;

use Lib\Controllers\baseController;

final class startRound extends  baseController
{
    /**
     * Action
     *
     * @return array
     */
    public function action() :array
    {
        return parent::templateVars(
            [
                'playerToStart' => $this->rules->getPlayerWhoStarts(array_keys($this->players)),
                'template'  => 'startRound.php'
            ]
        );
    }
}
