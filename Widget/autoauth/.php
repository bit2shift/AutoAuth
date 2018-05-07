<?php
function translate_path(string $original_separator, string $path) : string
{
	if($original_separator == DIRECTORY_SEPARATOR)
		return $path;
	else
		return str_replace($original_separator, DIRECTORY_SEPARATOR, $path);
}

libxml_set_external_entity_loader
(
	function(string $public, string $system, array $context) : string
	{
		if((strpos($system, 'file:') === 0) || @file_exists($system))
			return $system;

		$url = parse_url($system);
		return __DIR__ . translate_path('/', "/external/$url[host]$url[path]");
	}
);

spl_autoload_register
(
	function(string $class) : void
	{
		require_once __DIR__ . translate_path('\\', "\\internal\\$class.php");
	}
);

autoauth\filters\CSSEmbedURLs::register();
autoauth\filters\URLDecode::register();
autoauth\filters\URLEncode::register();
