<?php
namespace layout;

class Page
{
	private $cache;

	private function mime($path)
	{
		switch(pathinfo($path, PATHINFO_EXTENSION))
		{
		case 'css': return 'text/css';
		case 'png': return 'image/png';
		case 'ttf': return 'application/x-font-ttf';
		default:    return (new \finfo(FILEINFO_MIME_TYPE))->file($path);
		}
	}

	private function uri($path)
	{
		$mime = $this->mime($path);
		$data = base64_encode((strpos($mime, 'text/') === 0) ? $this->embed($path) : file_get_contents($path));
		return "data:$mime;base64,$data";
	}

	private function embed($path)
	{
		return preg_replace_callback
		(
			'/«([^«»]++)»/',
			function($match)
			{
				return $this->uri($match[1]);
			},
			file_get_contents($path)
		);
	}

	/**
	 * Creates a new instance based on the given folder.
	 * @param string $dir
	 */
	function __construct($dir)
	{
		$cache = "$dir/.cache/";

		if(!is_dir($cache) && !mkdir($cache, 0700))
			trigger_error('Cannot create cache directory. Performance will be affected.');
		else
			$this->cache = $cache;
	}
}
