<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();
$doc = JFactory::getDocument();

// Include the component HTML helpers.
JHtml::addIncludePath(MOLAJO_PATH_COMPONENT.'/helpers/html');

// Add Stylesheets
$doc->addStyleSheet('../media/system/css/system.css');
$doc->addStyleSheet('template/css/template.css');

if ($this->direction == 'rtl') {
	$doc->addStyleSheet('template/css/template_rtl.css');
}

// Load the JavaScript behaviors
JHtml::_('behavior.framework', true);
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('script', 'installation/template/js/installation.js', true, false, false, false);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>
		<jdoc:include type="head" />

		<!--[if IE 7]>
			<link href="template/css/ie7.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<script type="text/javascript">
			var baseUrl = '<?php echo JURI::current(); ?>';
 		</script>
	</head>
	<body>
		<div id="header1">
			<div id="header2">
				<div id="header3">
					<span class="logo"><a href="http://molajo.org" target="_blank"><img src="template/images/logo.png" alt="Molajo" /></a></span>
					<span>Molajo <?php echo MOLAJOVERSION; ?> <?php echo JText::_('INSTL_INSTALLATION') ?></span>
				</div>
			</div>
		</div>
		<jdoc:include type="message" />
		<div id="content-box">
			<div id="content-pad">
				<div id="stepbar">
					<?php echo JHtml::_('installation.stepbar'); ?>
					<div class="box"></div>
				</div>
				<div id="warning">
					<noscript>
						<div id="javascript-warning">
							<?php echo JText::_('INSTL_WARNJAVASCRIPT'); ?>
						</div>
					</noscript>
				</div>
				<div id="right">
					<div id="rightpad">
						<jdoc:include type="installation" />
					</div>
				</div>
				<div class="clr"></div>
			</div>
		</div>
		<div id="copyright">
			<?php $molajo= '<a href="http://molajo.org">Molajo&#174;</a>';
			echo JText::sprintf('JGLOBAL_ISFREESOFTWARE', $molajo) ?>
		</div>
	</body>
</html>