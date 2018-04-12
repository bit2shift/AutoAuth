<?php
namespace autoauth\filters;

use autoauth\util;

final class CSSEmbedURLs extends util\Filterer
{
	const FILTER_NAME = 'css_embed_urls';

	protected function filterer($eof)
	{
		static $partial;

		while(($data = $this->read()) !== false)
		{
			$partial .= $data;
			while(count($str = preg_split('/url\(\s*+(("|\')(?:(?!\2)[^\\\\\v]|\\\\\X)++\2|(?:[^"\'()\\\\\s[:^print:]]|\\\\\X)++)\s*+\)/', $partial, 2, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)) > 2)
			{
				$this->write($str[0]);
				$file = stripslashes($str[1]);
				$partial = $str[2];

				switch($file[0])
				{
				case '"':
				case "'":
					$file = substr($file, 1, -1);
				}

				$this->write('url(');

				$uri = util\DataURI::from(dirname(util\Streams::unfilteredURI($this->stream)) . "/$file");
				if($uri->valid())
					foreach($uri as $chunk)
						$this->write($chunk);
				else
					$this->write($file);

				$this->write(')');
			}
		}

		if($eof)
			$this->write($partial);

		return true;
	}
}
