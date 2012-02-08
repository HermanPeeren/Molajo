<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoApplicationHelper
{
    /**
     * @var null $_applications
     *
     * @static
     * @since 1.0
     */
    protected static $_applications = null;

    /**
     * getApplicationInfo
     *
     * Retrieves Application info from database

     * @param  $application
     *
     * @return  boolean
     * @since   1.0
     */
    public static function getApplicationInfo()
    {
        $id = null;
        if (self::$_applications === null) {

            $obj = new stdClass();

            if ($name == 'installation') {
                $id = 0;
                $obj->id = 0;
                $obj->name = 'installation';
                $obj->path = 'installation';
                $obj->asset_type_id = MOLAJO_ASSET_TYPE_BASE_APPLICATION;
                $obj->description = '';
                $obj->custom_fields = '';
                $obj->parameters = '';
                $obj->metadata = '';

                self::$_applications[0] = clone $obj;

            } else {

                $db = Molajo::Jdb();

                $query = $db->getQuery(true);

                $query->select($db->namequote('id'));
                $query->select($db->namequote('asset_type_id'));
                $query->select($db->namequote('name'));
                $query->select($db->namequote('path'));
                $query->select($db->namequote('description'));
                $query->select($db->namequote('custom_fields'));
                $query->select($db->namequote('parameters'));
                $query->select($db->namequote('metadata'));

                $query->from($db->namequote('#__applications'));

                $db->setQuery($query->__toString());

                $query->where($db->namequote('name').' = '.$db->quote($name));

                $results = $db->loadObjectList();
                if ($db->getErrorNum()) {
                    return new MolajoException($db->getErrorMsg());
                }
                if (count($results) == 0) {
                    //amy error;
                }

                foreach ($results as $result) {

                    $obj->id = $result->id;
                    $id = $result->id;
                    $obj->name = $result->name;
                    $obj->path = $result->path;
                    $obj->asset_type_id = $result->asset_type_id;
                    $obj->description = $result->description;
                    $obj->custom_fields = $result->custom_fields;
                    $obj->parameters = $result->parameters;
                    $obj->metadata = $result->metadata;

                    self::$_applications[$id] = clone $obj;
                }
            }
        }

        if (isset(self::$_applications[$id])) {
            return self::$_applications[$id];
        }

        /** unsuccessful */
        return null;
    }
}

/**

    if (defined('MOLAJO_APPLICATION_ID')) {
    } else {
        define('MOLAJO_APPLICATION_ID', $results->id);
    }
 */
