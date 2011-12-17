<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $parameters->def('layout', 'quick_list');
$wrap = $parameters->def('wrap', 'div');

require_once dirname(__FILE__) . '/helper.php';
$items = modContentHelper::getList($parameters, $user);