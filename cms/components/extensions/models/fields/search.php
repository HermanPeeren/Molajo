<?php
/**
 * @version		$Id: search.php 20266 2011-01-11 02:31:44Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	installer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Form Field Search class.
 *
 * @package		Joomla.Administrator
 * @subpackage	installer
 * * * @since		1.0
 */
class JFormFieldSearch extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Search';

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 * @since	1.0
	 */
	protected function getInput()
	{
		$html = '';
		$html.= '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars($this->value).'" title="'.MolajoText::_('JSEARCH_FILTER').'" onchange="this.form.submit();" />';
		$html.= '<button type="submit" class="btn">'.MolajoText::_('JSEARCH_FILTER_SUBMIT').'</button>';
		$html.= '<button type="button" class="btn" onclick="document.id(\''.$this->id.'\').value=\'\';this.form.submit();">'.MolajoText::_('JSEARCH_FILTER_CLEAR').'</button>';
		return $html;
	}
}