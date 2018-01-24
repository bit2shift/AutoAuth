<?php
namespace autoauth;

$config = require('config.php');
$layout = new layout\Page("pages/index.{$config->hook->userRole()}.xml");

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
