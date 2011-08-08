<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<?php if ($this->row->label == "") {
} else { ?>
<label
    class="hasTip"
    <?php if ($this->row->id == "") { } else { echo ' for="'.htmlspecialchars($this->row->id, ENT_COMPAT, 'UTF-8').'"'; } ?>
    <?php if ($this->row->description == "") { } else { echo ' name="'.htmlspecialchars(JText::_($this->row->description)).'"'; } ?>
    <?php echo JText::_(($this->row->label), ENT_COMPAT, 'UTF-8'); ?>
    >
    <span>
<?php } ?>
	    <input
			type="<?php echo $this->row->type; ?>"
			value="<?php echo htmlspecialchars($this->row->value, ENT_COMPAT, 'UTF-8'); ?>"
			<?php if ($this->row->id == "") { } else { echo ' id="'.htmlspecialchars($this->row->id, ENT_COMPAT, 'UTF-8').'"'; } ?>
			<?php if ($this->row->class == "") { } else { echo ' class="'.htmlspecialchars($this->row->class, ENT_COMPAT, 'UTF-8').'"'; } ?>
			<?php if ($this->row->name == "") { } else { echo ' name="'.$this->row->name.'"'; } ?>
			<?php echo $this->row->required; ?>
			<?php echo $this->row->pattern; ?>
			<?php echo $this->row->maxlength; ?>
			<?php echo $this->row->min; ?>
			<?php echo $this->row->max; ?>
			<?php echo $this->row->step; ?>
			<?php echo $this->row->size; ?>
			<?php echo $this->row->readonly; ?>
			<?php echo $this->row->disabled; ?>
			<?php echo $this->row->multiple; ?>
			<?php echo $this->row->placeholder; ?>
			<?php echo $this->row->autocomplete; ?>
			<?php echo $this->row->autofocus; ?>
			<?php echo $this->row->list; ?>
	    />
<?php if ($this->row->label == "") {
} else { ?>
	</span>
</label>
<?php }