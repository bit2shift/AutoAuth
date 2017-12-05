<?php
namespace layout;

class URLEncodeFilter extends \php_user_filter
{
	function filter($in, $out, &$consumed, $closing)
	{
		while($bucket = stream_bucket_make_writeable($in))
		{
			$consumed += $bucket->datalen;
			$bucket->data = rawurlencode($bucket->data);
			stream_bucket_append($out, $bucket);
		}
		return PSFS_PASS_ON;
	}

	function onCreate()
	{
		return true;
	}
}
