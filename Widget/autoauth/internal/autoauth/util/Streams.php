<?php
namespace autoauth\util;

final class Streams
{
	private function __construct() {}

	/**
	 * Optimal IO chunk size.
	 * @var int
	 */
	const CHUNK_SIZE = (1 << 13);

	/**
	 * Bit mask to round to the nearest multiple.
	 * @var int
	 */
	const CHUNK_SIZE_MASK = -self::CHUNK_SIZE;

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
