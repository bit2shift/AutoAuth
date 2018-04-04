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

	protected function filterer($eof)
	{
		static $partial;

		while($data = $this->read())
		{
			list($full, $partial) = self::split($partial . $data);
			$this->write(rawurldecode($full));
		}

		if($eof)
			$this->write(rawurldecode($partial));

		return true;
	}
}
