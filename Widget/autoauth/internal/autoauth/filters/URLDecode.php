<?php
namespace autoauth\filters;

use autoauth\util;

final class URLDecode extends util\Filterer
{
	const FILTER_NAME = 'convert.url-decode';

	private static function split($string)
	{
		$offset = strpos($string, '%', max(0, strlen($string) - 2));
		return util\Strings::slice($string, $offset);
	}

	protected function filterer($eof)
	{
		static $partial;

		while(($data = $this->read()) !== false)
		{
			list($full, $partial) = self::split($partial . $data);
			$this->write(rawurldecode($full));
		}

		if($eof)
			$this->write(rawurldecode($partial));

		return true;
	}
}
