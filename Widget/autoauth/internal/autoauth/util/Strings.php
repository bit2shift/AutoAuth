<?php
namespace autoauth\util;

final class Strings
{
	private function __construct() {}

	/**
	 * Slice string at offset.
	 * @param string $string
	 * @param int $offset
	 * @return string[]
	 */
	static function slice($string, $offset)
	{
		return [substr($string, 0, $offset), substr($string, $offset)];
	}
}
