<?php
namespace autoauth\util;

final class DataURI
{
	private function __construct() {}

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
	 * Creates an object containing the elements used to write a data
	 * URI to a stream without the need to load the file into memory.
	 * @param string $path
	 * @return bool|object {resource $handle, string $mime}
	 */
	static function from($path)
	{
		if(!$handle = @fopen($path, 'rb'))
			return false;

		$mime = 'data:' . self::mime($path);
		if(strpos($mime, 'text/'))
		{
			$mime .= ';charset=UTF-8,';
			stream_filter_append($handle, 'convert.url-encode');
		}
		else
		{
			$mime .= ';base64,';
			stream_filter_append($handle, 'convert.base64-encode');
		}

		return (object)['handle' => $handle, 'mime' => $mime];
	}
}
