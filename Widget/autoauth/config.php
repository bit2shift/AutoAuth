<?php
libxml_set_external_entity_loader
(
	function($public, $system, $context)
	{
		if((strpos($system, 'file:') === 0) || is_file($system))
			return $system;

		$host = $path = null;
		extract(parse_url($system), EXTR_IF_EXISTS);
		return __DIR__ . "/external/$host$path";
	}
);

spl_autoload_register
(
	function($class)
	{
		require_once(str_replace('\\', '/', "$class.php"));
	}
);

stream_filter_register('convert.url-decode', filters\URLDecode::class);
stream_filter_register('convert.url-encode', filters\URLEncode::class);

stream_wrapper_register('data-uri', layout\DataURIWrapper::class);

// Replace 'SMFHook' with the appropriate class from 'hooks\'
return new hooks\SMFHook('..');
