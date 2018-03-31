<?php
$config = require('config.php');

if(!$config->hook instanceof autoauth\hook\IHook)
{
	http_response_code(500);
	die(sprintf('"%s" is not a valid hook, it does not implement "%s".', get_class($config->hook), autoauth\hook\IHook::class));
}

$layout = new autoauth\layout\Page("pages/index.{$config->hook->userRole()}.xml");

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$name = $config->hook->userName();

	if($config->hook->userRole() === 'admin')
	{
		$name = $_POST['name'] ?: $name;
		$layout->{'//html:input[@maxlength]'}->item(0)->setAttribute('value', $name);
	}

	if(strlen($name))
	{
		//TODO
		$layout->{'//html:input[@readonly]'}->item(0)->setAttribute('value', "UUID for '$name'");
	}
}

$layout->display();
