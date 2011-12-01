<?php
/**
 * @package     Molajo
 * @subpackage  Molajo URLs Keyword Links
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport( 'joomla.plugin.plugin' );

class URLsKeywordLinks extends MolajoApplicationPlugin	{
	
	function onPrepareContent( &$article, &$parameters, $limitstart )
	{
			
	/**
	 * 	Article View, only
	 */
		$view = JRequest::getVar('view','article');
		if ($view !== 'article') {
			return;
		}
				
	/**
	 * rel="canonical" http://googlewebmastercentral.blogspot.com/2009/02/specify-your-canonical.html
	 */		
		$document =& MolajoFactory::getDocument();

		$uri =& MolajoFactory::getURI();
		$query = $uri->getQuery(true);
		$urlhost = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$ArticleURL = $urlhost.$article->readmore_link;
						
		$document =& MolajoFactory::getDocument();
		$document->addHeadLink($ArticleURL, 'canonical', 'rel', '');
    }
}
?>