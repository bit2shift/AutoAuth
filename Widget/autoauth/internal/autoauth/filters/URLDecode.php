<?php
namespace autoauth\filters;

final class URLDecode extends \autoauth\util\Filterer
{
	private static function split($string)
	{
		if(is_int($offset = strpos($string, '%', max(0, strlen($string) - 2))))
			return \autoauth\util\Strings::slice($string, $offset);
		else
			return [$string, null];
	}

	protected function filterer(callable $read, callable $write, $eof)
	{
		static $partial;

		while(is_string($data = $read(self::BLOCK_SIZE)))
		{
			list($full, $partial) = self::split($partial . $data);
			$write(rawurldecode($full));
		}

		if($eof)
			$write(rawurldecode($partial));

		return true;
	}
}
