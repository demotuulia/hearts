<form method="post">
    <input type="hidden" name = "controller" value="resetGame">
    <input type="submit" value="Reset game">
</form>

<?php include ( 'templates/pages/scores.php'); ?>

<?php include ( 'templates/pages/'. $templateParams['template']); ?>
