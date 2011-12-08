WORKING WITH MOLAJO LAYOUTS

This readme contains the following information:

    I. INTRODUCTION
    II. WHAT IS A MOLAJO LAYOUT?
    III. WHERE ARE LAYOUTS LOCATED?
    IV. HOW TO DETERMINE WHAT LAYOUT AN EXTENSION IS USING?
    V. HOW DO I CREATE, INSTALL, AND SHARE LAYOUTS?
    VI. HOW DO I OVERRIDE A LAYOUT?
    VII. WORKING WITH CSS AND JS FOR THE APPLICATION, COMPONENT, ASSET, AND/OR LAYOUT
    VIII. USING QUERY RESULTS IN LAYOUTS
    IX. WHAT DATA IS AVAILABLE WITHIN A LAYOUT FILE?
    X. WORKING WITH DATES
    XI. INTEGRATING OTHER EXTENSION OUTPUT WITHIN A LAYOUT
    XII. WORKING WITH IMAGES
    XIII. USING OTHER TEMPLATE SYSTEMS

Please check the README file on the root for the location of other README files.




SECTION I. INTRODUCTION

1. What is a Layout?

A Layout is a set of files used to format and display data.

Layouts can be shared between extensions.

There are several different types of layouts, called 'layout types'.

2. What is a Layout Type?

Layout Types are specific types of rendered output, including:

a) extensions - layouts shared by components, modules, and plugins
b) formfields - layouts used to generate form fields
c) head - layouts used to render document head information
d) wraps - layouts used to wrap extension output (these are called "styles" or "chrome" in Joomla)

3. What is a Model?

A Model retrieves data needed to produce a Layout.

4. What is a View?

A View retrieves a rowset from the Model and pushes it into the Layout.

5. What is a Rowset?

A rowset is the collection of data retrieved by the View from the Model.

The View processes each rowset, one at a time, in a loop, passing the data into the View for display.

6. What is a row?

A row is set of columns with specific data about a single item contained within the $this->row array. To view all columns at one time, use this command:

7. What is a column?

A column is a single piece of information about an item. Display the data for a column, as follows:

<?php echo $this->row->title; ?>



SECTION II. WHAT IS A MOLAJO LAYOUT?

1. What is a Molajo Layout?

A Molajo Layout is a collection of files used to display output from Molajo Extensions.

Layouts can be reused by any Component, Module, or Plugin. Even Form Field Types use Layouts.

There are four layout types: extensions, formfields, head, and wraps.

2. What is the structure of a Layout?

A Layout is organized in the following way:

layout-type
...layout-name
... ... css
... ... ... All files with a CSS extension are automatically loaded
... ... ... Name files rtl_ to indicate those files which should be loaded only for sites configured for a Right-to-left Language.
... ... images
... ... ... Any images needed for the layout can be stored in the images subfolder.
... ... js
... ... ... All files with a JS extension are automatically loaded
... ... language
... ... ... en-GB
... ... ... ... en-GB.layouts_layouttype_layoutname.ini
... ... ... ... en-GB.layouts_layouttype_layoutname.sys.ini
... ... layouts
... ... ... top.php
... ... ... header.php
... ... ... body.php
... ... ... footer.php
... ... ... bottom.php
... ... layout-name.xml




SECTION III. WHERE ARE LAYOUTS LOCATED?

The Core Molajo Layouts are located within the layouts/layout-type/layout-name folder

Valid Layout types include extensions, formfields, head, and wraps.

For example, the list layout will be found in layouts/extensions/list.





SECTION IV. HOW TO DETERMINE WHAT LAYOUT AN EXTENSION IS USING?






SECTION V. HOW DO I CREATE, INSTALL, AND SHARE LAYOUTS?

1. How can I install a Molajo Layout?

To install a Molajo Layout someone shared with you, use the Molajo Administrator-Extension Manager-Install option.

2. How can I share my Layout with other Molajo users?

Zip up your GPL, free of charge Layout and post it XYZ


To create a Layout, navigate to the Molajo Administrator Extension Manager.

Use the 'Create' Submenu Item.





SECTION VI. HOW DO I OVERRIDE A LAYOUT?

1. What sequence does Molajo use to search for Layouts?

Molajo searches for Layouts in this order, using the first found:

a) template/html/component-name/view-name/layout-name
b) template/layouts/layout-type/layout-name
c) component/view/tmpl/layout-name
d) layouts/layout-type/layout-name

2. How do I override a core Layout?

To override a core Layout, place the layout in a, b, or c, above.




SECTION VII. WORKING WITH CSS AND JS FOR THE APPLICATION, COMPONENT, ASSET, AND/OR LAYOUT

1. How do I add CSS for a Layout?

Place the CSS files into the layout/css folder. All files with a CSS extension are automatically loaded

2. How do I provide for right-to-left Languages?

Name files rtl_ to indicate those files which should be loaded only for sites configured for a Right-to-left Language.

3. How do I add JS to a Layout?

All files with a JS extension are automatically loaded

4. How can I include images?

Add images to the layout/images folder and use <img src="../images/name.jpg" /> within the layout files.

5. What about Language files?

Place language files within the project in the languages/xx-XX/ folder.

Name files:
xx-XX.layout_[layout-type]_[layout-name].ini
xx-XX.layout_[layout-type]_[layout-name].sys.ini

5. How does Molajo load CSS and JSS, system-wide?

System-wide CSS and JS in
    => media/system/css[js]/XYZ.css[js]

Application-specific CSS and JS in
    => media/system/[application]/css[js]/XYZ.css[js]

Extension specific CSS and JS in
    => media/system/[application]/[extension]/css[js]/XYZ.css[js]

Layout specific CSS and JS in
    => layouts/[layout-type]/[layout]/css[js]/XYZ.css[js]

Asset ID CSS and JS in
    => media/system/[application]/[asset_id]/css[js]/XYZ.css[js]



SECTION VIII. USING QUERY RESULTS IN LAYOUTS

1. How are the layout files processed by Molajo?

There are two options for using query results:

A. To process the recordset within your layout file, include the custom.php layout file.

When Molajo finds a custom.php file, it pushes the $this->rowset object into the file.

The layout must handle it's own loop processing for the recordset.

B. To allow Molajo to handle recordset process, don't include the custom.php file.

Instead, use the top.php, header.php, body.php, footer.php and bottom.php files, as described below.

2. How does the Molajo View process the recordset and render the layout files?

The Molajo View loops through each row, one at a time.

The following layout-type/layout-name/layouts/ files are included, if existing:

A. Before any rows and if there is a top.php file:
    * beforeDisplayContent output is rendered;
    * the top.php file is included.

B. Each row is processed one at a time, as follows:
    * if there is a header.php file, it is included and event afterDisplayTitle output is rendered.
    * if there is a body.php file, it is included.
    * if there is a footer.php file, it is included.

C. After all rows have been processed, and if there is a footer.php file:
    * the footer.php file is included;
    * afterDisplayContent output is rendered;




SECTION IX. WHAT DATA IS AVAILABLE WITHIN A LAYOUT FILE?

1. What data is available within the Layout file?

A. $this->app

<?php echo '<pre>'; var_dump($this->app); '</pre>';  ?>

B. $this->document

<?php echo '<pre>'; var_dump($this->document); '</pre>';  ?>

C. $this->user

<?php echo '<pre>'; var_dump($this->user); '</pre>';  ?>

D. $this->request

<?php echo '<pre>'; var_dump($this->request); '</pre>';  ?>

E. $this->state

<?php echo '<pre>'; var_dump($this->state); '</pre>';  ?>

F. $this->params

<?php echo '<pre>'; var_dump($this->params); '</pre>';  ?>

G. $this->rowset

<?php echo '<pre>'; var_dump($this->rowset); '</pre>';  ?>

H. $this->row

<?php echo '<pre>'; var_dump($this->row); '</pre>';  ?>

I. $this->pagination

<?php echo '<pre>'; var_dump($this->pagination); '</pre>';  ?>

J. $this->layout_path

<?php echo $this->layout_path; ?>

K. $this->layout

<?php echo $this->layout;  ?>

L. $this->$wrap;

<?php echo $this-wrap;  ?>




SECTION X. WORKING WITH DATES

1. What options are available for formatting dates in layouts?

Dates can be formatted one of two ways, using PHP date or the JHtml class.

2. How can PHP's Date function be used in a layout?

http://php.net/manual/en/function.date.php

Example:
$today = date("F j, Y, g:i a"); // March 10, 2001, 5:16 pm

3. How can Joomla's JHtml function be used in a layout to format dates?

$date	= JHtml::_('date', $this->row->created, JText::_('DATE_FORMAT_LC1'));
$time	= JHtml::_('date', $this->row->checked_out_time, 'H:i');

Options:
DATE_FORMAT_LC="l, d F Y"
DATE_FORMAT_LC1="l, d F Y"
DATE_FORMAT_LC2="l, d F Y H:i"
DATE_FORMAT_LC3="d F Y"
DATE_FORMAT_LC4="Y-m-d"
DATE_FORMAT_JS1="y-m-d"

4. How can I change the date formats for Molajo?

Date formats can be changed by overriding the language file.





SECTION XI. INTEGRATING OTHER EXTENSION OUTPUT WITHIN A LAYOUT

1. How can I render the output for another Component View within the Layout file?

$this->renderComponent ('component-name', 'view-name', 'layout-name', array('parameter1' => 'value', 'parameter2' => 'value'));

2. How can I render Module output within the Layout File?

$this->renderModule ('module-name', array('parameter1' => 'value', 'parameter2' => 'value');

3. How can I render a Module Position within the Layout File?

$this->renderModulePosition ('position-name', array('wrap' => 'name-of-wrap');





SECTION XII. WORKING WITH IMAGES

1. System Configuration

Locations:


Sizes:

 * 0 - original size
 * 1 - xsmall; configuration option, defaults to 50 x 50
 * 2 - small; configuration option, defaults to 75 x 75
 * 3 - medium; configuration option, defaults to 150 x 150
 * 4 - large; configuration option, defaults to 300 x 300
 * 5 - xlarge; configuration option, defaults to 500 x 500

Types:

2. How can I resize an image within a layout?

Add this to your layout or within your content.

{image name="dog.png" size=1 type=2}




SECTION XIII. USING OTHER TEMPLATE SYSTEMS

1. What does Molajo use as a default layout environment?

The MolajoView class handles layout processing. Normal PHP are used in core Layout files.

2. How can I use Twig to render Molajo output?

Babs?

3. How can I add <insert name of your favorite Template System here> to render Molajo Output?

Simply load the files needed within your component entry point file and then use them in your Layout.

