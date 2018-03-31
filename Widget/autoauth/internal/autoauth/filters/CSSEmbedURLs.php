<?php
namespace autoauth\filters;

final class CSSEmbedURLs extends \autoauth\util\Filterer
{
	protected function filterer(callable $read, callable $write, $eof)
	{
		static $partial;

		while(is_string($data = $read(self::BLOCK_SIZE)))
		{
			$partial .= $data;
			while(count($str = preg_split('/url\(\s*+(("|\')(?:(?!\2)[^\\\\\v]|\\\\\X)++\2|(?:[^"\'()\\\\\s[:^print:]]|\\\\\X)++)\s*+\)/', $partial, 2, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)) > 2)
			{
				$write($str[0]);
				$file = stripslashes($str[1]);
				$partial = $str[2];

				switch($file[0])
				{
				case '"':
				case "'":
					$file = substr($file, 1, -1);
				}

				$write('url(');

				if($uri = \autoauth\util\DataURI::from(dirname(stream_get_meta_data($this->stream)['uri']) . "/$file"))
				{
					$write($uri->mime);
					while(!feof($uri->handle))
						$write(fread($uri->handle, self::BLOCK_SIZE));
					fclose($uri->handle);
				}
				else
					$write($file);

				$write(')');
			}
		}

		if($eof)
			$write($partial);

		return true;
	}
}