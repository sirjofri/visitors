<?php
/*
sample home file
*/
session_start(); //recommended to work with sessions to prevent doubles. for testing it's better to directly start ./?site=count
if(!isset($_SESSION['counted']))
{
	include "./index.php?site=count";
	$_SESSION['counted']="true";
}
?>