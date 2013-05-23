<?php

include "./dbconnect.inc";

ini_set("display_errors","1");

//session_start(); //recommended to work with sessions to prevent doubles. for testing it's better without
//if(!isset($_SESSION['counted']))
//{
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
//	$_SESSION['counted']="true";
//}
?>