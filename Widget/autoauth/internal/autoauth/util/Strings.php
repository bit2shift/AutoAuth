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
		if(is_numeric($offset))
			return [(string)substr($string, 0, $offset), (string)substr($string, $offset)];
		else
			return [(string)$string, ''];
	}
}
