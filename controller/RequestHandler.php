<?php
require_once "InputHandler.php";

if (isset($_POST['data']))
{
	$postData = json_decode($_POST['data'], true);

	$action = $postData['action'];

	$result = InputHandler::handle($action, $postData);

	if (is_bool($result))
	{
		echo $result === true ? 'true' : 'false';
	}
	else
	{
		echo $result;
	}
}