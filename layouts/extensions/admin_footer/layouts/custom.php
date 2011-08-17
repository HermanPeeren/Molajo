<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<p class="copyright">
    <?php echo $this->rowset[0]->line1; ?>
    <span class="version">
        <?php echo $this->rowset[0]->line2; ?>
    </span>
</p>