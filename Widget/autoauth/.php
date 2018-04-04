<?php
function translate_path($original_separator, $path)
{
	if($original_separator == DIRECTORY_SEPARATOR)
		return $path;
	else
		return str_replace($original_separator, DIRECTORY_SEPARATOR, $path);
}

libxml_set_external_entity_loader
(
	function($public, $system, $context)
	{
		if((strpos($system, 'file:') === 0) || is_file($system))
			return $system;

		$url = parse_url($system);
		return __DIR__ . translate_path('/', "/external/$url[host]$url[path]");
	}
);

spl_autoload_register
(
	function($class)
	{
		require_once __DIR__ . translate_path('\\', "\\internal\\$class.php");
	}
);

autoauth\filters\CSSEmbedURLs::register();
autoauth\filters\URLDecode::register();
autoauth\filters\URLEncode::register();
