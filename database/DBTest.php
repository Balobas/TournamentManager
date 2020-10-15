<?php
//
//$connection = mysqli_init();
//$host = 'localhost';
//$username = 'php';
//$password = 'php';
//$dbName = 'championships';
//$connectionResult = $connection->real_connect($host, $username, $password, $dbName);
//
//if (!$connectionResult)
//{
//	$error = $connection->connect_errno.":".$connection->connect_error;
//	trigger_error($error, E_USER_ERROR);
//}
//
//$query = "select * from tournament where ID = 6";
//$result = $connection->query($query);
//var_export($result->fetch_assoc());

if (isset($_POST['action']))
{
	print_r(($_POST['data']));
}