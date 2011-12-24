<?php
/**
 * @package     Molajo
 * @subpackage  System
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

//echo '<pre>';var_dump(MolajoFactory::getLanguage());echo '</pre>';
if (MolajoFactory::getApplication()->get('html5', true) === true): ?>
<!DOCTYPE html>
    <?php else: ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MOLAJO_BASE_FOLDER; ?>"
      lang="<?php echo MolajoFactory::getLanguage()->language; ?>"
      dir="<?php echo MolajoFactory::getLanguage()->direction; ?>">
<head>
    <doc:include type="head"/>
</head>
<body>
<?php include $template_page_include; ?>
</body>
</html>