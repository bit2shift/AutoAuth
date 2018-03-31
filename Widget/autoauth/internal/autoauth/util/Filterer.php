<?php
namespace autoauth\util;

abstract class Filterer extends \php_user_filter
{
	const BLOCK_SIZE = 1 << 10;

	/**
	 * Derived classes implement this.
	 * @param callable $read($count) : string|false
	 * @param callable $write($data) : void
	 * @param bool $eof
	 * @return bool
	 */
	protected abstract function filterer(callable $read, callable $write, $eof);

	function filter($in, $out, &$consumed, $closing)
	{
		static $input, $output;

		if(!$this->filterer
			(
				function($count) use(&$consumed, $in, &$input)
				{
					while((strlen($input) < $count) && ($bucket = stream_bucket_make_writeable($in)))
					{
						$consumed += $bucket->datalen;
						$input .= $bucket->data;
					}

					list($data, $input) = Strings::slice($input, $count);
					return $data;
				},
				function($data) use($out, &$output)
				{
					list($full, $partial) = Strings::slice($output .= $data, self::BLOCK_SIZE);
					if(is_string($partial))
					{
						stream_bucket_append($out, stream_bucket_new($this->stream, $full));
						$output = $partial;
					}
				},
				$closing
			)
		)
			return PSFS_ERR_FATAL;

		if($closing)
			stream_bucket_append($out, stream_bucket_new($this->stream, $output));

		return PSFS_PASS_ON;
	}

	function onCreate()
	{
		return true;
	}
}