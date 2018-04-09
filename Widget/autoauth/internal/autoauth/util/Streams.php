<?php
namespace autoauth\util;

final class Streams
{
	private function __construct() {}

	/**
	 * Optimal IO chunk size.
	 * @return int
	 */
	static function ioChunkSize()
	{
		static $size;

		if(!$size)
		{
			$fp = fopen('php://memory', 'rw');
			$size = stream_set_chunk_size($fp, 1);
			fclose($fp);
		}

		return $size;
	}

	/**
	 * Retrieves the URI of a stream and removes any leading "php://filter".
	 * @param resource $stream
	 * @return string
	 */
	static function unfilteredURI($stream)
	{
		return preg_replace('~php://filter/.+?/resource=~', '', stream_get_meta_data($stream)['uri']);
	}
}
