<?php
namespace layout;

class Page
{
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
	 * @return string
	 */
	private static function uri($path)
	{
		$mime = self::mime($path);
		$data = base64_encode((strpos($mime, 'text/') === 0) ? self::embed($path) : file_get_contents($path));
		return "data:$mime;base64,$data";
	}

	/**
	 * Embeds all occurrences of «file» into data URIs.
	 * @param string $path
	 * @return string
	 */
	private static function embed($path)
	{
		return preg_replace_callback
		(
			'/«([^«»]++)»/',
			static function($match) use ($path)
			{
				return self::uri(dirname($path) . "/$match[1]");
			},
			file_get_contents($path)
		);
	}

	/**
	 * @var \DOMDocument
	 */
	private $layout;

	/**
	 * @var \DOMXPath
	 */
	private $needle;

	/**
	 * Instantiates a page from a given layout file.
	 * Layout validation is cached in a sibling folder.
	 * @param string $path
	 */
	function __construct($path)
	{
		$source = self::embed($path);

		$this->layout = new \DOMDocument();
		$this->layout->formatOutput = true;
		$this->layout->preserveWhiteSpace = false;
		if(!$this->layout->loadXML($source))
			throw new \DOMException("Cannot load '$path'");

		clearstatcache();

		$cache = (dirname($path) . '/.cache/');
		if(!is_dir($cache) && !@mkdir($cache, 0700))
			trigger_error('Cannot create cache directory. Performance will be affected.');

		$filesum = ($cache . basename($path) . '.sha256');
		if(@file_get_contents($filesum) !== ($checksum = hash('sha256', $source)))
		{
			if(!$this->layout->schemaValidate(__DIR__ . '/xhtml1-transitional.xsd'))
				throw new \DOMException("Document in '$path' is invalid");
			else
				@file_put_contents($filesum, $checksum);
		}

		$this->needle = new \DOMXPath($this->layout);
		$this->needle->registerNamespace('html', 'http://www.w3.org/1999/xhtml');
	}

	/**
	 * Evaluates a given variable name as XPath.
	 * Use 'html' as prefix for HTML elements.
	 * @param string $name
	 * @return DOMNodeList
	 */
	function __get($name)
	{
		return $this->needle->query($name);
	}

	/**
	 * Display the page, as XHTML.
	 */
	function display()
	{
		header('Content-Type: application/xhtml+xml; charset=UTF-8');
		echo $this->layout->saveXML();
	}
}
