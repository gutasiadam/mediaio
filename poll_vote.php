<?php
$vote = $_REQUEST['vote'];

//get content of textfile
$filename = "poll_result.txt";
$content = file($filename);

//put content in array
$array = explode("||", $content[0]);
$one = $array[0];
$two = $array[1];
$three = $array[2];
$four = $array[3];
$five = $array[4];

if ($vote == 0) {
  $one = $one + 1;
}
if ($vote == 1) {
  $two = $two + 1;
}
if ($vote == 2) {
  $three = $three + 1;
}
if ($vote == 3) {
  $four = $four + 1;
}
if ($vote == 4) {
  $five = $five + 1;
}


//insert votes to txt file
$insertvote = $one."||".$two."||".$three."||".$four."||".$five;
$fp = fopen($filename,"w");
fputs($fp,$insertvote);
fclose($fp);
$sum= $one+$two+$three+$four+$five
?>

<div id="poll2" >
<h5>Eredmény:</h5>
<h3 style="border 2px black">{Szavazás témája} - <?php echo($sum); ?>db</h3>
<td>{one}</td>
<div class="progress">
  <div class="progress-bar" role="progressbar" style="width :<?php echo(100*round($one/$sum,2)); ?>%" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"><?php echo(100*round($one/$sum,2))."%   (".$one.")"; ?></div>
</div>
<td>{two}</td>
<td>
<div class="progress">
  <div class="progress-bar" role="progressbar" style="width: <?php echo(100*round($two/$sum,2)); ?>%" aria-valuenow='' aria-valuemin="0" aria-valuemax="100"><?php echo(100*round($two/$sum,2))."%  (".$two.")"; ?>%</div>
</div>
<td>{three}</td>
<div class="progress">
  <div class="progress-bar" role="progressbar" style="width :<?php echo(100*round($three/$sum,2)); ?>%" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"><?php echo(100*round($three/$sum,2))."%   (".$three.")"; ?></div>
</div>
<td>{four}</td>
<div class="progress">
  <div class="progress-bar" role="progressbar" style="width :<?php echo(100*round($four/$sum,2)); ?>%" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"><?php echo(100*round($four/$sum,2))."%   (".$four.")"; ?></div>
</div>
<td>{five}</td>
<div class="progress">
  <div class="progress-bar" role="progressbar" style="width :<?php echo(100*round($five/$sum,2)); ?>%" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"><?php echo(100*round($five/$sum,2))."%   (".$five.")"; ?></div>
</div>
</td>
</tr>
</form>

</body>
</html>

<style>
#poll{
  width: 15%;
  text-align: center;
  margin: 0 auto; 
  border: 2px solid black;
}
</style>