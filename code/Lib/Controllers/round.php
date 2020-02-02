<?php
/**
 * A controller to set the cards of the rest of the players after the start card has been set
 *
 *  Note: with my English I use term 'round' for one session to to put the cards on
 *  the table. (First the random start card by one player and the the rest 3 cards after
 *  the rules of this game)
 */

declare(strict_types = 1);

namespace Lib\Controllers;

use Lib\Models\rules;
use Lib\Controllers\baseController;
use Lib\Controllers\endRound;

final class round extends  baseController
{
    /**
     * Action
     *
     * @return array
     */
    public function action() :array
    {
        if (count($_REQUEST['player']) == 1) {

            // read request from  startRound.php  template
            $startCardPlayerId = current(array_keys($_REQUEST['player']));
            $startCard =  current($_REQUEST['player']);
            $player = $this->players[$startCardPlayerId];
            $player->takeCard($startCard);

            $this->rules->setCardOnTheTable($startCard, $startCardPlayerId);
            $noValidMoves = [];
        } else  {

            // read request from round.php template
            $startCardPlayerId = (int)$_REQUEST['startCardPlayerId'];
            $startCard = $_REQUEST['startCard'];
            $this->rules->setStartCard($startCardPlayerId, $startCard);
            $noValidMoves = $this->noValidMoves();
            $cardsSetToTable =isset( $_REQUEST['player']) ?  $_REQUEST['player'] : [];

            if (empty($noValidMoves)) {
                return $this->endRound();
            }
        }

        $roundPlayers = $this->players;
        unset($roundPlayers[$startCardPlayerId]);

        return parent::templateVars(
            [
                'startCardPlayerId' => $startCardPlayerId,
                'startCard' => $startCard,
                'startCardHtml' => $this->cards->getHtmlSymbol($startCard),
                'player' => $player,
                'roundPlayers' => $roundPlayers,
                'noValidMoves' => $noValidMoves,
                'cardsSetToTable' => $cardsSetToTable,
                'template'  =>  'round.php'
            ]
        );
    }


    /**
     * Check no valid moves
     *
     * @param array $players
     * @param rules $rules
     * @return array
     */
    private function noValidMoves() :array
    {
        $cardsSetToTable =isset( $_REQUEST['player']) ?  $_REQUEST['player'] : [];
        $startCardPlayerId = $_REQUEST['startCardPlayerId'];
        $noValidMoves = [];
        foreach ($this->players as $playerId => $player) {
            if($playerId != $startCardPlayerId) {
                $isValid = true;
                if (!isset($cardsSetToTable[$playerId])) {
                    $isValid = false; // no card selected in the form
                } else {
                    $card = $cardsSetToTable[$playerId];
                    if (!$this->rules->isValidMove($card, $player->getCards())) {
                        $isValid = false;
                    }
                }
                if(!$isValid) {
                    $noValidMoves[$playerId] = $playerId;
                } else {
                    $this->rules->setCardOnTheTable($card, $playerId);
                }
            }
        }
        return $noValidMoves;
    }


    /**
     * Get the cars on the table as html string
     *
     * @return string
     */
    private function cardsOnTheTableStr(): string
    {
        $cardsOnTheTable = $this->rules->getCardsOnTheTable();
        $cardsOnTheTableStr = '';
        foreach ($cardsOnTheTable as  &$card) {
            $cardsOnTheTableStr.= $card['name'] = $this->players[$card['playerId']]->getName() . ':';
            $cardsOnTheTableStr.= $this->cards->getHtmlSymbol($card['cardCode']) . '&nbsp;&nbsp;';
        }

        return $cardsOnTheTableStr;
    }


    /**
     * End of one round
     *
     * @return array
     */
    protected function endRound() :array
    {
        $score = $this->rules->getScore();
        $looser = $this->rules->getLooser();
        $looserName = $this->players[$looser]->getName();
        $this->players[$looser]->setScore($score);

        $nextTemplate = ($this->rules->isGameOver($this->players)) ? 'gameOver' :  'round';

        return parent::templateVars(
            [
                'cardsOnTheTableStr' =>  $this->cardsOnTheTableStr(),
                'score' => $score,
                'looser' => $looser,
                'looserName' => $looserName,
                'template'  =>  'endRound.php',
                'nextTemplate' => $nextTemplate
            ]
        );
    }
}
