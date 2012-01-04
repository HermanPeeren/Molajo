<?php
/**
 * @version        $Id: cache.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;


/**
 * Molajo Page Cache Plugin
 *
 * @package        Joomla.Plugin
 * @subpackage    System.cache
 */
class plgSystemCache extends MolajoPluginHelper
{

    var $_cache = null;

    /**
     * Constructor
     *
     * @access    protected
     * @param    object    $subject The object to observe
     * @param    array    $config  An array that holds the plugin configuration
     * @since    1.0
     */
    function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);

        //Set the language in the class
        $config = MolajoController::getApplication()->get();
        $options = array(
            'defaultgroup' => 'page',
            'browsercache' => $this->parameters->get('browsercache', false),
            'caching' => false,
        );

        jimport('joomla.cache.cache');
        $this->_cache = JCache::getInstance('page', $options);
    }

    /**
     * Converting the site URL to fit to the HTTP request
     *
     */
    function onAfterInitialise()
    {
        global $_PROFILER;

        $user = MolajoController::getUser();

        if (JDEBUG) {
            return;
        }

        if ((int)$user->get('user_id') == 0 && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->_cache->setCaching(true);
        }

        $data = $this->_cache->get();

        if ($data !== false) {
            MolajoController::getApplication()->setBody($data);

            echo MolajoController::getApplication()->toString(MolajoController::getApplication()->get('gzip'));

            if (JDEBUG) {
                $_PROFILER->mark('afterCache');
                echo implode('', $_PROFILER->getBuffer());
            }

            MolajoController::getApplication()->close();
        }
    }

    function onAfterRender()
    {


        if (JDEBUG) {
            return;
        }

        $user = MolajoController::getUser();
        if ((int)$user->get('user_id') == 0) {
            //We need to check again here, because auto-login plugins have not been fired before the first aid check
            $this->_cache->store();
        }
    }
}
