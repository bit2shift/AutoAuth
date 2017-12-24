<?php
namespace filters;

final class URLEncode extends \php_user_filter
{
	function filter($in, $out, &$consumed, $closing)
	{
		while($bucket = stream_bucket_make_writeable($in))
		{
			$consumed += $bucket->datalen;
			stream_bucket_append($out, stream_bucket_new($this->stream, rawurlencode($bucket->data)));
		}
		return PSFS_PASS_ON;
	}

	function onCreate()
	{
		return true;
	}
}
