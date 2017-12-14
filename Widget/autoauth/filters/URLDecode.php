<?php
namespace filters;

final class URLDecode extends \php_user_filter
{
	private static function split($string)
	{
		if(is_int($position = strpos($string, '%', max(0, strlen($string) - 2))))
			return [substr($string, 0, $position), substr($string, $position)];
		else
			return [$string, ''];
	}

	/**
	 * Holds an incomplete sequence.
	 * @var string
	 */
	private $partial;

	function filter($in, $out, &$consumed, $closing)
	{
		while($bucket = stream_bucket_make_writeable($in))
		{
			$consumed += $bucket->datalen;
			list($bucket->data, $this->partial) = self::split("$this->partial$bucket->data");
			stream_bucket_append($out, stream_bucket_new($this->stream, rawurldecode($bucket->data)));
		}

		if($closing)
			stream_bucket_append($out, stream_bucket_new($this->stream, rawurldecode($this->partial)));

		return PSFS_PASS_ON;
	}

	function onCreate()
	{
		return true;
	}
}
