<?php
/**
 * @package     Molajo
 * @subpackage  Entry point
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$current_folder = basename(dirname(__FILE__));
require_once PLATFORM_MOLAJO_MVC . '/entry.php';

class CommentsController extends MolajoController
{
}

class CommentsControllerEdit extends MolajoControllerEdit
{
}

class CommentsControllerMultiple extends MolajoControllerMultiple
{
}

class CommentsViewDisplay extends MolajoView
{
}

class CommentsViewEdit extends MolajoViewEdit
{
}

class CommentsTableComment extends MolajoTableContent
{
}

class CommentsModelDisplay extends MolajoModelDisplay
{
}

class CommentsModelEdit extends MolajoModelEdit
{
}

class CommentsMolajoACL extends MolajoACL
{
}

class CommentsHelper
{
}
