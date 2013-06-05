<?php
/*
i think i should write a help-function to show the user advanced features
*/

include "./vconfig.inc";
include "./dbconnect.inc";

if(@$_GET['site']!="")
{
	if($_GET['site']=="timestatimage")
	{
///////////////////////////////////////////////////////////////////////
//             BEGIN IMAGE                                           //
///////////////////////////////////////////////////////////////////////
header('Content-Type: image/png');

include "./dbconnect.inc";

$db=mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname);
$result=mysqli_query($db,"SELECT * FROM statistics_time;") or die;
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
///////////////////////////////////////////////////////////////////////////
//                 END   IMAGE                                           //
///////////////////////////////////////////////////////////////////////////
	} elseif(($_GET['site']=="init" || $_GET['site']=="reset") && $save==false)
	{
/*******************************
initialize the database
*******************************/

//Check if database exists
$db=mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname) or die("<b>Error:</b> Cannot connect to database <i>".$dbname."</i> on line 8!<br>");
echo "Connected to database: <i>".$dbname."</i><br>";

//check if table 'statistics' exists and destroy if necessary
if(mysqli_fetch_array(mysqli_query($db,"SHOW TABLES WHERE Tables_in_".$dbname."='statistics';")))
{
	//destroy it
	mysqli_query($db,"DROP TABLE 'statistics';");
	echo "Table 'statistics' exists: <i>destroy</i><br>";
}
//create new table 'statistics'
mysqli_query($db,"CREATE TABLE statistics ( id INT(11) AUTO_INCREMENT PRIMARY KEY, date INT(8), number INT(11) );");
echo "Table 'statistics': <i>created</i><br>";

//check if table 'statistics_time' exists and destroy if necessary
if(mysqli_fetch_array(mysqli_query($db,"SHOW TABLES WHERE Tables_in_".$dbname."='statistics_time';")))
{
	//destroy it
	mysqli_query($db,"DROP TABLE 'statistics_time';");
	echo "Table 'statistics_time' exists: <i>destroy</i><br>";
}
//create new table 'statistics_time'
mysqli_query($db,"CREATE TABLE statistics_time ( id INT(11) AUTO_INCREMENT PRIMARY KEY, time INT(2), number INT(11) );");
echo "Table 'statistics_time': <i>created</i><br>";

//filling table 'statistics_time'
echo "Filling table 'statistics_time': <i>";
for($i=0;$i<24;$i++)
{
	mysqli_query($db,"INSERT INTO statistics_time (id,time,number) VALUES (NULL,'".$i."','0');");
	echo $i.", ";
}
echo "ready!</i><br>";

echo "<b>Your system is ready to count!</b>";
////////////////////////////////////////////
// END INIT FILE                          //
////////////////////////////////////////////
	} elseif($_GET['site']=="count")
	{
////////////////////////////////////////////
// BEGIN COUNT FILE                       //
////////////////////////////////////////////
	$date=date(Ymd);
	$db=mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname);
	$result=mysqli_query($db,"SELECT * FROM statistics WHERE date='".$date."';");
	if($ergebnis=mysqli_fetch_array($result)) //if there is some visit yet
	{
		$number=$ergebnis['number']+1;
		mysqli_query($db,"UPDATE statistics SET number='".$visitor."' WHERE date='".$date."';");
		echo "updated day<br>";
	} else { //if there is no visit today
		mysqli_query($db,"INSERT INTO statistics (id,date,number) VALUES (NULL,'".$date."','1');");
		echo "initialized day<br>";
	}
	mysqli_close($db);
	$time=date("H");
	$db=mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname);
	$result=mysqli_query($db,"SELECT * FROM statistics_time WHERE time='".$time."';");
	if($ergebnis=mysqli_fetch_array($result)) //if there is some visit yet
	{
		$number=$ergebnis['number']+1;
		mysqli_query($db,"UPDATE statistics_time SET number='".$number."' WHERE time='".$time."';");
		echo "updated time<br>";
	} else { //if there is no initial visit yet
		mysqli_query($db,"INSERT INTO statistics_time (id,time,number) VALUES (NULL,'".$time."','1');");
		echo "initialized time<br>";
	}
	mysqli_close($db);

/////////////////////////////////////////////
// END COUNT FILE                          //
/////////////////////////////////////////////
	} elseif($_GET['site']="validate")
	{
		echo "Information:<br><p>To validate you have to copy or save the source and paste it into a validator</p><p>it is not possible to validate the whole system with referer data!</p>";
	}
} else {

session_start();
if(@$_SESSION['id']=="")
{
$_SESSION['id']="dummy";
}
if($_SESSION['id']!="visitors")
{
	if( !isset($_POST['user']) || !isset($_POST['password']))
	{
		echo "<!DOCTYPE html>\n";
		echo "<html>\n";
		echo "<head>\n";
		echo "<meta charset=\"utf-8\">\n";
		echo "<title>visitors - login</title>\n";
		echo "<style type=\"text/html\">\n";
		echo "body { text-align:center;font-family:monospace; }\n";
		echo "#box { width:300px;margin:20px auto;padding:0px;text-align:left; }\n";
		echo ".hidden { display:none; }\n";
		echo "</style>\n";
		echo "</head>\n";
		echo "<body onload=\"document.getElementById('userbox').focus();\">\n";
		echo "<div id=\"box\">\n";
		echo "<header>\n";
		echo "<h1>visitors - login</h1>\n";
		echo "</header>\n";
		echo "<section>\n";
		echo "<header class=\"hidden\">\n";
		echo "<h1>login</h1>\n";
		echo "</header>\n";
		echo "<form action=\"./\" method=\"post\">\n";
		echo "Username: <input type=\"text\" name=\"user\" id=\"userbox\"><br>\n";
		echo "Password: <input type=\"password\" name=\"password\"><br>\n";
		echo "<input type=\"submit\" value=\"login\">\n";
		echo "</form>\n";
		echo "</section>\n";
		echo "</div>\n";
		echo "</body>\n";
		echo "</html>\n";
	} else {
		if(($_POST['user']==$user1 && $_POST['password']==$password1) || ($_POST['user']==$user2 && $_POST['password']==$password2) || ($_POST['user']==$user3 && $_POST['password']==$password3))
		{
			$_SESSION['id']="visitors";
			echo "<!DOCTYPE html>";
			echo "<html>";
			echo "<head>";
			echo "<meta charset=\"utf-8\">";
			echo "<title>Access granted</title>";
			echo "</head>";
			echo "<body onload=\"location.reload();\">";
			echo "<div id=\"box\">";
			echo "<header>";
			echo "<h1>Access granted</h1>";
			echo "</header>";
			echo "<section>";
			echo "<p>Please reload the page or click <a href=\"javascript:location.reload()\">here</a>.</p>";
			echo "</section>";
			echo "</div>";
			echo "</body>";
			echo "</html>";
		}
	}

} else {

$db=mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname);

$result=mysqli_query($db,"SELECT * FROM statistics;");

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<meta charset=\"utf-8\">";
echo "<title>visitors</title>";
echo "<style type=\"text/css\">";
echo "body { font-family:monospace;text-align:center; }";
echo "td { font-size:14px; }";
echo "tr > .week { width:50px;text-align:right;padding-right:5px;border:solid;border-width:1px 0px 0px 1px; }";
echo "tr > .day { width:200px;text-align:right;padding-right:5px;border:solid;border-width:1px 0px 0px 1px; }";
echo "tr > .result { text-align:left;padding-left:5px;width:100px;border:solid;border-width:1px 1px 0px 1px; }";
echo "#box { width:350px;margin:20px auto;padding:0px;text-align:left; }";
echo "img { border:solid;border-width:1px; }";
echo "#sidebar { position:fixed;right:50px;top:50px;text-align:right;border:solid;border-width:1px 1px 0px 1px;padding-right:10px; } ";
echo "#sidebar > ul, #sidebar>ul>li { list-style-type:none; }";
echo "#sidebar > ul>li>a, #infobox a { text-decoration:none;color:#000; }";
echo "#sidebar>ul>li>a:hover, #infobox a:hover { text-decoration:underline;cursor:pointer; }";
echo "#sidebar>img { border:none; }";
echo "#space {height:100px;}";
echo "footer {position:fixed;margin:0px;bottom:0px;width:400px;left:50%;margin-left:-200px;text-align:center;background-color:#fff;border:solid;border-width:1px 1px 0px 1px; }";
echo "#infobox { position:fixed;left:0px;top:0px;height:100%;width:100%;background-color:rgba(0,0,0,0.5);display:none; }";
echo ".hiddenbox { position:fixed;left:50%;top:30%;background-color:#fff;border:solid;border-width:1px;border-color:#000;padding:20px; }";
echo "#helpbox { width:400px;height:400px;margin-left:-200px;margin-top:-200px; }";
echo "#informationbox { width:400px;height:400px;margin-left:-200px;margin-top:-200px; }";
echo ".boxcloser { text-align:center; }";
echo "</style>";
echo "<script type=\"text/javascript\"><!--\n";
echo "timeout=null;";
echo "function scroll_to(element)\n";
echo "{";
echo "clearTimeout(timeout);";
echo "var from=0;";
echo "if(window.pageYOffset) {";
echo "from=window.pageYOffset; } else if(document.body && document.body.scrollTop) {";
echo "from=document.body.scrollTop;";
echo "}";
echo "if(from>element.offsetTop) {";
echo "window.scrollBy(0,-2);\n";
echo "timeout=setTimeout(scroll_to,1,element);\n";
echo "}";
echo "if(from==element.offsetTop+1 || from==element.offsetTop-1)";
echo "{ clearTimeout(timeout);element.scrollIntoView();}";
echo "if(from<element.offsetTop) {";
echo "window.scrollBy(0,2);\n";
echo "timeout=setTimeout(scroll_to,1,element);\n";
echo "}";
echo "}";
echo "function show_help()";
echo "{";
echo "document.getElementById(\"informationbox\").style.display=\"none\";";
echo "document.getElementById(\"infobox\").style.display=\"block\";";
echo "document.getElementById(\"helpbox\").style.display=\"block\";";
echo "}";
echo "function hide_help()";
echo "{";
echo "document.getElementById(\"helpbox\").style.display=\"none\";";
echo "document.getElementById(\"infobox\").style.display=\"none\";";
echo "}";
echo "function show_info(id)";
echo "{";
echo "var info=\"\";";
echo "if(id==\"validate\")";
echo "{ info=\"To validate you have to copy or save the source and paste it into a validator.<br><br>It is not possible to validate the whole system with referer data!\"; }";
echo "document.getElementById(\"informationp\").innerHTML=info;";
echo "document.getElementById(\"infobox\").style.display=\"block\";";
echo "document.getElementById(\"helpbox\").style.display=\"none\";";
echo "document.getElementById(\"informationbox\").style.display=\"block\";";
echo "}";
echo "function hide_info()";
echo "{";
echo "document.getElementById(\"informationbox\").style.display=\"none\";";
echo "document.getElementById(\"infobox\").style.display=\"none\";";
echo "}";
echo "//--></script>";
echo "</head>";
echo "<body>";
echo "<div id=\"box\">";
echo "<header>";
echo "<h1>visitors</h1>";
echo "</header>";
echo "<section id=\"daily\">";
echo "<header><h2>daily statistics</h2></header>";
echo "<table>";
echo "<tr>";
echo "<td class=\"week\"><b>Week</b></td><td class=\"day\"><b>Date</b></td><td class=\"result\"><b>Number</b></td>";
echo "</tr>";

while($ergebnis=mysqli_fetch_array($result)) //if there is a visit
{
$inputdate=$ergebnis['date'];
$datestamp=strtotime($inputdate);
$day=date('w',$datestamp);
switch($day) //Replace them with your language
{
	case 0:
		$day="Sunday";
		break;
	case 1:
		$day="Monday";
		break;
	case 2:
		$day="Tuesday";
		break;
	case 3:
		$day="Wednesday";
		break;
	case 4:
		$day="Thursday";
		break;
	case 5:
		$day="Friday";
		break;
	case 6:
		$day="Saturday";
		break;
}
$dateparted=str_split($inputdate); // e.g. 20130102
$dateparsed=$dateparted[6].$dateparted[7].".".$dateparted[4].$dateparted[5].".".$dateparted[0].$dateparted[1].$dateparted[2].$dateparted[3];
echo "<tr><td class=\"week\">".date('W',$datestamp)."</td><td class=\"day\">".($ergebnis['number']>=$a?"<b>".$day:$day).", ".($ergebnis['number']>=$a?$dateparsed."</b>":$dateparsed)."</td><td class=\"result\">".($ergebnis['number']>=$a?"<b>".$ergebnis['number']."</b>":$ergebnis['number'])."</td></tr>";
}

echo "</table>";
echo "</section>";
echo "<section id=\"hourly\">";
echo "<header><h2>hourly statistics</h2></header>";
echo "<img src=\"./?site=timestatimage\" alt=\"[IMG]\">";
echo "</section>";
echo "<section id=\"space\">";
echo "</section>";
echo "<div id=\"infobox\">";
echo "<section id=\"helpbox\" class=\"hiddenbox\">";
echo "<header><h2>help</h2></header>";
echo "<p>help section - [wip]</p>";
echo "<p class=\"boxcloser\"><a onclick=\"hide_help();\">Close</a></p>";
echo "</section>";
echo "<section id=\"informationbox\" class=\"hiddenbox\">";
echo "<header><h2>Information</h2></header>";
echo "<p id=\"informationp\"></p>";
echo "<p class=\"boxcloser\"><a onclick=\"hide_info();\">Close</a></p>";
echo "</section>";
echo "</div>";
echo "</div>";
echo "<aside>";
echo "<section id=\"sidebar\">";
echo "<header><h2>Contents</h2></header>";
echo "<ul>";
//echo "<li><a href=\"javascript:document.getElementById('box').scrollIntoView(true);\">top</a></li>"; //fast version
echo "<li><a onclick=\"scroll_to(document.getElementById('box'));\" ondblclick=\"document.getElementById('box').scrollIntoView(true);\">top</a></li>";
//echo "<li><a href=\"javascript:document.getElementById('daily').scrollIntoView(true);\">daily statistics</a></li>";
echo "<li><a onclick=\"scroll_to(document.getElementById('daily'));\" ondblclick=\"document.getElementById('daily').scrollIntoView(true);\">daily statistics</a></li>";
//echo "<li><a href=\"javascript:document.getElementById('hourly').scrollIntoView(true);\">hourly statistics</a></li>";
echo "<li><a onclick=\"scroll_to(document.getElementById('hourly'));\" ondblclick=\"document.getElementById('hourly').scrollIntoView(true);\">hourly statistics</a></li>";
echo "<li><a onclick=\"show_help();\">help</a></li>";
echo "</ul>";
//echo "<br><br><a href=\"http://jigsaw.w3.org/css-validator/check/referer\"><img style=\"border:0;width:88px;height:31px;\" src=\"http://jigsaw.w3.org/css-validator/images/vcss\" alt=\"Valid CSS!\"></a>";
//echo "<img src=\"http://www.w3.org/html/logo/badge/html5-badge-v-css3-performance-semantics.png\" width=\"38\" height=\"170\" alt=\"HTML5 Powered with CSS3 / Styling, Performance &amp; Integration, and Semantics\" title=\"HTML5 Powered with CSS3 / Styling, Performance &amp; Integration, and Semantics\" ondblclick=\"window.open('http://jigsaw.w3.org/css-validator/check/referer','_blank');\" onclick=\"location.href='http://validator.w3.org/check?uri=referer';\">";
echo "<img src=\"http://www.w3.org/html/logo/badge/html5-badge-v-css3-performance-semantics.png\" width=\"38\" height=\"170\" alt=\"HTML5 Powered with CSS3 / Styling, Performance &amp; Integration, and Semantics\" title=\"HTML5 Powered with CSS3 / Styling, Performance &amp; Integration, and Semantics\" onclick=\"show_info('validate');\">";
echo "</section>";
echo "</aside>";
echo "<footer>";
echo "<p>Latest update: ".date("d.m.Y H:i:s")."</p>";
echo "</footer>";
echo "</body>";
echo "</html>";

mysqli_close($db);
}
}

?>