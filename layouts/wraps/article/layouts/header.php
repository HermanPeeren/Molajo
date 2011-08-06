<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C)  2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ($this->params->get('html5', true) === true) :
    $headertype = 'article';
else :
    $headertype = 'div';
endif;
include MOLAJO_LAYOUTS_COMMON.'/headertype.php';
include MOLAJO_LAYOUTS_COMMON.'/headings.php';