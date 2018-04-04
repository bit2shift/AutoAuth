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
			$fp = @fopen('php://memory', 'rw');
			$size = @stream_set_chunk_size($fp, 1);
			@fclose($fp);
		}

		return $size;
	}
}
