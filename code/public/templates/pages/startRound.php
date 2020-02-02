<?php
$playerToStart = $templateParams['playerToStart'];
$player = $templateParams['players'][$playerToStart];
?>

Start by:   <?php echo($player->getName()) ?> <br>
    Cards:
<form method="post">
    <input type="hidden" name="controller" value = "round">
    <?php include 'form/row.php' ?>
    <input type="submit" value="Submit">
</form>


