<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Form Field to display a list of the layouts for a component view from the extension or template overrides.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldComponentlayout extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    protected $type = 'ComponentLayout';

    /**
     * Method to get the field calendar.
     *
     * @return  string   The field calendar.
     * @since   1.0
     */
    protected function getInput()
    {
        // Initialize variables.

        // Get the application id.
        $application_id = $this->element['application_id'];

        if (is_null($application_id) && $this->form instanceof MolajoForm) {
            $application_id = $this->form->getValue('application_id');
        }
        $application_id = (int)$application_id;

        $application = MolajoApplicationHelper::getApplicationInfo($application_id);

        // Get the extension.
        $extn = (string)$this->element['extension'];

        if (empty($extn) && ($this->form instanceof MolajoForm)) {
            $extn = $this->form->getValue('extension');
        }

        $extn = preg_replace('#\W#', '', $extn);

        // Get the template.
        $template = (string)$this->element['template'];
        $template = preg_replace('#\W#', '', $template);

        // Get the style.
        if ($this->form instanceof MolajoForm) {
            $template_id = $this->form->getValue('template_id');
        }

        $template_id = preg_replace('#\W#', '', $template_id);

        // Get the view.
        $view = (string)$this->element['view'];
        $view = preg_replace('#\W#', '', $view);

        // If a template, extension and view are present build the options.
        if ($extn && $view && $application) {

            // Load language file
            $lang = MolajoFactory::getLanguage();
            $lang->load($extn . '.sys', MOLAJO_BASE_FOLDER, null, false, false)
            || $lang->load($extn . '.sys', MOLAJO_BASE_FOLDER . '/components/' . $extn, null, false, false)
            || $lang->load($extn . '.sys', MOLAJO_BASE_FOLDER, $lang->getDefault(), false, false)
            || $lang->load($extn . '.sys', MOLAJO_BASE_FOLDER . '/components/' . $extn, $lang->getDefault(), false, false);

            // Get the database object and a new query object.
            $db = MolajoFactory::getDBO();
            $query = $db->getQuery(true);

            // Build the query.
            $query->select('e.element, e.name');
            $query->from('#__extensions as e');
            $query->where('e.application_id = ' . (int)$application_id);
            $query->where('e.type = ' . $db->quote('template'));
            $query->where('e.enabled = 1');

            if ($template) {
                $query->where('e.element = ' . $db->quote($template));
            }

            if ($template_id) {
                $query->join('LEFT', '#__template_styles as s on s.template=e.element');
                $query->where('s.id=' . (int)$template_id);
            }

            // Set the query and load the templates.
            $db->setQuery($query);
            $templates = $db->loadObjectList('element');

            // Check for a database error.
            if ($db->getErrorNum()) {
                MolajoError::raiseWarning(500, $db->getErrorMsg());
            }

            // Build the search paths for component layouts.
            $component_path = JPath::clean($application->path . '/components/' . $extn . '/views/' . $view . '/layouts');

            // Prepare array of component layouts
            $component_layouts = array();

            // Prepare the grouped list
            $groups = array();

            // Add a Use Global option if useglobal="true" in XML file
            if ($this->element['useglobal'] == 'true') {
                $groups[MolajoTextHelper::_('JOPTION_FROM_STANDARD')]['items'][] = MolajoHTML::_('select.option', '', MolajoTextHelper::_('JGLOBAL_USE_GLOBAL'));
            }

            // Add the layout options from the component path.
            if (is_dir($component_path) && ($component_layouts = JFolder::files($component_path, '^[^_]*\.xml$', false, true))) {
                // Create the group for the component
                $groups['_'] = array();
                $groups['_']['id'] = $this->id . '__';
                $groups['_']['text'] = MolajoTextHelper::sprintf('JOPTION_FROM_COMPONENT');
                $groups['_']['items'] = array();

                foreach ($component_layouts as $i => $file)
                {
                    // Attempt to load the XML file.
                    if (!$xml = simplexml_load_file($file)) {
                        unset($component_layouts[$i]);

                        continue;
                    }

                    // Get the help data from the XML file if present.
                    if (!$menu = $xml->xpath('layout[1]')) {
                        unset($component_layouts[$i]);

                        continue;
                    }

                    $menu = $menu[0];

                    // Add an option to the component group
                    $value = JFile::stripext(JFile::getName($file));
                    $component_layouts[$i] = $value;
                    $text = isset($menu['option']) ? MolajoTextHelper::_($menu['option']) : (isset($menu['title'])
                            ? MolajoTextHelper::_($menu['title']) : $value);
                    $groups['_']['items'][] = MolajoHTML::_('select.option', '_:' . $value, $text);
                }
            }

            // Loop on all templates
            if ($templates) {
                foreach ($templates as $template)
                {
                    // Load language file
                    $lang->load('template_' . $template->element . '.sys', $application->path, null, false, false)
                    || $lang->load('template_' . $template->element . '.sys', $application->path . '/templates/' . $template->element, null, false, false)
                    || $lang->load('template_' . $template->element . '.sys', $application->path, $lang->getDefault(), false, false)
                    || $lang->load('template_' . $template->element . '.sys', $application->path . '/templates/' . $template->element, $lang->getDefault(), false, false);

                    $template_path = JPath::clean($application->path . '/templates/' . $template->element . '/html/' . $extn . '/' . $view);

                    // Add the layout options from the template path.
                    if (is_dir($template_path) && ($files = JFolder::files($template_path, '^[^_]*\.php$', false, true))) {
                        // Files with corresponding xml files are alternate menu items, not alternate layout files
                        // so we need to exclude these files from the list.
                        $xml_files = JFolder::files($template_path, '^[^_]*\.xml$', false, true);
                        for ($j = 0, $count = count($xml_files); $j < $count; $j++)
                        {
                            $xml_files[$j] = JFile::stripext(JFile::getName($xml_files[$j]));
                        }
                        foreach ($files as $i => $file)
                        {
                            // Remove layout files that exist in the component folder or that have XML files
                            if ((in_array(JFile::stripext(JFile::getName($file)), $component_layouts))
                                || (in_array(JFile::stripext(JFile::getName($file)), $xml_files))
                            ) {
                                unset($files[$i]);
                            }
                        }
                        if (count($files)) {
                            // Create the group for the template
                            $groups[$template->name] = array();
                            $groups[$template->name]['id'] = $this->id . '_' . $template->element;
                            $groups[$template->name]['text'] = MolajoTextHelper::sprintf('JOPTION_FROM_TEMPLATE', $template->name);
                            $groups[$template->name]['items'] = array();

                            foreach ($files as $file)
                            {
                                // Add an option to the template group
                                $value = JFile::stripext(JFile::getName($file));
                                $text = $lang->hasKey($key = strtoupper('TPL_' . $template->name . '_' . $extn . '_' . $view . '_LAYOUT_' . $value))
                                        ? MolajoTextHelper::_($key) : $value;
                                $groups[$template->name]['items'][] = MolajoHTML::_('select.option', $template->element . ':' . $value, $text);
                            }
                        }
                    }
                }
            }

            // Compute attributes for the grouped list
            $attr = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';

            // Prepare HTML code
            $html = array();

            // Compute the current selected values
            $selected = array($this->value);

            // Add a grouped list
            $html[] = MolajoHTML::_('select.groupedlist', $groups, $this->name, array('id' => $this->id, 'group.id' => 'id', 'list.attr' => $attr, 'list.select' => $selected));


            return implode($html);
        }
        else
        {
            return '';
        }
    }
}

