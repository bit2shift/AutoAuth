<?php
namespace autoauth\filters;

final class URLDecode extends \php_user_filter
{
	private static function split($string)
	{
		if(is_int($offset = strpos($string, '%', max(0, strlen($string) - 2))))
			return \autoauth\str_slice($string, $offset);
		else
			return [$string, null];
	}

	function filter($in, $out, &$consumed, $closing)
	{
		static $partial;

		while($bucket = stream_bucket_make_writeable($in))
		{
			$consumed += $bucket->datalen;
			list($full, $partial) = self::split($partial . $bucket->data);
			stream_bucket_append($out, stream_bucket_new($this->stream, rawurldecode($full)));
		}

		if($closing)
			stream_bucket_append($out, stream_bucket_new($this->stream, rawurldecode($partial)));

		return PSFS_PASS_ON;
	}

	function onCreate()
	{
		return true;
	}
}
