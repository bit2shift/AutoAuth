<?php
namespace autoauth\filters;

use autoauth\util;

final class URLEncode extends util\Filterer
{
	const FILTER_NAME = 'convert.url-encode';

	protected function filterer($eof)
	{
		while(($data = $this->read()) !== false)
			$this->write(rawurlencode($data));

		return true;
	}
}
