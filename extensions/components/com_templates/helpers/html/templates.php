<?php
/**
 * @version		$Id: templates.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 */
class MolajoHTMLTemplates
{
	/**
	 * Display the thumb for the template.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function thumb($template, $application_id = 0)
	{
		$application		= JApplicationHelper::getApplicationInfo($application_id);
		$basePath	= $application->path.'/templates/'.$template;
		$baseUrl	= ($application_id == 0) ? JUri::root(true) : JUri::root(true).'/administrator';
		$thumb		= $basePath.'/template_thumbnail.png';
		$preview	= $basePath.'/template_preview.png';
		$html		= '';

		if (file_exists($thumb))
		{
			$applicationPath = ($application_id == 0) ? '' : 'administrator/';
			$thumb	= $applicationPath.'templates/'.$template.'/template_thumbnail.png';
			$html	= MolajoHTML::_('image',$thumb,MolajoText::_('COM_TEMPLATES_PREVIEW'));
			if (file_exists($preview))
			{
				$preview	= $baseUrl.'/templates/'.$template.'/template_preview.png';
				$html		= '<a href="'.$preview.'" class="modal" title="'.MolajoText::_('COM_TEMPLATES_CLICK_TO_ENLARGE').'">'.$html.'</a>';
			}
		}

		return $html;
	}
}