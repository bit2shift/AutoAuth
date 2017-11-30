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
	 * @param string $path
	 */
	function __construct($path)
	{
		$this->layout = new \DOMDocument();
		if(!$this->layout->load($path))
			throw new \DOMException("Cannot load '$path'");

		if(!$this->layout->validate())
			throw new \DOMException("Document in '$path' is invalid");

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
