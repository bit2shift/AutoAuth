<?php
namespace autoauth\filters;

use autoauth\util;

final class URLEncode extends util\Filterer
{
	const FILTER_NAME = 'convert.url-encode';

	protected function filterer(bool $eof) : bool
	{
		while(strlen($data = $this->read()))
			$this->write(rawurlencode($data));

		return true;
	}
}
