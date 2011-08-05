<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$headerLevel = isset($attribs['level']) ? (int) $attribs['level'] : 1;
$headerClass = 'TBD'; ?>
<?php if ($html5) { ?>
<footer class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php } else { ?>
<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php } ?>
<?php if ($module->showtitle) : ?>
    <h<?php echo $headerLevel; ?> class="<?php echo $headerClass; ?>">
        <?php echo $module->title; ?>
<?php echo '</h'.$headerLevel; ?>>
<?php endif; ?>