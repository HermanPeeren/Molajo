<?php
/**
 * @version     $id: responses
 * @package     Molajo
 * @subpackage  HTML Class
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * @package        Joomla.Administrator
 * @subpackage    response
 */
abstract class MolajoHtmlResponse
{
    /**
     * @param    int $value    The state value
     * @param    int $i
     */
    function spammed($value = 0, $i, $canChange = true)
    {
        // Array of image, task, title, action
        $states = array(
            0 => array('disabled.png', 'responses.spammed', 'RESPONSES_UNSPAMMED', 'CONTENT_TOGGLE_TO_SPAM'),
            1 => array('spammed.png', 'responses.unspammed', 'RESPONSES_SPAMMED', 'CONTENT_TOGGLE_TO_UNSPAM'),
        );
        $state = JArrayHelper::getValue($states, (int)$value, $states[1]);
        $html = JHTML::_('image', 'admin/' . $state[0], MolajoTextHelper::_($state[2]), NULL, true);
        if ($canChange) {
            $html = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" title="' . MolajoTextHelper::_($state[3]) . '">'
                    . $html . '</a>';
        }

        return $html;
    }
}