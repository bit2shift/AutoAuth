<?php
namespace autoauth\filters;

final class CSSEmbedURLs extends \autoauth\util\Filterer
{
	protected function filterer($eof)
	{
		static $partial;

		while($data = $this->read())
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

				if($uri = \autoauth\util\DataURI::from(dirname(stream_get_meta_data($this->stream)['uri']) . "/$file"))
				{
					$this->write($uri->mime);
					while(!feof($uri->handle))
						$this->write(fread($uri->handle, self::BLOCK_SIZE));
					fclose($uri->handle);
				}
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
