<?php
namespace autoauth\filters;

final class URLEncode extends \autoauth\util\Filterer
{
	protected function filterer(callable $read, callable $write, $eof)
	{
		while(is_string($data = $read(self::BLOCK_SIZE)))
			$write(rawurlencode($data));

		return true;
	}
}
