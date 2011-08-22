<?php
/**
 * @package    Molajo
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * DocumentXML class, provides an easy interface to parse and display XML output
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */

jimport('joomla.document.document');

class MolajoDocumentXml extends MolajoDocument
{
	/**
	 * Document name
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $_name = 'joomla';

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @return  MolajoDocumentXml
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		//set mime type
		$this->_mime = 'application/xml';

		//set document type
		$this->_type = 'xml';
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  The rendered data
	 *
	 * @since  1.0
	 */
	public function render($cache = false, $params = array())
	{
		parent::render();
		JResponse::setHeader('Content-disposition', 'inline; filename="'.$this->getName().'.xml"', true);

		return $this->getBuffer();
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 *
	 * @return  void
	 *
	 * @since  1.0
	 */
	public function setName($name = 'joomla')
	{
		$this->_name = $name;
	}
}
