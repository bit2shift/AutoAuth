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
	private static function mime(string $path) : string
	{
		switch(pathinfo($path, PATHINFO_EXTENSION))
		{
		case 'css': return 'text/css';
		case 'png': return 'image/png';
		case 'ttf': return 'application/x-font-ttf';
		default:    return (new \finfo(FILEINFO_MIME_TYPE))->buffer(file_get_contents($path, false, null, 0, 256));
		}
	}

	/**
	 * Instantiates a data URI generator.
	 * @param string $path
	 * @return \Generator
	 */
	static function from(string $path) : \Generator
	{
		if(!$handle = @fopen($path, 'rb'))
			return;

		$mime = 'data:' . self::mime($path);
		if(strpos($mime, 'text/'))
		{
			stream_filter_append($handle, 'convert.url-encode');
			yield "$mime;charset=UTF-8,";
		}
		else
		{
			stream_filter_append($handle, 'convert.base64-encode');
			yield "$mime;base64,";
		}

		while(!feof($handle))
			yield fread($handle, Streams::CHUNK_SIZE);

		fclose($handle);
	}
}
