<?php
$config = require('config.php');

if(!$config->hook instanceof autoauth\hook\IHook)
{
	http_response_code(500);
	die(sprintf('"%s" is not a valid hook, it does not implement "%s".', get_class($config->hook), autoauth\hook\IHook::class));
}

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$name = $config->hook->userName();

	if($config->hook->userRole() === 'admin')
		$name = $_POST['name'] ?: $name;

	if(strlen($name))
	{
		//TODO
		$uuid = "UUID for '$name'";
	}
}
else
{
	$name = '';
	$uuid = '';
}

require('pages/index.xml');
