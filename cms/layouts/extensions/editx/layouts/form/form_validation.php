<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Single View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

?>
<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        if (task == 'resource.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
        <?php echo $this->form->getField('content_text')->save(); ?>
            Joomla.submitform(task, document.getElementById('item-form'));
        } else {
            alert('<?php echo $this->escape(MolajoTextHelper::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
        }
    }
</script>