<?php
/*
output: image of time-statistics as PNG
*/

header('Content-Type: image/png');

include "./dbconnect.inc";

$db=mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname);
$result=mysqli_query($db,"SELECT * FROM statistics_time;");
$gesamt=0;
while($ergebnis=mysqli_fetch_array($result))
{
	$gesamt=$gesamt+$ergebnis['number'];
}


$im=@imagecreatetruecolor(350,500) or die("Cannot initialize new GD image stream");
imagesavealpha($im,true);
$fill_color=imagecolorallocatealpha($im, 0,0,0,127);
imagefill($im,0,0,$fill_color);
$text_color=imagecolorallocate($im, 0,0,0);
if($gesamt!=0)
{
$begin=5;
imagestring($im,2,10,$begin,"Total: ".$gesamt,$text_color);
$wood_color=imagecolorallocate($im,0,0,0);
imagefilledrectangle($im,140,$begin+1,100*2+140,$begin+10,$wood_color);
$result=mysqli_query($db,"SELECT * FROM statistics_time;");
while($ergebnis=mysqli_fetch_array($result))
{
	$begin=$begin+20;
	imagefilledrectangle($im,140,$begin,($ergebnis['number']/$gesamt*100*2+140),$begin+10,$wood_color);
	imagestring($im,2,10,$begin,($ergebnis['time']<10?"0".$ergebnis['time']:$ergebnis['time'])."-".(($ergebnis['time']+1)<10?"0".($ergebnis['time']+1):($ergebnis['time']+1))." Uhr:   ".round($ergebnis['number']/$gesamt*100,2)."%",$text_color);
}
} else {
imagestring($im,2,10,5,"ERROR: Division by 0",$text_color);
imagestring($im,2,10,25,"Maybe there is no entry yet :-(",$text_color);
}
imagepng($im);
imagedestroy($im);
?>