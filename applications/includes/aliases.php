<?php
/**
 * @package     Molajo
 * @subpackage  Load
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

function hook() {
    echo 'is this all a hook is? interesting.';
}
// todo: automate service names
Class AccessService extends MolajoAccessService {}
Class ApplicationService extends MolajoApplicationService {}
Class DateService extends MolajoDateService {}
Class DispatcherService extends MolajoDispatcherService {}
Class DoctrineService extends MolajoDoctrineService {}
Class FilesystemService extends MolajoFileSystemService {}
Class FilterService extends MolajoFilterService {}
Class ImageService extends MolajoImageService {}
Class JDatabaseService extends MolajoJDatabaseService {}
Class LanguageService extends MolajoLanguageService {}
Class MailService extends MolajoMailService {}
Class MessageService extends MolajoMessageService {}
Class SecurityService extends MolajoSecurityService {}
Class SessionService extends MolajoSessionService {}
Class TextService extends MolajoTextService {}
Class UrlService extends MolajoUrlService {}
Class UserService extends MolajoUserService {}

Class AssetHelper extends MolajoAssetHelper {}
Class ComponentHelper extends MolajoComponentHelper {}
Class ContentHelper extends MolajoContentHelper {}
Class ExtensionHelper extends MolajoExtensionHelper {}
Class InstallerHelper extends MolajoInstallerHelper {}
Class LanguageHelper extends MolajoLanguageHelper {}
Class LoadHelper extends MolajoLoadHelper {}
Class MenuHelper extends MolajoMenuHelper {}
Class ModuleHelper extends MolajoModuleHelper {}
Class SiteHelper extends MolajoSiteHelper {}
Class ThemeHelper extends MolajoThemeHelper {}
Class UserHelper extends MolajoUserHelper {}
Class ViewHelper extends MolajoViewHelper {}

/**
 *  Molajo Base Class
 */
class Molajo extends MolajoBase
{
    public static function Site()
    {
        return MolajoBase::getSite();
    }
    public static function Application()
    {
        return MolajoBase::getApplication();
    }
    public static function Request($request = null, $override_request_url = null, $override_asset_id = null)
    {
        return MolajoBase::getRequest($request, $override_request_url, $override_asset_id);
    }
    public static function Parser()
    {
        return MolajoBase::getParser();
    }
    public static function Renderer()
    {
        return MolajoBase::getRenderer();
    }
    public static function Responder()
    {
        return MolajoBase::getResponder();
    }
    public static function Services()
    {
        return MolajoBase::getServices();
    }
}

abstract class JFactory extends MolajoBase
{
}


/**
 *  Molajo Class for alias creation, ex Molajo::User
 */
class Registry extends JRegistry {}
class Input extends JInput {}
class FilterInput extends JFilterInput {}
class FilterOutput extends JFilterOutput {}
