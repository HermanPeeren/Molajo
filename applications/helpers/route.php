<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Route handling class
 *
 * @package     Joomla.Platform
 * @since       11.1
 */
class MolajoRouteHelper
{
    /**
     * Translates an internal Joomla URL to a humanly readible URL.
     *
     * @param   string   Absolute or Relative URI to Joomla resource.
     * @param   boolean  Replace & by &amp; for XML compilance.
     * @param   integer  Secure state for the resolved URI.
     *        1: Make URI secure using global secure site URI.
     *        0: Leave URI in the same secure state as it was passed to the function.
     *        -1: Make URI unsecure using the global unsecure site URI.
     * @return  The translated humanly readible URL.
     */
    public static function _($url, $xhtml = true, $ssl = null)
    {
        // Get the router.

        $router = MolajoController::getApplication()->getRouter();

        // Make sure that we have our router
        if (!$router) {
            return null;
        }

        if ((strpos($url, '&') !== 0) && (strpos($url, 'index.php') !== 0)) {
            return $url;
        }

        // Build route.
        $uri = $router->build($url);
        $url = $uri->toString(array('path', 'query', 'fragment'));

        // Replace spaces.
        $url = preg_replace('/\s/u', '%20', $url);

        /*
           * Get the secure/unsecure URLs.
           *
           * If the first 5 characters of the BASE are 'https', then we are on an ssl connection over
           * https and need to set our secure URL to the current request URL, if not, and the scheme is
           * 'http', then we need to do a quick string manipulation to switch schemes.
           */
        if ((int)$ssl) {
            $uri = JURI::getInstance();

            // Get additional parts.
            static $prefix;
            if (!$prefix) {
                $prefix = $uri->toString(array('host', 'port'));
            }

            // Determine which scheme we want.
            $scheme = ((int)$ssl === 1) ? 'https' : 'http';

            // Make sure our URL path begins with a slash.
            if (!preg_match('#^/#', $url)) {
                $url = '/' . $url;
            }

            // Build the URL.
            $url = $scheme . '://' . $prefix . $url;
        }

        if ($xhtml) {
            $url = htmlspecialchars($url);
        }

        return $url;
    }
}
