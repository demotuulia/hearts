<?php
/**
 * A controller to reset the game
 *
 */

declare(strict_types = 1);

namespace Lib\Controllers;

use Lib\Models\rules;
use Lib\Controllers\baseController;
use Lib\Models\player;
use Lib\Controllers\startGame;

final class resetGame extends  baseController
{
    /**
     * Action
     *
     * @return array
     */
    public function action(): array
    {
        player::clearPlayers();
        $startGame = new startGame();
        return $startGame->action();
    }
}