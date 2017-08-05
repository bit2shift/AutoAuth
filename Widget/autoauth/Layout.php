<?php
class Layout
{
	private $cache;

	private function mime($path)
	{
		switch(pathinfo($path, PATHINFO_EXTENSION))
		{
		case 'css': return 'text/css';
		case 'png': return 'image/png';
		case 'ttf': return 'application/x-font-ttf';
		default:    return (new finfo(FILEINFO_MIME_TYPE))->file($path);
		}
	}

	private function embed($path)
	{
		return preg_replace_callback
		(
			'/«([^«»]++)»/',
			function($match)
			{
				$mime = $this->mime($match[1]);
				$data = base64_encode((strpos($mime, 'text/') === 0) ? $this->embed($match[1]) : file_get_contents($match[1]));
				return "data:$mime;base64,$data";
			},
			file_get_contents($path)
		);
	}

	function test_embed($path)
	{
		echo $this->embed($path);
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
