<?php
namespace autoauth\layout;

class DataURIWrapper
{
	const SCHEME = 'data-uri';

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
	 * @var resource
	 */
	private $target;

	/**
	 * @var string
	 */
	private $mime;

	/**
	 * Opens file or URL.
	 * @param string $url Specifies the URL that was passed to the original function.
	 * @param string $mode The mode used to open the file, as detailed for fopen().
	 * @param int $options Holds additional flags set by the streams API. It can hold one or more of the following values OR'd together.
	 * @param string $opened_path If the path is opened successfully, and STREAM_USE_PATH is set in options, opened_path should be set to the full path of the file/resource that was actually opened.
	 * @return bool
	 */
	function stream_open($url, $mode, $options, &$opened_path)
	{
		if($mode[0] !== 'r')
			return false;

		$opened_path = substr($url, strlen(self::SCHEME) + 3);

		if($options & STREAM_USE_PATH)
			$opened_path = stream_resolve_include_path($opened_path);

		if(!$this->target = @fopen($opened_path, $mode))
			return false;

		$this->mime = 'data:' . self::mime($opened_path);
		if(strpos($this->mime, 'text/'))
		{
			$this->mime .= ';charset=UTF-8,';
			stream_filter_append($this->target, 'convert.url-encode');
		}
		else
		{
			$this->mime .= ';base64,';
			stream_filter_append($this->target, 'convert.base64-encode');
		}

		return true;
	}

	/**
	 * Tests for end-of-file on a file pointer.
	 * @return bool
	 */
	function stream_eof()
	{
		return empty($this->mime) && feof($this->target);
	}

	/**
	 * Read from stream.
	 * @param int $count How many bytes of data from the current position should be returned.
	 * @return string
	 */
	function stream_read($count)
	{
		if(empty($this->mime))
			return fread($this->target, $count);

		list($out, $this->mime) = \autoauth\str_slice($this->mime, $count);
		return $out . fread($this->target, $count - strlen($out));
	}

	/**
	 * Close a resource.
	 */
	function stream_close()
	{
		fclose($this->target);
	}
}
