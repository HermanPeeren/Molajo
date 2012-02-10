<?php
/**
 * @package     Molajo
 * @subpackage  HHelper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Language
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoLanguageHelper
{
    /**
     * Creates a language object
     *
     * @return  MolajoLanguage object
     *
     * @since   1.0
     */
    public static function get($language = null)
    {
        if (trim($language) == '') {
            $language = Molajo::Configuration()->get('language', 'en-GB');
        }
        $debug = Molajo::Configuration()->get('debug_language', 0);
        return MolajoLanguage::getInstance($language, $debug);
    }

    /**
     * Builds a list of the system languages which can be used in a select option
     *
     *  $_SERVER['HTTP_ACCEPT_LANGUAGE'];
     * @param   string   $actualLanguage  Client key for the area
     * @param   string   $basepath        Base path to use
     * @param   boolean  $caching         True if caching is used
     * @param   array    $installed       An array of arrays (text, value, selected)
     *
     * @return  array  List of system languages
     *
     * @since   1.0
     */
    public static function createLanguageList($actualLanguage, $basePath = MOLAJO_EXTENSIONS_LANGUAGES, $caching = false, $installed = false)
    {
        $list = array();
        $languages = MolajoLanguage::getKnownLanguages($basePath);

        if (MOLAJO_APPLICATION_ID == 0) {
            $installed == false;

        } elseif ($installed === true) {
            $installed_languages = ExtensionService::get(2);
        }

        foreach ($languages as $language => $metadata)
        {
            $option = array();

            $option['text'] = $metadata['name'];
            $option['value'] = $language;
            if ($language == $actualLanguage) {
                $option['selected'] = 'selected="selected"';
            }
            $list[] = $option;
        }

        return $list;
    }

    /**
     * getLanguage
     *
     * Tries to detect the language.
     *
     * @return  string  locale or null if not found
     * @since   1.0
     */
    public static function getLanguage($options)
    {
        /** 1. request */
        if (empty($options['language'])) {
            $language = JRequest::getString('language', null);
            if ($language && LanguageService::exists($language)) {
                $options['language'] = $language;
            }
        }

        /** 2. user option for user */
        if (empty($options['language'])) {
            $language = Molajo::Application()->get('User', '', 'services')->getParameter('language');
            if ($language && LanguageService::exists($language)) {
                $options['language'] = $language;
            }
        }

        /** 3. browser detection */
        if (empty($options['language'])) {
            if ($detect_browser && empty($options['language'])) {
                $language = LanguageService::detectLanguage();
                if ($language && LanguageService::exists($language)) {
                    $options['language'] = $language;
                }
            }
        }

        /** 4. site default for application */
        if (empty($options['language'])) {
            $language = $config->get('language', 'en-GB');
            if ($language && LanguageService::exists($language)) {
                $options['language'] = $language;
            }
        }

        /** 5. default */
        if (LanguageService::exists($options['language'])) {
        } else {
            $options['language'] = 'en-GB';
        }

    }

    /**
     * Tries to detect the language.
     *
     * @return  string  locale or null if not found
     * @since   1.0
     */
    public static function detectLanguage()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        } else {
            return null;
        }

        $systemLangs = self::getLanguages();
        foreach ($browserLangs as $browserLang)
        {
            // Slice out the part before ; on first step, the part before - on second, place into array
            $browserLang = substr($browserLang, 0, strcspn($browserLang, ';'));
            $primary_browserLang = substr($browserLang, 0, 2);
            foreach ($systemLangs as $systemLang)
            {
                // Take off 3 letters iso code languages as they can't match browsers' languages and default them to en
                $Jinstall_lang = $systemLang->lang_code;

                if (strlen($Jinstall_lang) < 6) {
                    if (strtolower($browserLang) == strtolower(substr($systemLang->lang_code, 0, strlen($browserLang)))) {
                        return $systemLang->lang_code;
                    }
                    else if ($primary_browserLang == substr($systemLang->lang_code, 0, 2)) {
                        $primaryDetectedLang = $systemLang->lang_code;
                    }
                }
            }

            if (isset($primaryDetectedLang)) {
                return $primaryDetectedLang;
            }
        }

        return null;
    }

    /**
     * Get available languages
     *
     * @param   string  $key  Array key
     *
     * @return  array  An array of published languages
     *
     * @since   1.0
     */
    public static function getLanguages($key = 'default')
    {
        static $languages;

        if (empty($languages)) {

            // Installation uses available languages
            if (MOLAJO_APPLICATION_ID == 0) {
                $languages[$key] = array();
                $knownLangs = LanguageService::getKnownLanguages(MOLAJO_BASE_FOLDER);
                foreach ($knownLangs as $metadata)
                {
                    // take off 3 letters iso code languages as they can't match browsers' languages and default them to en
                    $languages[$key][] = new Registry(array('lang_code' => $metadata['tag']));
                }
            } else {
                $languages['default'] = ExtensionService::get(2);
                $languages['sef'] = array();
                $languages['lang_code'] = array();

                if (isset($languages['default'][0])) {
                    foreach ($languages['default'] as $lang) {
                        $languages['sef'][$lang->sef] = $lang;
                        $languages['lang_code'][$lang->lang_code] = $lang;
                    }
                }
            }
        }

        return $languages[$key];
    }
}
