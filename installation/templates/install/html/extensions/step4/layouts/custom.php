<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="inner">
    <h2>Review</h2>
    <p>You have provided all of the information needed to install Molajo. Press Install when ready to proceed.</p>
    <div class="summary">
        <h3>Site information</h3>
        <ul class="list-reset">
            <li><strong>Site name:</strong> <span><?php echo $this->setup['sitename'] ?></span></li>
            <li><strong>Your name:</strong> <span><?php echo $this->setup['name'] ?></span></li>
            <li><strong>Your email:</strong> <span><?php echo $this->setup['admin_email'] ?></span></li>
            <li><strong>Your password:</strong> <span><?php echo $this->setup['admin_password'] ?></span></li>
        </ul>
    </div>

    <form action="<?php echo JUri::current() ?>" method="post">

        <input type="hidden" name="language"       value="<?php echo $this->setup['language'] ?>">
        <input type="hidden" name="sitename"       value="<?php echo $this->setup['sitename'] ?>">
        <input type="hidden" name="name"           value="<?php echo $this->setup['name'] ?>">
        <input type="hidden" name="admin_email"    value="<?php echo $this->setup['admin_email'] ?>">
        <input type="hidden" name="admin_password" value="<?php echo $this->setup['admin_password'] ?>">
        <input type="hidden" name="hostname"       value="<?php echo $this->setup['hostname'] ?>">
        <input type="hidden" name="db_scheme"      value="<?php echo $this->setup['db_scheme'] ?>">
        <input type="hidden" name="db_username"    value="<?php echo $this->setup['db_username'] ?>">
        <input type="hidden" name="db_password"    value="<?php echo $this->setup['db_password'] ?>">
        <input type="hidden" name="db_prefix"      value="<?php echo $this->setup['db_prefix'] ?>">
        <input type="hidden" name="db_type"        value="<?php echo $this->setup['db_type'] ?>">
        <input type="hidden" name="remove_tables"  value="<?php echo $this->setup['remove_tables'] ?>">
        <input type="hidden" name="install_sample" value="<?php echo $this->setup['install_sample'] ?>">

    <div id="actions">
        <!--a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=step3" class="btn-secondary">&laquo; <strong>P</strong>revious</a-->
        <!--a href="<?php echo JURI::base(); ?>index.php?option=com_installer&task=install" class="btn-primary alt">Install &raquo;</a-->
        <button type="submit" class="btn-secondary" name="layout" value="step3"><?php echo MolajoText::_('Previous') ?></button>
        <button type="submit" class="btn-primary" name="task" value="install"><?php echo MolajoText::_('Next') ?></button>
    </div>

    <form>

</div>
