<?php
/**
 * @version     $id: router.php
 * @package     Molajo
 * @subpackage  Router
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * ArticlesBuildRoute
 *
 * Build the route for the com_articles component
 *
 * @param $query
 * @return array
 * @since 1.0
 */
function ArticlesBuildRoute(&$query)
{
    $router = new MolajoRouterBuild();
    return $router->buildRoute(&$query, 'com_articles', 'article', 'articles', 'Article', '#__articles');
}

/**
 * ArticlesParseRoute
 *
 * Parse the segments of a URL.
 *
 * called out of MolajoRouterSite::_parseSefRoute()
 *
 * @param  $query
 * @return array
 * since 1.0
 */
function ArticlesParseRoute ($segments)
{
    $router = new MolajoRouterParse();
    return $router->parseRoute($segments, 'com_articles', 'article', 'articles', 'Article', '#__articles');
}