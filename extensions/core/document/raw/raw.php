<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 * DocumentRAW class, provides an easy interface to parse and display raw output
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */

class MolajoDocumentRaw extends MolajoDocument
{

    /**
     * Class constructor
     *
     * @param   array  $options  Associative array of options
     *
     * @return  MolajoDocumentRaw
     *
     * @since   1.0
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        //set mime type
        $this->_mime = 'text/html';

        //set document type
        $this->_type = 'raw';
    }

    /**
     * Render the document.
     *
     * @param   boolean  $cache   If true, cache the output
     * @param   array    $parameters  Associative array of attributes
     *
     * @return  The rendered data
     *
     * @since   1.0
     */
    public function render($cache = false, $parameters = array())
    {
        parent::render();
        return $this->getBuffer();
    }
}
