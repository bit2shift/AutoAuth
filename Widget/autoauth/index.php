<?php
$hook = require('config.php');

$layout = new layout\Page("pages/index.{$hook->userRole()}.xml");

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

	default:
		return;
	}

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
