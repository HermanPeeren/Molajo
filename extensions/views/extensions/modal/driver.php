<?php
/**
 * @version     $id: driver.php
 * @package     Molajo
 * @subpackage  View Driver
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** custom css/js **/
MolajoController::getApplication()->addStyleSheet('../media/' . $this->request['option'] . '/css/administrator.css');

/** component parameters **/
$this->state = JComponentHelper::getParameters($this->request['option']);

/** output **/
include $this->viewHelper->getPath('modal_item.php');
