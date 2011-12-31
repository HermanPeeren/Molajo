<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="header">
    <doc:include type="modules" name="header" wrap="header"/>
    <doc:include type="modules" name="menu" wrap="nav"/>
    <doc:include type="message"/>
</div>
<div class="section">
    <doc:include type="component"/>
</div>
<div class="footer">
    <doc:include type="modules" name="footer" wrap="footer"/>
</div>