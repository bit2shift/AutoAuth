<?php
namespace layout;

class Page
{
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
		stream_wrapper_register('embed', EmbeddingWrapper::class);

		$this->layout = new \DOMDocument();
		$this->layout->formatOutput = true;
		$this->layout->preserveWhiteSpace = false;
		if(!$this->layout->load("embed://$path"))
			throw new \DOMException("Cannot load '$path'");

		clearstatcache();

		$cache = (dirname($path) . '/.cache/');
		if(!is_dir($cache) && !@mkdir($cache, 0700))
			trigger_error('Cannot create cache directory. Performance will be affected.');

		$filesum = ($cache . basename($path) . '.sha256');
		if(@file_get_contents($filesum) !== ($checksum = hash_file('sha256', $path)))
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
	 * @return \DOMNodeList
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
