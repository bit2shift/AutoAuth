<?php
namespace autoauth\layout;

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

		$this->layout->documentURI = dirname($path);

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
						$writer->startAttribute($attribute->name);
						//TODO CSS handler
						$writer->endAttribute();
						break;

					case ['img', 'src']:
						$writer->startAttribute($attribute->name);

						if($fp = fopen("data-uri://{$this->layout->documentURI}/$attribute->value", 'r'))
						{
							while(!feof($fp))
								$writer->text(fread($fp, 1024));
							fclose($fp);
						}

						$writer->endAttribute();
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
