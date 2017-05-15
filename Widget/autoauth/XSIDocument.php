<?php
class XSIDocument extends DOMDocument
{
	/**
	 * Creates a new XSIDocument object from a file
	 * @param string $path
	 * @throws DOMException
	 */
	function __construct($path)
	{
		parent::__construct();

		if(!$this->load($path))
			throw new DOMException("Cannot load '$path'");

		if(!$this->validate())
			throw new DOMException("Document in '$path' is invalid");
	}

	/**
	 * Validates the document based on its XSD
	 * @return bool
	 */
	function validate()
	{
		$schemas = preg_split('/\s+/', $this->documentElement->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation'));

		if(count($schemas) % 2)
			return false;

		for($i = 1; $i < count($schemas); $i += 2)
		{
			if(!$this->schemaValidate(dirname($this->documentURI) . DIRECTORY_SEPARATOR . $schemas[$i]))
				return false;
		}

		return true;
	}
}
