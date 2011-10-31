<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a template style.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * * * @since		1.0
 */
class TemplatesViewStyle extends JView
{
	protected $item;
	protected $form;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= MolajoFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$canDo		= TemplatesHelper::getActions();

		MolajoToolbarHelper::title(
			$isNew ? MolajoText::_('COM_TEMPLATES_MANAGER_ADD_STYLE')
			: MolajoText::_('COM_TEMPLATES_MANAGER_EDIT_STYLE'), 'thememanager'
		);

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::apply('style.apply');
			MolajoToolbarHelper::save('style.save');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			MolajoToolbarHelper::save2copy('style.save2copy');
		}

		if (empty($this->item->id))  {
			MolajoToolbarHelper::cancel('style.cancel');
		} else {
			MolajoToolbarHelper::cancel('style.cancel', 'JTOOLBAR_CLOSE');
		}
		MolajoToolbarHelper::divider();
		// Get the help information for the template item.

		$lang = MolajoFactory::getLanguage();

		$help = $this->get('Help');
		if ($lang->hasKey($help->url)) {
			$debug = $lang->setDebug(false);
			$url = MolajoText::_($help->url);
			$lang->setDebug($debug);
		}
		else {
			$url = null;
		}
		MolajoToolbarHelper::help($help->key, false, $url);
	}
}
