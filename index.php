<?php
/*
i think i should write a help-function to show the user advanced features
*/
include "./vusers.inc";

$a=10;

include "./dbconnect.inc";

session_start();
if($_SESSION['id']!="visitors")
{
	if( !(isset($_POST['user'])&& isset($_POST['password'])))
	{
		echo "<!DOCTYPE html>\n";
		echo "<html>\n";
		echo "<head>\n";
		echo "<meta charset=\"utf-8\">\n";
		echo "<title>Visitors - login</title>\n";
		echo "<style type=\"text/html\">\n";
		echo "body { text-align:center;font-family:monospace; }\n";
		echo "#box { width:300px;margin:20px auto;padding:0px;text-align:left; }\n";
		echo ".hidden { display:none; }\n";
		echo "</style>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "<div id=\"box\">\n";
		echo "<header>\n";
		echo "<h1>Visitors - login</h1>\n";
		echo "</header>\n";
		echo "<section>\n";
		echo "<header class=\"hidden\">\n";
		echo "<h1>login</h1>\n";
		echo "</header>\n";
		echo "<form action=\"./\" method=\"post\">\n";
		echo "Username: <input type=\"text\" name=\"user\"><br>\n";
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
echo "#sidebar > ul>li>a { text-decoration:none;color:#000; }";
echo "#sidebar>ul>li>a:hover { text-decoration:underline;cursor:pointer; }";
echo "#sidebar>img { border:none; }";
echo "#space {height:100px;}";
echo "footer {position:fixed;margin:0px;bottom:0px;width:400px;left:50%;margin-left:-200px;text-align:center;background-color:#fff;border:solid;border-width:1px 1px 0px 1px; }";
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
echo "<tr><td class=\"week\">".date('W',$datestamp)."</td><td class=\"day\">".($ergebnis['visitor']>=$a?"<b>".$day:$day).", ".($ergebnis['visitor']>=$a?$dateparsed."</b>":$dateparsed)."</td><td class=\"result\">".($ergebnis['visitor']>=$a?"<b>".$ergebnis['visitor']."</b>":$ergebnis['visitor'])."</td></tr>";
}

echo "</table>";
echo "</section>";
echo "<section id=\"hourly\">";
echo "<header><h2>hourly statistics</h2></header>";
echo "<img src=\"./timestatimage.php\" alt=\"[IMG]\">";
echo "</section>";
echo "<section id=\"space\">";
echo "</section>";
echo "</div>";
echo "<section id=\"sidebar\">";
echo "<header><h2>Contents</h2></header>";
echo "<ul>";
//echo "<li><a href=\"javascript:document.getElementById('box').scrollIntoView(true);\">top</a></li>"; //fast version
echo "<li><a onclick=\"scroll_to(document.getElementById('box'));\" ondblclick=\"document.getElementById('box').scrollIntoView(true);\">top</a></li>";
//echo "<li><a href=\"javascript:document.getElementById('daily').scrollIntoView(true);\">daily statistics</a></li>";
echo "<li><a onclick=\"scroll_to(document.getElementById('daily'));\" ondblclick=\"document.getElementById('daily').scrollIntoView(true);\">daily statistics</a></li>";
//echo "<li><a href=\"javascript:document.getElementById('hourly').scrollIntoView(true);\">hourly statistics</a></li>";
echo "<li><a onclick=\"scroll_to(document.getElementById('hourly'));\" ondblclick=\"document.getElementById('hourly').scrollIntoView(true);\">hourly statistics</a></li>";
echo "</ul>";
//echo "<br><br><a href=\"http://jigsaw.w3.org/css-validator/check/referer\"><img style=\"border:0;width:88px;height:31px;\" src=\"http://jigsaw.w3.org/css-validator/images/vcss\" alt=\"Valid CSS!\"></a>";
echo "<img src=\"http://www.w3.org/html/logo/badge/html5-badge-v-css3-performance-semantics.png\" width=\"38\" height=\"170\" alt=\"HTML5 Powered with CSS3 / Styling, Performance &amp; Integration, and Semantics\" title=\"HTML5 Powered with CSS3 / Styling, Performance &amp; Integration, and Semantics\" ondblclick=\"window.open('http://jigsaw.w3.org/css-validator/check/referer','_blank');\" onclick=\"location.href='http://validator.w3.org/check?uri=referer';\">";
echo "</section>";
echo "<footer>";
echo "<p>Latest update: ".date("d.m.Y H:i:s")."</p>";
echo "</footer>";
echo "</body>";
echo "</html>";

mysqli_close($db);
}

?>