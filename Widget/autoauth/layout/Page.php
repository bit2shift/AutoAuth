<?php
namespace layout;

class Page extends \DOMDocument
{
	/**
	 * Fast MIME-type query for known file extensions.
	 * @param string $path
	 * @return string
	 */
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

	/**
	 * Transforms a file into a data URI.
	 * @param string $path
	 * @return string
	 */
	private function uri($path)
	{
		$mime = $this->mime($path);
		$data = base64_encode((strpos($mime, 'text/') === 0) ? $this->embed($path) : file_get_contents($path));
		return "data:$mime;base64,$data";
	}

	/**
	 * Embeds all occurrences of «file» into data URIs.
	 * @param string $path
	 * @return string
	 */
	private function embed($path)
	{
		return preg_replace_callback
		(
			'/«([^«»]++)»/',
			function($match) use ($path)
			{
				return $this->uri(dirname($path) . "/$match[1]");
			},
			file_get_contents($path)
		);
	}

	/**
	 * Instantiates a page from a given layout file.
	 * Schema validation of the layout is cached.
	 * @param string $path
	 */
	function __construct($path)
	{
		parent::__construct();

		$this->formatOutput = true;
		$this->preserveWhiteSpace = false;

		$cache = (dirname($path) . '/.cache/');
		if(!is_dir($cache) && !@mkdir($cache, 0700))
			trigger_error('Cannot create cache directory. Performance will be affected.');

		$src = $this->embed($path);
		if(!$this->loadXML($src))
			throw new \DOMException("Cannot load '$path'");

		$filesum = ($cache . basename($path) . '.sha256');
		$checksum = hash('sha256', $src);
		if(@file_get_contents($filesum) !== $checksum)
		{
			if(!$this->schemaValidate(__DIR__ . '/xhtml1-transitional.xsd'))
				throw new \DOMException("Document in '$path' is invalid");
			else
				@file_put_contents($filesum, $checksum);
		}
	}
}
