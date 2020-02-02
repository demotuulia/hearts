<?php
/**
 * A controller when the game is over
 *
 */

declare(strict_types = 1);

namespace Lib\Controllers;

use Lib\Models\player;
use Lib\Controllers\baseController;

final class gameOver extends  baseController
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
                'template'  => 'gameOver.php'
            ]
        );
    }
}
