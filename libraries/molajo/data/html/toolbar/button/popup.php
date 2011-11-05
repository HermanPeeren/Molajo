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
 * Renders a popup window button
 *
 * @package    Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoButtonPopup extends MolajoButton
{
    /**
     * Button type
     *
     * @var    string
     */
    protected $_name = 'Popup';

    public function fetchButton($type = 'Popup', $name = '', $text = '', $url = '', $width = 640, $height = 480, $top = 0, $left = 0, $onClose = '')
    {
        MolajoHTML::_('behavior.modal');

        $text = MolajoText::_($text);
        $class = $this->fetchIconClass($name);
        $doTask = $this->_getCommand($name, $url, $width, $height, $top, $left);

        $html = "<a class=\"modal\" href=\"$doTask\" rel=\"{handler: 'iframe', size: {x: $width, y: $height}, onClose: function() {" . $onClose . "}}\">\n";
        $html .= "<span class=\"$class\">\n";
        $html .= "</span>\n";
        $html .= "$text\n";
        $html .= "</a>\n";

        return $html;
    }

    /**
     * Get the button id
     *
     * Redefined from MolajoButton class
     *
     * @param   string    $name    Button name
     * @return  string    Button CSS Id
     * @since       1.0
     */
    public function fetchId($type, $name)
    {
        return $this->_parent->getName() . '-' . "popup-$name";
    }

    /**
     * Get the JavaScript command for the button
     *
     * @param   object   $definition    Button definition
     * @return  string   JavaScript command string
     * @since   1.0
     */
    protected function _getCommand($name, $url, $width, $height, $top, $left)
    {
        if (substr($url, 0, 4) !== 'http') {
            $url = JURI::base() . $url;
        }

        return $url;
    }
}
