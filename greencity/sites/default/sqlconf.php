<?php
//  OpenEMR
//  MySQL Config

$host	= 'localhost';
$port	= '3306';
$login	= 'root';
$pass	= '';
$dbase	= 'greencity';

$sql = mysql_connect($host,$login,$pass);
$con = $con = mysql_select_db($dbase);

$conn=mysqli_connect($host,$login,$pass,$dbase);

//Added ability to disable
//utf8 encoding - bm 05-2009
global $disable_utf8_flag;
$disable_utf8_flag = false;

$sqlconf = array();
global $sqlconf;
$sqlconf["host"]= $host;
$sqlconf["port"] = $port;
$sqlconf["login"] = $login;
$sqlconf["pass"] = $pass;
$sqlconf["dbase"] = $dbase;
//////////////////////////
//////////////////////////
//////////////////////////
//////DO NOT TOUCH THIS///
$config = 1; /////////////
//////////////////////////
//////////////////////////
//////////////////////////
?>
