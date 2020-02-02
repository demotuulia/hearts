<h1><?php echo ($templateParams['cardsOnTheTableStr']); ?></h1>
<h3>

<br><br>
Looser: <?php echo $templateParams['looserName']?><br>
Score:  <?php echo $templateParams['score'] ?><br>

<br>
<form method="post">
    <input type="hidden" name="controller" value = "startRound">
    <input type="submit" value="Next Round">
</form>

