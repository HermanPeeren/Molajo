<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class MolajoDisplayController extends MolajoController
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  array   $request
     * @since  1.0
     */
    public function __construct($task, $parameters)
    {
        parent::__construct($task, $parameters);
    }

    /**
     * display
     *
     * Display task is used to render view output
     *
     * @return  object  Rendered output
     * @since   1.0
     */
    public function display()
    {
        /** instantiate model */
        $mc = (string)$this->task->get('model');
        $this->model = new $mc();
        $this->model->task = $this->task;
        $this->model->parameters = $this->parameters;

        /** check out */
        if ($this->task->get('task') == 'edit') {
            $results = parent::checkoutItem();
            if ($results === false) {
                return $this->redirectClass->setSuccessIndicator(false);
            }
        }

        /** query results */
        $this->rowset = $this->model->getItems();

        /** pagination */
        $this->pagination = $this->model->getPagination();

        /** no results */
        if (count($this->rowset) == 0
            && $this->parameters->get('extension_suppress_no_results') == 1
        ) {
            return '';
        }

        /** render template view */
        $this->view_path = $this->task->get('template_view_path');
        $this->view_path_url = $this->task->get('template_view_path_url');
        $renderedOutput = $this->renderView($this->task->get('template_view_name'));

        /** render wrap view around template view results */
        return $this->wrapView($this->task->get('wrap_view_name'), $renderedOutput);
    }

    /**
     * wrapView
     *
     * @param $view
     * @param $renderedOutput
     *
     * @return string
     * @since 1.0
     */
    public function wrapView($view, $renderedOutput)
    {
        /** create rowset */
        $this->rowset = array();

        $tempObject = new JObject();
        $tempObject->set('wrap_view_css_id', $this->task->get('wrap_view_css_id'));
        $tempObject->set('wrap_view_css_class', $this->task->get('wrap_view_css_class'));
        $tempObject->set('content', $renderedOutput);

        $this->rowset[] = $tempObject;

        /** paths */
        $this->view_path = $this->task->get('wrap_view_path');
        $this->view_path_url = $this->task->get('wrap_view_path_url');

        /** render wrap */
        return $this->renderView($this->task->get('wrap_view_name'), 'wraps');
    }

    /**
     * renderView
     *
     * Depending on the files within view/view-type/view-name/views/*.*:
     *
     * 1. Provide all query results in $this->rowset for the view to process
     *      How? Include view named custom.php
     *
     * 2. Loop thru the $this->rowset object processing each row, one at a time.
     *      How? Include top.php, header.php, body.php, footer.php, and/or bottom.php views
     *
     * @return string
     * @since 1.0
     */
    protected function renderView($view)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** start collecting output */
        ob_start();

        /** 1. view handles loop and event processing */
        if (file_exists($this->view_path . '/views/custom.php')) {
            include $this->view_path . '/views/custom.php';

        } else {

            /** 2. controller manages loop and event processing */
            $totalRows = count($this->rowset);
            foreach ($this->rowset as $this->row) {

                /** view: before any rows are processed */
                if ($rowCount == 1) {

                    if (isset($this->row->event->beforeRenderView)) {
                        echo $this->row->event->beforeRenderView;
                    }

                    if (file_exists($this->view_path . '/views/top.php')) {
                        include $this->view_path . '/views/top.php';
                    }
                }

                /** view: row header, body, and footer */
                if ($this->row == null) {
                } else {

                    if (isset($this->row->event->beforeRenderViewItem)) {
                        echo $this->row->event->beforeRenderViewItem;
                    }

                    if (file_exists($this->view_path . '/views/header.php')) {
                        include $this->view_path . '/views/header.php';
                    }

                    if (file_exists($this->view_path . '/views/body.php')) {
                        include $this->view_path . '/views/body.php';
                    }

                    if (file_exists($this->view_path . '/views/footer.php')) {
                        include $this->view_path . '/views/footer.php';
                    }

                    if (isset($this->row->event->afterRenderViewItem)) {
                        echo $this->row->event->afterRenderViewItem;
                    }

                    $rowCount++;
                }
            }

            /** view: after all rows are processed */
            if ($rowCount > $totalRows) {
                if (file_exists($this->view_path . '/views/bottom.php')) {
                    include $this->view_path . '/views/bottom.php';

                    if (isset($this->row->event->afterRenderView)) {
                        echo $this->row->event->afterRenderView;
                    }
                }
            }
        }

        /** collect and return rendered output */
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * safeHTML
     *
     * @param string $text
     *
     * @return  string
     * @since   1.0
     */
    static public function safeHTML($htmlText)
    {

    }

    /**
     * safeInteger
     *
     * @param string $integer
     *
     * @return  string
     * @since   1.0
     */
    static public function safeInteger($integer)
    {
        return (int) $integer;
    }

    /**
     * safeText
     *
     * @param string $text
     *
     * @return  string
     * @since   1.0
     */
    static public function safeText($text)
    {
        return htmlspecialchars($text, ENT_COMPAT, 'utf-8');
    }

    /**
     * safeURL
     *
     * @param   string  $url
     *
     * @return  string
     * @since  1.0
     */
    static public function safeURL($url)
    {
        if (Molajo::Application()->get('unicode_slugs') == 1) {
            return FilterOutput::stringURLUnicodeSlug($url);
        } else {
            return FilterOutput::stringURLSafe($url);
        }
    }
}
class Display extends MolajoDisplayController {}

/** 7. Optional data (put this into a model parent?) */
//		$this->category	            = $this->get('Category');
//		$this->categoryAncestors    = $this->get('Ancestors');
//		$this->categoryParent       = $this->get('Parent');
//		$this->categoryPeers	    = $this->get('Peers');
//		$this->categoryChildren	    = $this->get('Children');

/** used in manager */

/**
 * @var $render object
 */
//protected $render;

/**
 * @var $saveOrder string
 */
// protected $saveOrder;
//      $this->authorProfile        = $this->get('Author');

//      $this->tags (tag cloud)
//      $this->tagCategories (menu)
//      $this->calendar

/** blog variables
move variables into $options
retrieve variables here in controller - and split int rowset if needed

protected $category;
protected $children;
protected $lead_items = array();
protected $intro_items = array();
protected $link_items = array();
protected $columns = 1;
 */
//Navigation
//$this->navigation->get('form_return_to_link')
//$this->navigation->get('previous')
//$this->navigation->get('next')
//
// Pagination
//$this->navigation->get('pagination_start')
//$this->navigation->get('pagination_limit')
//$this->navigation->get('pagination_links')
//$this->navigation->get('pagination_ordering')
//$this->navigation->get('pagination_direction')
//$this->breadcrumbs
//$total = $this->getTotal();

//$this->configuration;
//Parameters (Includes Global Options, Menu Item, Item);
//$this->parameters->get('view_show_page_view_heading', 1);
//$this->parameters->get('view_page_view_class_suffix', '');
