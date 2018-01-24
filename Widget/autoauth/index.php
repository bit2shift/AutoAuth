<?php
namespace autoauth;

$config = require('config.php');

$class = new \ReflectionClass($config->hook->class);
if(!$class->isSubclassOf(hooks\IHook::class))
{
	http_response_code(500);
	die("'{$config->hook->class}' is not a valid hook, it needs to implement '" . hooks\IHook::class . "'.");
}

$hook = $class->newInstanceArgs($config->hook->args);

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	switch($hook->userRole())
	{
	case 'admin':
		$name = $_POST['name'];
		break;

	case 'user':
		$name = $hook->userName();
		break;
	}
}

$layout = new layout\Page("pages/index.{$hook->userRole()}.xml");

if(isset($name))
{
	$out = $layout->{'//html:input[@readonly]'}->item(0);

	if(!strlen($name))
		$out->setAttribute('value', 'Username cannot be empty!');
	else
	{
		//TODO
		$out->setAttribute('value', "UUID for '$name'");
	}
}

$layout->display();
