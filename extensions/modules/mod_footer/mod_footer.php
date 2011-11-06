<?php
/**
 * @package     Molajo
 * @subpackage  Footer
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** initialise  */
$tmpobj = new JObject();
$layout = $params->def('layout', 'admin_footer');
$wrap = $params->def('wrap', 'footer');

/**
 *  Line 1
 */
if (JString::strpos(MolajoText :: _('MOD_FOOTER_LINE1'), '%date%')) {
	$line1 = str_replace('%date%', MolajoFactory::getDate()->format('Y'), MolajoText :: _('MOD_FOOTER_LINE1'));
} else {
    $line1 = MolajoText :: _('MOD_FOOTER_LINE1');
}
if (JString::strpos($line1, '%sitename%')) {
	$line1 = str_replace('%sitename%', MolajoFactory::getApplication()->getConfig('sitename', 'Molajo'), $line1);
}
$tmpobj->set('line1', $line1);

/**
 *  Line 2
 */
$link = $params->def('link', 'http://molajo.org');
$linked_text = $params->def('linked_text', 'Molajo&#153;');
$remaining_text = $params->def('remaining_text', ' is free software.');
$version = $params->def('version', MolajoText::_(MOLAJOVERSION));

$tmpobj->set('link', $link);
$tmpobj->set('linked_text', $linked_text);
$tmpobj->set('remaining_text', $remaining_text);
$tmpobj->set('version', $version);

$line2 = '<a href="'.$link.'">'.$linked_text.' v.'.$version.'</a>';
$line2 .= $remaining_text;
$tmpobj->set('line2', $line2);

/** save recordset */
$rowset[] = $tmpobj;