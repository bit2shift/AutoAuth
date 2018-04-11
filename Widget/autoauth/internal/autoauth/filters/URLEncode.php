<?php
namespace autoauth\filters;

use autoauth\util;

final class URLEncode extends util\Filterer
{
	const FILTER_NAME = 'convert.url-encode';

	protected function filterer($eof)
	{
		while(is_string($data = $this->read()))
			$this->write(rawurlencode($data));

		return true;
	}
}
