<?php
// require_once "/www/groups/d/de_comp353_4";
//track everytime login
session_start();

$_SESSION['logInFail'] = "";
//initialize vars

//should have it as 'localhost:3306'

// $server = "dec353.encs.concordia.ca";
// $username = "dec353_4";
// $password = "group353";
// $database = "dec353_4";

$server = 'localhost';
$username = 'root';
$password = '';
$database = 'comp353';

//ex: z_admins and Z_users each have the stuff
//security not goal - just connect w it


try
{
	$db = new PDO("mysql:host=$server;dbname=$database;",$username, $password);
	// foreach($db->query("SELECT * FROM user") as $row) {
 //        print_r($row);
    //}
}
catch(PDOException $e)
{
	die('Could not connect to database: ' . $e->getMessage());
}



?>