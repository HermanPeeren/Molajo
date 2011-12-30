<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2012 Babs Gösgens. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display View
 *
 * @package        Molajo
 * @subpackage    View
 * @since        1.0
 */
class InstallerViewDisplay extends MolajoView
{

    /**
     * Tdddddd
     *
     * @var    ddd
     * @since  1.0
     */
    protected $system_checks = null;
    protected $form_fields = null;
    protected $form_edits = null;

    /**
     * display
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $helper = $this->loadHelper('installer');

        /** check view */
        $view = JRequest::getCmd('next_step', 'step1');

        if ($view == 'step1') {
        }
        else if ($view == 'step2') {

        }
        else if ($view == 'step3') {

        }
        else if ($view == 'step4') {
        }

        // We want to enable single page (or however many steps) so we need to assign these to any view
        $this->assign('setup', $this->getModel()->getSetup());
        $this->assign('languages', $this->getModel()->getLanguageList());
        $this->assign('db_types', $this->getModel()->getDBTypes());
        $this->assign('mock_data', $this->getModel()->getMockDataTypes());

        /** load unused fields into hidden form fields for display */

        parent::display($view);
    }

}
