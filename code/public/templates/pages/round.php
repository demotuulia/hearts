<h1>Start card : <?php echo $templateParams['startCardHtml'] ?> </h1>

<form method="post">
    <input type="hidden" name="controller" value = "round">
    <input type = 'hidden' name="startCard" value="<?php echo $templateParams['startCard']?>">
    <input type = 'hidden' name="startCardPlayerId" value="<?php echo $templateParams['startCardPlayerId'] ?>">
<?php foreach ($templateParams['roundPlayers'] as $player) :?>
    <br>
    <?php echo($player->getName()) ?>
    <?php include 'form/row.php' ?>
<?php endforeach;?>
    <br>
    <input type="submit" value="Submit">
</form>
