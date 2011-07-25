<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoMenu class
 *
 * @package		Molajo
 * @subpackage	Menu
 * @since		1.0
 */
class MolajoMenuSite extends JMenu
{
	/**
	 * Loads the entire menu table into memory.
	 *
	 * @return array
	 */
	public function load()
	{
		$cache = MolajoFactory::getCache('mod_menu', '');  // has to be mod_menu or this cache won't get cleaned

		if (!$data = $cache->get('menu_items'.MolajoFactory::getLanguage()->getTag())) {
			// Initialise variables.
			$db		= MolajoFactory::getDbo();
			$app	= MolajoFactory::getApplication();
			$query	= $db->getQuery(true);

			$query->select('m.id, m.menutype, m.title, m.alias, m.path AS route, m.link, m.type, m.level');
			$query->select('m.browserNav, m.access, m.params, m.home, m.img, m.template_style_id');
			$query->select('m.component_id, m.parent_id, m.language');
			$query->select('e.element as component');
			$query->from('#__menu AS m');
			$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
			$query->where('m.published = 1');
			$query->where('m.parent_id > 0');
			$query->where('m.application_id = 0');
			$query->order('m.lft');

            $acl = new MolajoACL ();
            $acl->getQueryInformation ('', $query, 'viewaccess', array('table_prefix'=>'m'));
echo $query->__toString();
            $db->setQuery($query->__toString());
            $menus = $db->loadObjectList();

            if ($db->getError()) {
                MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
                return false;
            }

            if (count($menus) > 0) {

                foreach ($menus as &$menu) {
                    // Get parent information.
                    $parent_tree = array();
                    if (isset($menus[$menu->parent_id])) {
                        $parent_tree  = $menus[$menu->parent_id]->tree;
                    }

                    // Create tree.
                    $parent_tree[] = $menu->id;
                    $menu->tree = $parent_tree;

                    // Create the query array.
                    $url = str_replace('index.php?', '', $menu->link);
                    $url = str_replace('&amp;','&',$url);

                    parse_str($url, $menu->query);
                }
            }
			$cache->store($menus, 'menu_items'.MolajoFactory::getLanguage()->getTag());

			$this->_items = $menus;
		} else {
			$this->_items = $data;
		}
	}
    
	/**
	 * Gets menu items by attribute
	 *
	 * @param	string	$attributes	The field name
	 * @param	string	$values		The value of the field
	 * @param	boolean	$firstonly	If true, only returns the first item found
	 *
	 * @return	array
	 */
	public function getItems($attributes, $values, $firstonly = false)
	{
		$attributes = (array) $attributes;
		$values = (array) $values;
		$app	= MolajoFactory::getApplication();
		// Filter by language if not set
		if ($app->isSite() && $app->getLanguageFilter() && !array_key_exists('language',$attributes)) {
			$attributes[]='language';
			$values[]=array(MolajoFactory::getLanguage()->getTag(), '*');
		}
		return parent::getItems($attributes, $values, $firstonly);
	}

	/**
	 * Get menu item by id
	 *
	 * @param	string	$language	The language code.
	 *
	 * @return	object	The item object
	 * @since	1.5
	 */
	function getDefault($language='*')
	{
		if (array_key_exists($language, $this->_default) && MolajoFactory::getApplication()->getLanguageFilter()) {
			return $this->_items[$this->_default[$language]];
		}
		else if (array_key_exists('*', $this->_default)) {
			return $this->_items[$this->_default['*']];
		}
		else {
			return 0;
		}
	}
}
