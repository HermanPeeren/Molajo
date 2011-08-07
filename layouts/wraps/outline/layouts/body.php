<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
    <div class="mod-preview">
    <div class="mod-preview-info"><?php echo $this->row->position."[".$this->row->style."]"; ?></div>
    <div class="mod-preview-wrapper">
        <?php echo $this->row->content; ?>
    </div>
</div>