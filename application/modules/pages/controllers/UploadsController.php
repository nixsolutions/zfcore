<?php
/**
 * Uploads controller for pages module
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 *
 * @version  $Id: ImagesController.php 182 2010-08-09 08:24:15Z andreyalek $
 */
class Pages_UploadsController extends Core_Controller_Action
{
    /**
    * Init
    */
    public function init()
    {
        $this->_isDashboard();
    }

    /**
     * Index
     */
    public function indexAction()
    {
    }

    /**
     * Elfinder ajax connector action
     */
    public function connectorAction()
    {
        $this->_helper->layout->disableLayout();

        error_reporting(0); // Set E_ALL for debuging

        include_once 'elFinder/elFinderConnector.class.php';
        include_once 'elFinder/elFinder.class.php';
        include_once 'elFinder/elFinderVolumeDriver.class.php';
        include_once 'elFinder/elFinderVolumeLocalFileSystem.class.php';

        // Required for MySQL storage connector
        // include_once 'elFinder/elFinderVolumeMySQL.class.php';



        $opts = array(
        	// 'debug' => true,
        	'roots' => array(
        		array(
        			'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
        			'path'          => 'uploads',         // path to files (REQUIRED)
        			'URL'           => $this->view->baseUrl('uploads'), // URL to files (REQUIRED)
        			'accessControl' => array($this, 'access')    // disable and hide dot starting files (OPTIONAL)
        		)
        	)
        );

        // run elFinder
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }

    /**
     * Simple function to demonstrate how to control file access using "accessControl" callback.
     * This method will disable accessing files/folders starting from  '.' (dot)
     *
     * @param  string  $attr  attribute name (read|write|locked|hidden)
     * @param  string  $path  file path relative to volume root directory started with directory separator
     * @return bool
     **/
    public function access($attr, $path, $data, $volume)
    {
    	return strpos(basename($path), '.') === 0   // if file/folder begins with '.' (dot)
    		? !($attr == 'read' || $attr == 'write')  // set read+write to false, other (locked+hidden) set to true
    		: ($attr == 'read' || $attr == 'write');  // else set read+write to true, locked+hidden to false
    }
}