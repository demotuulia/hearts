<?php
    $playerId = $player->getId();
    $cardSetToTable = (isset($templateParams['cardsSetToTable'][$playerId]))
        ? $templateParams['cardsSetToTable'][$playerId] : 's';
    $noValidMoves = $templateParams['noValidMoves'];
?>

<h1>
<?php foreach ($player->getCards() as $code =>$htmlCode) :?>
    <?php $checked = ($cardSetToTable == $code) ? 'checked' : ''; ?>
    <input type="radio" name="player[<?php echo($playerId) ?>]" value="<?php echo($code) ?>" <?php echo($checked) ?>>
    <?php echo($htmlCode) ?>&nbsp;

<?php endforeach;?>
</h1>
<?php if( isset($noValidMoves[$playerId])) :?>
  <span style="color: red"> Not a valid move</span>
<?php endif;?>
