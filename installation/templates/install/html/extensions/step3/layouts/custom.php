<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
var_dump($this->setup);
?>
<div class="inner">

    <h2>Database Setup</h2>

    <p>Enter your database connection details below. Contact your host if you are not sure what these are.</p>

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

        <ol class="list-reset forms">
            <li>
                <span class="inner-wrap">
                    <label for="hostname" class="inlined"><?php echo MolajoText::_('Host name') ?></label>
                    <input type="text" class="input-text" id="hostname" name="hostname" title="<?php echo MolajoText::_('Host name') ?>" value="<?php echo $this->setup['hostname'] ?>" />
                    <span class="note"><?php echo MolajoText::_('This is usually <b>localhost</b>.') ?></span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="db_scheme" class="inlined"><?php echo MolajoText::_('Database name') ?></label>
                    <input type="text" class="input-text" id="db_scheme" name="db_scheme" title="<?php echo MolajoText::_('Database name') ?>" value="<?php echo $this->setup['db_scheme'] ?>" />
                    <span class="note"><?php echo MolajoText::_('The name of the database you are installing Molajo on.') ?></span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="db_username" class="inlined"><?php echo MolajoText::_('Username') ?></label>
                    <input type="text" class="input-text" id="db_username" name="db_username" title="Username" value="<?php echo $this->setup['db_username'] ?>" />
                    <span class="note"><?php echo MolajoText::_('Your MySQL database username.') ?></span>
                </span>
            </li>
            <li>
                <span class="inner-wrap">
                    <label for="db_password" class="inlined"><?php echo MolajoText::_('Password') ?></label>
                    <input type="text" class="input-text" id="db_password" name="db_password" title="<?php echo MolajoText::_('Password') ?>" value="<?php echo $this->setup['db_password'] ?>" />
                    <span class="note"><?php echo MolajoText::_('Your MySQL database password.') ?></span>
                </span>
            </li>
        </ol>

        <ol class="list-rest radios">
            <li>
                <span class="label"><?php echo MolajoText::_('Database type') ?></span>
                <label class="radio-left" for="mysql"><input name="dbtype" id="mysql" value="myql" type="radio"><?php echo MolajoText::_('MySQL') ?></label>
                <label class="radio-right label-selected" for="mysqli"><input name="dbtype" id="mysqli" value="mysqli" type="radio" checked="checked"><?php echo MolajoText::_('MySQLi') ?></label>
                <span class="note"><?php echo MolajoText::_('MySQLi is recommended, but not all hosts support it. <a href="#">Learn more</a>.') ?></span>
            </li>
            <li>
                <span class="label"><?php echo MolajoText::_('Existing database') ?></span>
                <label class="radio-left" for="remove"><input name="existingdb" id="remove" value="remove" type="radio"><?php echo MolajoText::_('Remove') ?></label>
                <label class="radio-right label-selected" for="backup"><input name="existingdb" id="backup" value="backup" type="radio" checked="checked"><?php echo MolajoText::_('Backup') ?></label>
                <span class="note alt"><?php echo MolajoText::_('If you have an existing database with the same name, would you like it to be replaced or backed up.') ?></span>
            </li>
            <li>
                <span class="label"><?php echo MolajoText::_('Sample Data') ?></span>
                <label class="radio-left" for="sample-data"><input name="sample-data" id="install" value="1" type="radio" checked="checked"><?php echo MolajoText::_('Yes') ?></label>
                <label class="radio-right label-selected" for="backup"><input name="sample-data" id="install" value="0" type="radio"><?php echo MolajoText::_('No') ?></label>
                <span class="note alt"><?php echo MolajoText::_('Installing sample data is strongly recommended for beginners.') ?></span>
            </li>
        </ol>

    <div id="actions">
        <!--a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=step2" class="btn-secondary">&laquo; <strong>P</strong>revious</a-->
        <!--a href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=display&layout=step4" class="btn-primary"><strong>N</strong>ext &raquo;</a-->
        <button type="submit" class="btn-secondary" name="layout" value="step2"><?php echo MolajoText::_('Previous') ?></button>
        <button type="submit" class="btn-primary" name="layout" value="step4"><?php echo MolajoText::_('Next') ?></button>
    </div>
    </form>
</div>