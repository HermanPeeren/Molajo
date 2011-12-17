<?php
/**
 * @version        $Id: default_filter.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 * * * @since        1.0
 */

// no direct access
defined('_JEXEC') or die;

?>
<fieldset id="filter-bar">
    <div class="filter-search fltlft">
        <?php foreach ($this->form->getFieldSet('search') as $name): ?>
        <?php if (!$name->hidden): ?>
            <?php echo $name->label; ?>
            <?php endif; ?>
        <?php echo $name->input; ?>
        <?php endforeach; ?>
    </div>
    <div class="filter-select fltrt">
        <?php foreach ($this->form->getFieldSet('select') as $name): ?>
        <?php if (!$name->hidden): ?>
            <?php echo $name->label; ?>
            <?php endif; ?>
        <?php echo $name->input; ?>
        <?php endforeach; ?>
    </div>
</fieldset>
<div class="clr"></div>
