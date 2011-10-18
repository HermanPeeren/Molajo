<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * @package		Molajo
 * @subpackage	mod_content
 * @since       1.0
 */
class modContentHelper
{
	/**
	 * Get a list of items for a specific type(s) of content
	 *
	 * @param	object  $params Module Parameters
	 * @param	object  $user   User object
	 *
	 * @return	mixed	An array of items for specified content or false
	 */
	public static function getList($params, $user)
	{
//todo: change to use MolajoModelDisplay

        $db	= MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $lang = MolajoFactory::getLanguage()->getTag();

        $date = MolajoFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        $query->select('a.id, a.title, a.checked_out, a.checked_out_time');
        $query->select('a.access, a.created, a.created_by, a.created_by_alias, a.featured, a.state');
        $query->select('a.catid, b.title as category_title');

        $query->from('#'.$params->get('component_table', '_articles').' AS a');
        $query->join('LEFT','#__categories AS b ON b.id = a.catid');

        $query->where('a.published = 1');
        $query->where('(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')');
        $query->where('(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')');

        $query->where('b.published = 1');
        $query->where('(b.publish_up = '.$db->Quote($nullDate).' OR b.publish_up <= '.$db->Quote($now).')');
        $query->where('(b.publish_down = '.$db->Quote($nullDate).' OR b.publish_down >= '.$db->Quote($now).')');

        $acl = new MolajoACL ();
        $acl->getQueryInformation ('', &$query, 'viewaccess', array('table_prefix'=>'a'));

        if (MolajoFactory::getApplication()->isSite()
            && MolajoFactory::getApplication()->getLanguageFilter()) {
            $query->where('a.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
        }

        /** category filter */
        $categoryId = $params->def('catid', 0);
        if ((int) $categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

//        $categoryIds = $params->get('catid', array());
        /** Online User */
        if ((int) $params->def('limit_to_online_user', 0) > 0) {
            $query->where('a.created_by = '.(int) $user->get('id'));
        }

       /** olimit and rdering */
		$query->ordering('start', 0);
		$query->ordering('limit', $params->get('count', 5));
		$query->ordering('order', $params->get('ordering', 'desc'));

        $db->setQuery($query->__toString());

        $items = $db->loadObjectList();

        if($db->getErrorNum()){
            JError::raiseWarning(500, MolajoText::sprintf('MOLAJO_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
            return false;
        }

		/** Add information to query results */
        $i = 1;
        $acl = new MolajoACL ();

        if (count($items) == 0) {
            $items[0]->columncount = '4';
            $items[0]->columnheading1 = MolajoText::_('MOD_LATEST_LATEST_ITEMS');
            $items[0]->columnheading2 = MolajoText::_('JSTATUS');
            $items[0]->columnheading3 = MolajoText::_('MOD_LATEST_CREATED');
            $items[0]->columnheading4 = MolajoText::_('MOD_LATEST_CREATED_BY');

        } else {

            foreach ($items as $item) {

                /** Headings */
                $item->columncount = '4';
                $item->columnheading.$i = MolajoText::_('MOD_LATEST_LATEST_ITEMS');
                $item->columnheading.$i = MolajoText::_('JSTATUS');
                $item->columnheading.$i = MolajoText::_('MOD_LATEST_CREATED');
                $item->columnheading.$i = MolajoText::_('MOD_LATEST_CREATED_BY');

                /** ACL */
                if ($acl->authoriseTask ('com_articles', 'display', 'view', $item->id, $item->catid, $item)) {
                    $item->link = MolajoRoute::_('index.php?option=com_articles&task=edit&id='.$item->id);
                } else {
                    $item->link = '';
                }

                /** Rowcount */
                $item->rowcount = $i++;
            }
        }

		return $items;
	}

	/**
	 * Get the alternate title for the module
	 *
	 * @param	JObject	The module parameters.
	 * @return	string	The alternate title for the module.
	 */
	public static function getTitle($params)
	{
		$who = $params->get('user_id');
		$catid = (int)$params->get('catid');
		$type = $params->get('ordering') == 'c_dsc' ? '_CREATED' : '_MODIFIED';
		if ($catid)
		{
			$category = JCategories::getInstance('Content')->get($catid);
			if ($category) {
				$title = $category->title;
			}
			else {
				$title = MolajoText::_('MOD_POPULAR_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}
		return MolajoText::plural('MOD_LATEST_TITLE'.$type.($catid ? "_CATEGORY" : '').($who!='0' ? "_$who" : ''), (int)$params->get('count'), $title);
	}
}
