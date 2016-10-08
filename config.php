<?php
@ini_set("session.hash_function","sha512");
@session_start();

$debug = TRUE;

if ($debug === TRUE) {
	error_reporting(E_ALL);
	ini_set("display_errors","On");
} elseif ($debug === FALSE) {
	error_reporting(0);
	ini_set("display_errors","Off");
} else {
	die;
}

$db = array();
$db['host'] = "localhost";
$db['user'] = "root";
$db['pass'] = "";
$db['data'] = "mijnwesel";

$con = new mysqli($db['host'],$db['user'],$db['pass'],$db['data']);

//require_once ("class/");
//$class = new Class();

require_once ("class/user.class.php");
$user = new user($con);
$session = new session($con);

/*

var_dump($user->add("koen","koen","admin@admin.nl"));
echo "<br>";
var_dump($user->login("koen","koen"));
echo "<br>";
var_dump($session->check($_SESSION['sid'],$_SESSION['uid']));
echo "<br>";
var_dump($session->destroy($_SESSION['sid']));
echo "<br>";
var_dump($session->check($_SESSION['sid'],$_SESSION['uid']));

*/

//echo $user->add("","","");
?>