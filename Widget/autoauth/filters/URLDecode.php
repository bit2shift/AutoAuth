<?php
namespace filters;

final class URLDecode extends \php_user_filter
{
	private $partial;

	function filter($in, $out, &$consumed, $closing)
	{
		while($bucket = stream_bucket_make_writeable($in))
		{
			$consumed += $bucket->datalen;
			@list($bucket->data, $this->partial) = preg_split('/(%.?)$/', "$this->partial$bucket->data", null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			$bucket->data = rawurldecode($bucket->data);
			stream_bucket_append($out, $bucket);
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
