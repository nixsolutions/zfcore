<?php
/**
 * Images controller for pages module
 * 
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 * 
 * @version  $Id: ImagesController.php 182 2010-08-09 08:24:15Z andreyalek $
 */
class Pages_ImagesController extends Core_Controller_Action
{
    /**
     * instance of model Model_Image
     *
     * @var Model_Image
     */
    private $_image;
    
    /**
     * Seccess message
     *
     * @var string
     */
    protected $_success = 'success';
    
    /**
     * Init environmonet
     */
    public function init() 
    {
        /* Initialize */
        parent::init();
        
        /* is Dashboard Controller */
        $this->_isDashboard();
        
        Zend_Layout::getMvcInstance()->disableLayout();        
        $this->_image = new Pages_Model_Image();
    }

    /**
     * Show image manager
     */
    public function indexAction() 
    {
        $this->view->sSuccess = $this->_success;
        
        if ($this->_image->getPath($this->_getParam('dir'))) {
            $this->view->sDir = $this->_getParam('dir');
        } else {
            $sessionDir = new Zend_Session_Namespace('uploadDir');
            if (isset($sessionDir->dir) && $sessionDir->dir) {
                $this->view->sDir = $sessionDir->dir;
                unset($sessionDir->dir);
            } else {
                $this->view->sDir = $this->_image->getDefaultDir();
            }
        }

        // classes, thumb sizes etc
        $this->view->aOptions = $this->_image->getOptions();
    }
    
    /**
     * View upload directory
     */
    public function managerAction()
    {
        Zend_Layout::getMvcInstance()->enableLayout();
        $this->_helper->layout->setLayout('dashboard/layout');
        
        $this->indexAction();
    }
    
    /**
     * Browse files and directories
     */
    public function browserAction() 
    {
        $this->view->sDir = $this->_getParam('dir');

        // get files and dirs from path
        try {
            $this->view->aList = $this->_image
                                      ->getDirectory($this->_getParam('dir'));
        } catch (Exception $e) {
            return $this->_forward(
                'internal',
                'error',
                'default',
                array('error' => $e->getMessage())
            );
        }
    }


    /**
     * Delete image action
     */
    public function deleteAction() 
    {
        try {
            $this->_image->delete(
                $this->_getParam('path'),
                $this->_getParam('name')
            );
        } catch (Exception $e) {
            return $this->_forward(
                'internal',
                'error',
                'default',
                array('error' => $e->getMessage())
            );
        }
        
        return $this->_forward(
            'browser',
            'images',
            'pages',
            array('dir' => $this->_getParam('path'))
        );
    }
    
    /**
     * Upload new image
     */
    public function uploadAction() 
    {
        $sDir = $this->_getParam('dirPath');

        $sessionDir = new Zend_Session_Namespace('uploadDir');
        $sessionDir->dir = $sDir;

        try {
            if (!$this->_request->isPost()) {
                throw new Exception('Invalid access method');
            }
            $this->_image->upload($sDir);
        } catch (Exception $e) {
            return $this->_forward(
                'internal',
                'error', 
                'default',
                array('error' => $e->getMessage())
            );
        }

        $this->getFrontController()->setBaseUrl('');
        $this->_redirect($this->_getParam('returnto'));
    }
    
    /**
     * Create thumb for image
     */
    public function thumbAction()
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('thumb', 'json')
                      ->initContext('json');
                      
        try {              
            $result = $this->_image->createThumb(
                $this->_getParam('file'),
                $this->_getParam('size')
            );
            if ($result) {
                $this->view->result = $this->_success;
            } else {
                $this->view->result = false;
            }
        } catch (Exception $e) {
            $this->view->result = false;
        }
                                                   
        $this->_helper->viewRenderer->setNoRender();
    }
}