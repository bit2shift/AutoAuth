<?php
namespace autoauth\filters;

final class URLEncode extends \autoauth\util\Filterer
{
	protected function filterer($eof)
	{
		while($data = $this->read())
			$this->write(rawurlencode($data));

		return true;
	}
}
