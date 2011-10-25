<?php
/**
 * @version		$Id: default.php 21529 2011-06-11 22:17:15Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Add specific helper files for html generation
MolajoHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$user		= MolajoFactory::getUser();
$userId		= $user->get('id');
$application		= $this->state->get('filter.application_id', 0) ? MolajoText::_('JADMINISTRATOR') : MolajoText::_('JSITE');
$applicationId	= $this->state->get('filter.application_id', 0);
?>
<form action="<?php echo MolajoRoute::_('index.php?option=com_languages&view=installed&application='.$applicationId); ?>" method="post" id="adminForm" name="adminForm">

	<?php if ($this->ftp): ?>
		<?php echo $this->loadTemplate('ftp');?>
	<?php endif; ?>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="20">
					<?php echo MolajoText::_('COM_LANGUAGES_HEADING_NUM'); ?>
				</th>
				<th width="20">
					&#160;
				</th>
				<th width="25%" class="title">
					<?php echo MolajoText::_('COM_LANGUAGES_HEADING_LANGUAGE'); ?>
				</th>
				<th>
					<?php echo MolajoText::_('JCLIENT'); ?>
				</th>
				<th>
					<?php echo MolajoText::_('COM_LANGUAGES_HEADING_DEFAULT'); ?>
				</th>
				<th>
					<?php echo MolajoText::_('MOLAJOVERSION'); ?>
				</th>
				<th>
					<?php echo MolajoText::_('JDATE'); ?>
				</th>
				<th>
					<?php echo MolajoText::_('JAUTHOR'); ?>
				</th>
				<th>
					<?php echo MolajoText::_('COM_LANGUAGES_HEADING_AUTHOR_EMAIL'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->rows as $i => $row) :
			$canCreate	= $user->authorise('core.create',		'com_languages');
			$canEdit	= $user->authorise('core.edit',			'com_languages');
			$canChange	= $user->authorise('core.edit.state',	'com_languages');
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td width="20">
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td width="20">
					<?php echo MolajoHTML::_('languages.id',$i,$row->language);?>
				</td>
				<td width="25%">
					<?php echo $this->escape($row->name); ?>
				</td>
				<td align="center">
					<?php echo $application;?>
				</td>
				<td align="center">
					<?php echo MolajoHTML::_('jgrid.isdefault', $row->published, $i, 'installed.',  !$row->published && $canChange);?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->version); ?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->creationDate); ?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->author); ?>
				</td>
				<td align="center">
					<?php echo $this->escape($row->authorEmail); ?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo MolajoHTML::_('form.token'); ?>
	</div>
</form>
