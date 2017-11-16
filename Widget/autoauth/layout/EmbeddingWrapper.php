<?php
namespace layout;

class EmbeddingWrapper
{
	/**
	 * Optimal I/O chunk size.
	 * @var integer
	 */
	const CHUNK_SIZE = (1 << 12);

	/**
	 * Fast MIME-type query for known file extensions.
	 * @param string $path
	 * @return string
	 */
	private static function mime($path)
	{
		switch(pathinfo($path, PATHINFO_EXTENSION))
		{
		case 'css': return 'text/css';
		case 'png': return 'image/png';
		case 'ttf': return 'application/x-font-ttf';
		default:    return (new \finfo(FILEINFO_MIME_TYPE))->file($path);
		}
	}

	/**
	 * Transforms a file into a data URI.
	 * @param string $path
	 * @return \Generator
	 */
	private static function uri($path)
	{
		$mime = 'data:' . self::mime($path);
		if(strpos($mime, 'text/'))
		{
			yield "$mime;charset=UTF-8,";
			foreach(self::embed($path) as $str)
				yield rawurlencode($str);
		}
		elseif($handle = fopen($path, 'rb'))
		{
			yield "$mime;base64,";
			stream_filter_append($handle, 'convert.base64-encode');
			while(!feof($handle))
				yield fread($handle, self::CHUNK_SIZE);
			fclose($handle);
		}
	}

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

	/**
	 * Path splicer
	 * @param string $path
	 * @return string
	 */
	private static function path($path)
	{
		$url = parse_url($path);
		return "$url[host]$url[path]";
	}

	/**
	 * embed()'s return
	 * @var \Generator
	 */
	private $iterator;

	/**
	 * Intermediary buffer
	 * @var string
	 */
	private $buffer = '';

	/**
	 * Retrieve information about a file
	 * @param string $path The file path or URL to stat. Note that in the case of a URL, it must be a :// delimited URL. Other URL forms are not supported.
	 * @param int $flags Holds additional flags set by the streams API. It can hold one or more of the following values OR'd together.
	 * @return array
	 */
	function url_stat($path, $flags)
	{
		if(!($flags & STREAM_URL_STAT_LINK))
			return stat(self::path($path));

		return false;
	}

	/**
	 * Opens file or URL
	 * @param string $path Specifies the URL that was passed to the original function.
	 * @param string $mode The mode used to open the file, as detailed for fopen().
	 * @param int $options Holds additional flags set by the streams API. It can hold one or more of the following values OR'd together.
	 * @param string $opened_path If the path is opened successfully, and STREAM_USE_PATH is set in options, opened_path should be set to the full path of the file/resource that was actually opened.
	 * @return bool
	 */
	function stream_open($path, $mode, $options, &$opened_path)
	{
		if(($mode !== 'r') && ($mode !== 'rb'))
			return false;

		$path = self::path($path);
		$this->iterator = self::embed($path);
		if(!$this->iterator->valid())
			return false;

		if($options & STREAM_USE_PATH)
			$opened_path = stream_resolve_include_path($path);

		return true;
	}

	/**
	 * Read from stream
	 * @param int $count How many bytes of data from the current position should be returned.
	 * @return string
	 */
	function stream_read($count)
	{
		while($this->iterator->valid() && (strlen($this->buffer) < $count))
		{
			$this->buffer .= $this->iterator->current();
			$this->iterator->next();
		}

		$out = substr($this->buffer, 0, $count);
		$this->buffer = substr($this->buffer, $count);
		return $out;
	}

	/**
	 * Tests for end-of-file on a file pointer
	 * @return boolean
	 */
	function stream_eof()
	{
		return !strlen($this->buffer) && !$this->iterator->valid();
	}
}
