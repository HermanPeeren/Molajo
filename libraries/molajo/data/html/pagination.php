<?php
/**
 * @package    Molajo
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Pagination Class.  Provides a common interface for content pagination for the
 * Molajo Framework.
 *
 * @package    Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoPagination extends JObject
{
    /**
     * The record number to start dislpaying from.
     *
     * @var int
     */
    public $limitstart = null;

    /**
     * Number of rows to display per page.
     *
     * @var int
     */
    public $limit = null;

    /**
     * Total number of rows.
     *
     * @var int
     */
    public $total = null;

    /**
     * Prefix used for request variables.
     *
     * @var int
     */
    public $prefix = null;

    /**
     * View all flag
     *
     * @var boolean
     */
    protected $_viewall = false;

    /**
     * Additional URL parameters to be added to the pagination URLs generated by the class.  These
     * may be useful for filters and extra values when dealing with lists and GET requests.
     *
     * @var    array
     * @since  1.0
     */
    protected $_additionalUrlParameters = array();

    /**
     * Constructor.
     *
     * @param   integer  The total number of items.
     * @param   integer  The offset of the item to start at.
     * @param   integer  The number of items to display per page.
     * @param   string   The prefix used for request variables.
     */
    function __construct($total, $limitstart, $limit, $prefix = '')
    {
        // Value/type checking.
        $this->total = (int)$total;
        $this->limitstart = (int)max($limitstart, 0);
        $this->limit = (int)max($limit, 0);
        $this->prefix = $prefix;

        if ($this->limit > $this->total) {
            $this->limitstart = 0;
        }

        if (!$this->limit) {
            $this->limit = $total;
            $this->limitstart = 0;
        }

        /*
           * If limitstart is greater than total (i.e. we are asked to display records that don't exist)
           * then set limitstart to display the last natural page of results
           */
        if ($this->limitstart > $this->total - $this->limit) {
            $this->limitstart = max(0, (int)(ceil($this->total / $this->limit) - 1) * $this->limit);
        }

        // Set the total pages and current page values.
        if ($this->limit > 0) {
            $this->set('pages.total', ceil($this->total / $this->limit));
            $this->set('pages.current', ceil(($this->limitstart + 1) / $this->limit));
        }

        // Set the pagination iteration loop values.
        $displayedPages = 10;
        $this->set('pages.start', $this->get('pages.current') - ($displayedPages / 2));
        if ($this->get('pages.start') < 1) {
            $this->set('pages.start', 1);
        }
        if (($this->get('pages.start') + $displayedPages) > $this->get('pages.total')) {
            $this->set('pages.stop', $this->get('pages.total'));
            if ($this->get('pages.total') < $displayedPages) {
                $this->set('pages.start', 1);
            } else {
                $this->set('pages.start', $this->get('pages.total') - $displayedPages + 1);
            }
        } else {
            $this->set('pages.stop', ($this->get('pages.start') + $displayedPages - 1));
        }

        // If we are viewing all records set the view all flag to true.
        if ($limit == 0) {
            $this->_viewall = true;
        }
    }

    /**
     * Method to set an additional URL parameter to be added to all pagination class generated
     * links.
     *
     * @param   string   $key    The name of the URL parameter for which to set a value.
     * @param   mixed    $value    The value to set for the URL parameter.
     *
     * @return  mixed    The old value for the parameter.
     *
     * @since   1.0
     */
    public function setAdditionalUrlParam($key, $value)
    {
        // Get the old value to return and set the new one for the URL parameter.
        $result = isset($this->_additionalUrlParameters[$key]) ? $this->_additionalUrlParameters[$key] : null;

        // If the passed parameter value is null unset the parameter, otherwise set it to the given value.
        if ($value === null) {
            unset($this->_additionalUrlParameters[$key]);
        }
        else {
            $this->_additionalUrlParameters[$key] = $value;
        }

        return $result;
    }

    /**
     * Method to get an additional URL parameter (if it exists) to be added to
     * all pagination class generated links.
     *
     * @param   string   $key    The name of the URL parameter for which to get the value.
     *
     * @return  mixed    The value if it exists or null if it does not.
     *
     * @since   1.0
     */
    public function getAdditionalUrlParam($key)
    {
        $result = isset($this->_additionalUrlParameters[$key]) ? $this->_additionalUrlParameters[$key] : null;

        return $result;
    }

    /**
     * Return the rationalised offset for a row with a given index.
     *
     * @param   integer  $index The row index
     * @return  integer      Rationalised offset for a row with a given index.
     * @since   1.0
     */
    public function getRowOffset($index)
    {
        return $index + 1 + $this->limitstart;
    }

    /**
     * Return the pagination data object, only creating it if it doesn't already exist.
     *
     * @return  object   Pagination data object.
     * @since   1.0
     */
    public function getData()
    {
        static $data;
        if (!is_object($data)) {
            $data = $this->_buildDataObject();
        }
        return $data;
    }

    /**
     * Create and return the pagination pages counter string, ie. Page 2 of 4.
     *
     * @return  string   Pagination pages counter string.
     * @since   1.0
     */
    public function getPagesCounter()
    {
        // Initialise variables.
        $html = null;
        if ($this->get('pages.total') > 1) {
            $html .= MolajoText::sprintf('MOLAJO_HTML_PAGE_CURRENT_OF_TOTAL', $this->get('pages.current'), $this->get('pages.total'));
        }
        return $html;
    }

    /**
     * Create and return the pagination result set counter string, ie. Results 1-10 of 42
     *
     * @return  string   Pagination result set counter string.
     * @since   1.0
     */
    public function getResultsCounter()
    {
        // Initialise variables.
        $html = null;
        $fromResult = $this->limitstart + 1;

        // If the limit is reached before the end of the list.
        if ($this->limitstart + $this->limit < $this->total) {
            $toResult = $this->limitstart + $this->limit;
        }
        else {
            $toResult = $this->total;
        }

        // If there are results found.
        if ($this->total > 0) {
            $msg = MolajoText::sprintf('MOLAJO_HTML_RESULTS_OF', $fromResult, $toResult, $this->total);
            $html .= "\n".$msg;
        }
        else {
            $html .= "\n".MolajoText::_('MOLAJO_HTML_NO_RECORDS_FOUND');
        }

        return $html;
    }

    /**
     * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x.
     *
     * @return  string   Pagination page list string.
     * @since    1.0
     */
    public function getPagesLinks()
    {
        $app = MolajoFactory::getApplication();

        // Build the page navigation list.
        $data = $this->_buildDataObject();

        $list = array();
        $list['prefix'] = $this->prefix;

        $itemOverride = false;
        $listOverride = false;

        $chromePath = MOLAJO_EXTENSION_TEMPLATES.'/'.$app->getTemplate().'/'.'html'.'/'.'pagination.php';
        if (file_exists($chromePath)) {
            require_once $chromePath;
            if (function_exists('pagination_item_active') && function_exists('pagination_item_inactive')) {
                $itemOverride = true;
            }
            if (function_exists('pagination_list_render')) {
                $listOverride = true;
            }
        }

        // Build the select list
        if ($data->all->base !== null) {
            $list['all']['active'] = true;
            $list['all']['data'] = ($itemOverride) ? pagination_item_active($data->all)
                    : $this->_item_active($data->all);
        } else {
            $list['all']['active'] = false;
            $list['all']['data'] = ($itemOverride) ? pagination_item_inactive($data->all)
                    : $this->_item_inactive($data->all);
        }

        if ($data->start->base !== null) {
            $list['start']['active'] = true;
            $list['start']['data'] = ($itemOverride) ? pagination_item_active($data->start)
                    : $this->_item_active($data->start);
        } else {
            $list['start']['active'] = false;
            $list['start']['data'] = ($itemOverride) ? pagination_item_inactive($data->start)
                    : $this->_item_inactive($data->start);
        }
        if ($data->previous->base !== null) {
            $list['previous']['active'] = true;
            $list['previous']['data'] = ($itemOverride) ? pagination_item_active($data->previous)
                    : $this->_item_active($data->previous);
        } else {
            $list['previous']['active'] = false;
            $list['previous']['data'] = ($itemOverride) ? pagination_item_inactive($data->previous)
                    : $this->_item_inactive($data->previous);
        }

        $list['pages'] = array(); //make sure it exists
        foreach ($data->pages as $i => $page)
        {
            if ($page->base !== null) {
                $list['pages'][$i]['active'] = true;
                $list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_active($page)
                        : $this->_item_active($page);
            } else {
                $list['pages'][$i]['active'] = false;
                $list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_inactive($page)
                        : $this->_item_inactive($page);
            }
        }

        if ($data->next->base !== null) {
            $list['next']['active'] = true;
            $list['next']['data'] = ($itemOverride) ? pagination_item_active($data->next)
                    : $this->_item_active($data->next);
        }
        else {
            $list['next']['active'] = false;
            $list['next']['data'] = ($itemOverride) ? pagination_item_inactive($data->next)
                    : $this->_item_inactive($data->next);
        }

        if ($data->end->base !== null) {
            $list['end']['active'] = true;
            $list['end']['data'] = ($itemOverride) ? pagination_item_active($data->end)
                    : $this->_item_active($data->end);
        }

        else {
            $list['end']['active'] = false;
            $list['end']['data'] = ($itemOverride) ? pagination_item_inactive($data->end)
                    : $this->_item_inactive($data->end);
        }

        if ($this->total > $this->limit) {
            return ($listOverride) ? pagination_list_render($list) : $this->_list_render($list);
        }
        else {
            return '';
        }
    }

    /**
     * Return the pagination footer.
     *
     * @return  string   Pagination footer.
     * @since    1.0
     */
    public function getListFooter()
    {
        $app = MolajoFactory::getApplication();

        $list = array();
        $list['prefix'] = $this->prefix;
        $list['limit'] = $this->limit;
        $list['limitstart'] = $this->limitstart;
        $list['total'] = $this->total;
        $list['limitfield'] = $this->getLimitBox();
        $list['pagescounter'] = $this->getPagesCounter();
        $list['pageslinks'] = $this->getPagesLinks();

        $chromePath = MOLAJO_EXTENSION_TEMPLATES.'/'.$app->getTemplate().'/'.'html'.'/'.'pagination.php';
        if (file_exists($chromePath)) {
            require_once $chromePath;
            if (function_exists('pagination_list_footer')) {
                return pagination_list_footer($list);
            }
        }
        return $this->_list_footer($list);
    }

    /**
     * Creates a dropdown box for selecting how many records to show per page.
     *
     * @return  string   The HTML for the limit # input box.
     * @since    1.0
     */
    public function getLimitBox()
    {
        $app = MolajoFactory::getApplication();

        // Initialise variables.
        $limits = array();

        // Make the option list.
        for ($i = 5; $i <= 30; $i += 5) {
            $limits[] = MolajoHTML::_('select.option', "$i");
        }
        $limits[] = MolajoHTML::_('select.option', '50', MolajoText::_('J50'));
        $limits[] = MolajoHTML::_('select.option', '100', MolajoText::_('J100'));
        $limits[] = MolajoHTML::_('select.option', '0', MolajoText::_('JALL'));

        $selected = $this->_viewall ? 0 : $this->limit;

        $html = MolajoHTML::_('select.genericlist', $limits, $this->prefix.'limit', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);

        return $html;
    }

    /**
     * Return the icon to move an item UP.
     *
     * @param   integer  $i            The row index.
     * @param   boolean  $condition    True to show the icon.
     * @param   string   $task        The task to fire.
     * @param   string   $alt        The image alternative text string.
     * @param   boolean  $enabled    An optional setting for access control on the action.
     * @param   string   $checkbox    An optional prefix for checkboxes.
     *
     * @return  string   Either the icon to move an item up or a space.
     * @since    1.0
     */
    public function orderUpIcon($i, $condition = true, $task = 'orderup', $alt = 'MOLAJO_HTML_MOVE_UP', $enabled = true, $checkbox = 'cb')
    {
        if (($i > 0 || ($i + $this->limitstart > 0)) && $condition) {
            return MolajoHTML::_('jgrid.orderUp', $i, $task, '', $alt, $enabled, $checkbox);
        }
        else {
            return '&#160;';
        }
    }

    /**
     * Return the icon to move an item DOWN.
     *
     * @param   integer  $i            The row index.
     * @param   integer  $n            The number of items in the list.
     * @param   boolean  $condition    True to show the icon.
     * @param   string   $task        The task to fire.
     * @param   string   $alt        The image alternative text string.
     * @param   boolean  $enabled    An optional setting for access control on the action.
     * @param   string   $checkbox    An optional prefix for checkboxes.
     *
     * @return  string   Either the icon to move an item down or a space.
     * @since    1.0
     */
    public function orderDownIcon($i, $n, $condition = true, $task = 'orderdown', $alt = 'MOLAJO_HTML_MOVE_DOWN', $enabled = true, $checkbox = 'cb')
    {
        if (($i < $n - 1 || $i + $this->limitstart < $this->total - 1) && $condition) {
            return MolajoHTML::_('jgrid.orderDown', $i, $task, '', $alt, $enabled, $checkbox);
        }
        else {
            return '&#160;';
        }
    }

    protected function _list_footer($list)
    {
        $html = "<div class=\"list-footer\">\n";

        $html .= "\n<div class=\"limit\">".MolajoText::_('JGLOBAL_DISPLAY_NUM').$list['limitfield']."</div>";
        $html .= $list['pageslinks'];
        $html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";

        $html .= "\n<input type=\"hidden\" name=\"".$list['prefix']."limitstart\" value=\"".$list['limitstart']."\" />";
        $html .= "\n</div>";

        return $html;
    }

    protected function _list_render($list)
    {
        // Reverse output rendering for right-to-left display.
        $html = '<ul>';
        $html .= '<li class="pagination-start">'.$list['start']['data'].'</li>';
        $html .= '<li class="pagination-prev">'.$list['previous']['data'].'</li>';
        foreach ($list['pages'] as $page) {
            $html .= '<li>'.$page['data'].'</li>';
        }
        $html .= '<li class="pagination-next">'.$list['next']['data'].'</li>';
        $html .= '<li class="pagination-end">'.$list['end']['data'].'</li>';
        $html .= '</ul>';

        return $html;
    }

    protected function _item_active(&$item)
    {
        $app = MolajoFactory::getApplication();
        if ($item->base > 0) {
            return "<a title=\"".$item->text."\" onclick=\"document.adminForm.".$this->prefix."limitstart.value=".$item->base."; Joomla.submitform();return false;\">".$item->text."</a>";
        }
        else {
            return "<a title=\"".$item->text."\" onclick=\"document.adminForm.".$this->prefix."limitstart.value=0; Joomla.submitform();return false;\">".$item->text."</a>";
        }
        //admin			return "<a title=\"".$item->text."\" href=\"".$item->link."\" class=\"pagenav\">".$item->text."</a>";

    }

    protected function _item_inactive(&$item)
    {
        //admin			return "<span>".$item->text."</span>";
        return "<span class=\"pagenav\">".$item->text."</span>";
    }

    /**
     * Create and return the pagination data object.
     *
     * @return  object  Pagination data object.
     * @since   1.0
     */
    protected function _buildDataObject()
    {
        // Initialise variables.
        $data = new stdClass();

        // Build the additional URL parameters string.
        $parameters = '';
        if (!empty($this->_additionalUrlParameters)) {
            foreach ($this->_additionalUrlParameters as $key => $value)
            {
                $parameters .= '&'.$key.'='.$value;
            }
        }

        $data->all = new MolajoPaginationObject(MolajoText::_('MOLAJO_HTML_VIEW_ALL'), $this->prefix);
        if (!$this->_viewall) {
            $data->all->base = '0';
            $data->all->link = MolajoRoute::_($parameters.'&'.$this->prefix.'limitstart=');
        }

        // Set the start and previous data objects.
        $data->start = new MolajoPaginationObject(MolajoText::_('MOLAJO_HTML_START'), $this->prefix);
        $data->previous = new MolajoPaginationObject(MolajoText::_('JPREV'), $this->prefix);

        if ($this->get('pages.current') > 1) {
            $page = ($this->get('pages.current') - 2) * $this->limit;

            // Set the empty for removal from route
            //$page = $page == 0 ? '' : $page;

            $data->start->base = '0';
            $data->start->link = MolajoRoute::_($parameters.'&'.$this->prefix.'limitstart=0');
            $data->previous->base = $page;
            $data->previous->link = MolajoRoute::_($parameters.'&'.$this->prefix.'limitstart='.$page);
        }

        // Set the next and end data objects.
        $data->next = new MolajoPaginationObject(MolajoText::_('JNEXT'), $this->prefix);
        $data->end = new MolajoPaginationObject(MolajoText::_('MOLAJO_HTML_END'), $this->prefix);

        if ($this->get('pages.current') < $this->get('pages.total')) {
            $next = $this->get('pages.current') * $this->limit;
            $end = ($this->get('pages.total') - 1) * $this->limit;

            $data->next->base = $next;
            $data->next->link = MolajoRoute::_($parameters.'&'.$this->prefix.'limitstart='.$next);
            $data->end->base = $end;
            $data->end->link = MolajoRoute::_($parameters.'&'.$this->prefix.'limitstart='.$end);
        }

        $data->pages = array();
        $stop = $this->get('pages.stop');
        for ($i = $this->get('pages.start'); $i <= $stop; $i++)
        {
            $offset = ($i - 1) * $this->limit;
            // Set the empty for removal from route
            //$offset = $offset == 0 ? '' : $offset;

            $data->pages[$i] = new MolajoPaginationObject($i, $this->prefix);
            if ($i != $this->get('pages.current') || $this->_viewall) {
                $data->pages[$i]->base = $offset;
                $data->pages[$i]->link = MolajoRoute::_($parameters.'&'.$this->prefix.'limitstart='.$offset);
            }
        }
        return $data;
    }
}

/**
 * Pagination object representing a particular item in the pagination lists.
 *
 * @package    Molajo
 * @subpackage  HTML
 * @since       1.0
 */
class MolajoPaginationObject extends JObject
{
    public $text;
    public $base;
    public $link;
    public $prefix;

    public function __construct($text, $prefix = '', $base = null, $link = null)
    {
        $this->text = $text;
        $this->prefix = $prefix;
        $this->base = $base;
        $this->link = $link;
    }
}
