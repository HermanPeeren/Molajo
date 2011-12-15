<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Installation Helper
 *
 * @package        Molajo
 * @subpackage    Application
 * @since       1.0
 */
class MolajoInstallationApplicationHelper extends MolajoApplicationHelper
{

    /**
     * Forces specific option for installer
     *
     * @return    string        option
     * @since    1.0
     */
    public static function findOption()
    {
        JRequest::setVar('option', 'installer');
        return 'installer';
    }

}