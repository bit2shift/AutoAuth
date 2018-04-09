<?php
namespace autoauth\layout;

use autoauth\util;

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
	 * Write URL-like attribute as data URI, if convertible.
	 * @param \XMLWriter $writer
	 * @param \DOMAttr $attr
	 * @param array $filters
	 */
	private function embed_attribute(\XMLWriter $writer, \DOMAttr $attr, array $filters = [])
	{
		$prefix = count($filters) ? 'php://filter/' . implode('|', $filters) . '/resource=' : '';
		$uri = util\DataURI::from($prefix . dirname($this->layout->documentURI) . "/$attr->value");
		if($uri->valid())
		{
			$writer->startAttribute($attr->name);
			foreach($uri as $chunk)
				$writer->text($chunk);
			$writer->endAttribute();
		}
		else
			$writer->writeAttribute($attr->name, $attr->value);
	}

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
			throw new \DOMException("Document ($path) does not conform to DTD");

		if(!$this->layout->schemaValidate('http://www.w3.org/2002/08/xhtml/xhtml1-transitional.xsd'))
			throw new \DOMException("Document ($path) does not conform to XML Schema");

		//fix-up from 'file:/...' to 'file:///...'
		$this->layout->documentURI = preg_replace('~file:/(?!/)~', 'file:///', $this->layout->documentURI);

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
	 * @return void
	 */
	function display()
	{
		$writer = new \XMLWriter();
		if(!$writer->openUri('php://output'))
			return http_response_code(500);

		ob_start('ob_gzhandler');
		header('Content-Type: application/xhtml+xml; charset=UTF-8');

		$writer->startDocument($this->layout->xmlVersion, $this->layout->xmlEncoding, $this->layout->xmlStandalone ? 'yes' : 'no');

		for($node = $this->layout->firstChild; $node; $node = $node->firstChild ?: $node->nextSibling ?: $node->parentNode->nextSibling)
		{
			switch($node->nodeType)
			{
			case XML_DOCUMENT_TYPE_NODE:
				$writer->writeDtd($node->name, $node->publicId, $node->systemId, $node->internalSubset);
				break;

			case XML_TEXT_NODE:
				if(!$node->isWhitespaceInElementContent())
					$writer->text($node->wholeText);

				break;

			case XML_ELEMENT_NODE:
				$writer->startElementNs($node->prefix ?: null, $node->localName, $node->namespaceURI);

				foreach($node->attributes as $attribute)
				{
					switch([$node->localName, $attribute->name])
					{
					case ['link', 'href']:
						$this->embed_attribute($writer, $attribute, ['css_embed_urls']);
						break;

					case ['img', 'src']:
						$this->embed_attribute($writer, $attribute);
						break;

					default:
						$writer->writeAttribute($attribute->name, $attribute->value);
					}
				}

				if(!$node->firstChild)
					$writer->endElement();

				break;
			}

			if(!$node->firstChild && !$node->nextSibling)
				$writer->fullEndElement();
		}

		$writer->endDocument();
		$writer->flush();
		ob_end_flush();
	}
}
