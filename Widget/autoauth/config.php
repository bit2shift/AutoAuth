<?php
spl_autoload_register
(
	function($class)
	{
		require_once(str_replace('\\', DIRECTORY_SEPARATOR, "$class.php"));
	}
);

// Replace 'SMFHook' with the appropriate class from 'hooks\'
return new hooks\SMFHook('..');
