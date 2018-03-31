<?php
namespace autoauth;

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

stream_filter_register('convert.url-decode', filters\URLDecode::class);
stream_filter_register('convert.url-encode', filters\URLEncode::class);

stream_filter_register('css_embed_urls', filters\CSSEmbedURLs::class);

/**
 * Slice string at offset.
 * @param string $string
 * @param int $offset
 * @return string[]
 */
function str_slice($string, $offset)
{
	return [substr($string, 0, $offset), substr($string, $offset)];
}
