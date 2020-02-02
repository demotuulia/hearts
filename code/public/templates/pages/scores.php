 ===============================
<br>
Scores:

<?php foreach ($templateParams['ranking'] as $player) :?>
    <br>
    <?php echo($player->getName()) ?>
    <?php echo($player->getScore()) ?>

<?php endforeach;?>

<br>
===============================
 <h3>
 &hearts;: 1 point<br>
 &spades;J: 2 points<br>
 &#9670Q: 5 points<br>

 </h3>
