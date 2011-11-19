<?php
/**
 * @version     $id: plugintype
 * @package     Molajo
 * @subpackage  HTML Class
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Select list of Plugin Types
 *
 * @static
 * @package        Joomla.Framework
 * @subpackage    HTML
 * @since        1.5
 */
abstract class MolajoHtmlPluginType
{
    /**
     * Returns an array of plugin types
     *
     * @param    string    The extension option.
     * @param    array    An array of configuration options.
     *
     * @return    array
     */
    public function options()
    {

        /** option list **/
        $options = array();
        //        $options[]	= MolajoHTML::_('select.option', 'authentication', MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_AUTHENTICATION'));
        $options[] = MolajoHTML::_('select.option', 'content', MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_CONTENT'));
        //        $options[]	= MolajoHTML::_('select.option', 'editors', MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_EDITORS'));
        //        $options[]	= MolajoHTML::_('select.option', 'editors-xtd', MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_EDITOR_BUTTONS'));
        $options[] = MolajoHTML::_('select.option', 'extension', MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_EXTENSION'));
        $options[] = MolajoHTML::_('select.option', 'search', MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_SEARCH'));
        $options[] = MolajoHTML::_('select.option', 'system', MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_SYSTEM'));
        $options[] = MolajoHTML::_('select.option', 'user', MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE_USER'));

        return $options;

    }
}