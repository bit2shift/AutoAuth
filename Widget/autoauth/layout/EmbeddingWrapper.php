<?php
namespace autoauth\layout;

class EmbeddingWrapper
{
	/**
	 * Optimal I/O chunk size.
	 * @var integer
	 */
	const CHUNK_SIZE = (1 << 12);

	/**
	 * Embeds all occurrences of «file» into data URIs.
	 * @param string $path
	 * @return \Generator
	 */
	private static function embed($path)
	{
		if($handle = fopen($path, 'rb'))
		{
			$path = dirname($path);
			while(!feof($handle))
			{
				$buffer = fread($handle, self::CHUNK_SIZE);
				for($a = 0; ($b = strpos($buffer, '"', $a)) !== false; $a = ($b + 1))
				{
					$str = substr($buffer, $a, ($b - $a));
					if(isset($string))
					{
						$string .= $str;

						if(is_file($str = "$path/$string"))
						{
							yield '"';
							foreach(self::uri($str) as $str)
								yield $str;
							yield '"';
						}
						else
							yield "\"$string\"";

						unset($string);
					}
					else
					{
						yield $str;
						$string = '';
					}
				}

				$str = substr($buffer, $a);
				if(isset($string))
					$string .= $str;
				else
					yield $str;
			}

			if(isset($string))
				yield "\"$string";

			fclose($handle);
		}
	}
}
