<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldCalendar extends MolajoFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $type = 'Calendar';

	/**
	 * Method to get the field calendar markup.
	 *
	 * @return  string   The field calendar markup.
	 * @since   1.0
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$format = $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d';

		// Build the attributes array.
		$attributes = array();
		if ($this->element['size']) {
			$attributes['size'] = (int) $this->element['size'];
		}
		if ($this->element['maxlength']) {
			$attributes['maxlength'] = (int) $this->element['maxlength'];
		}
		if ($this->element['class']) {
			$attributes['class'] = (string) $this->element['class'];
		}
		if ((string) $this->element['readonly'] == 'true') {
			$attributes['readonly'] = 'readonly';
		}
		if ((string) $this->element['disabled'] == 'true') {
			$attributes['disabled'] = 'disabled';
		}
		if ($this->element['onchange']) {
			$attributes['onchange'] = (string) $this->element['onchange'];
		}

		// Handle the special case for "now".
		if (strtoupper($this->value) == 'NOW') {
			$this->value = strftime($format);
		}

		// Get some system objects.
		$config = MolajoFactory::getConfig();
		$user	= MolajoFactory::getUser();

		// If a known filter is given use it.
		switch (strtoupper((string) $this->element['filter']))
		{
			case 'SERVER_UTC':
				// Convert a date to UTC based on the server timezone.
				if (intval($this->value)) {
					// Get a date object based on the correct timezone.
					$date = MolajoFactory::getDate($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone($config->get('offset')));

					// Transform the date string.
					$this->value = $date->toMySQL(true);
				}
				break;

			case 'USER_UTC':
				// Convert a date to UTC based on the user timezone.
				if (intval($this->value)) {
					// Get a date object based on the correct timezone.
					$date = MolajoFactory::getDate($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));

					// Transform the date string.
					$this->value = $date->toMySQL(true);
				}
				break;
		}

		return MolajoHTML::_('calendar', $this->value, $this->name, $this->id, $format, $attributes);
	}
}
