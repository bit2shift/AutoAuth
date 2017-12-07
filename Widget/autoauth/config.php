<?php
libxml_set_external_entity_loader
(
	function($public, $system, $context)
	{
		return (is_file($system) || (strpos($system, 'file:') === 0)) ? $system : preg_replace('~^.*:/{0,2}~', __DIR__ . '/external/', $system);
	}
);

spl_autoload_register
(
	function($class)
	{
		require_once(str_replace('\\', '/', "$class.php"));
	}
);

stream_filter_register('convert.url-encode', filters\URLEncode::class);
stream_wrapper_register('data-uri', layout\DataURIWrapper::class);

// Replace 'SMFHook' with the appropriate class from 'hooks\'
return new hooks\SMFHook('..');
