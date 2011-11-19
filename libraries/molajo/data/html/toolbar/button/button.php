<?php
/**
 * @package    Molajo
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Button base class
 *
 * The MolajoButton is the base class for all MolajoButton types
 *
 * @package    Molajo
 * @subpackage  HTML
 * @since       1.0
 */
abstract class MolajoButton extends JObject
{
    /**
     * element name
     *
     * This has to be set in the final renderer classes.
     *
     * @var    string
     */
    protected $_name = null;

    /**
     * reference to the object that instantiated the element
     *
     * @var    object
     */
    protected $_parent = null;

    /**
     * Constructor
     */
    public function __construct($parent = null)
    {
        $this->_parent = $parent;
    }

    /**
     * Get the element name
     *
     * @return  string   type of the parameter
     */
    public function getName()
    {
        return $this->_name;
    }

    public function render(&$definition)
    {
        /*
           * Initialise some variables
           */
        $html = null;
        $id = call_user_func_array(array(&$this, 'fetchId'), $definition);
        $action = call_user_func_array(array(&$this, 'fetchButton'), $definition);

        // Build id attribute
        if ($id) {
            $id = "id=\"$id\"";
        }

        // Build the HTML Button
        $html .= "<li class=\"button\" $id>\n";
        $html .= $action;
        $html .= "</li>\n";

        return $html;
    }

    /**
     * Method to get the CSS class name for an icon identifier
     *
     * Can be redefined in the final class
     *
     * @param   string   $identifier    Icon identification string
     * @return  string   CSS class name
     * @since   1.0
     */
    public function fetchIconClass($identifier)
    {
        return "icon-32-$identifier";
    }

    /**
     * Get the button
     *
     * Defined in the final button class
     *
     * @since       1.0
     */
    abstract public function fetchButton();
}
