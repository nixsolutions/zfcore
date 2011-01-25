<?php
/**
 * MailController for admin module
 *
 * @category   Application
 * @package    Dashboard
 * @subpackage Controller
 * 
 * @version  $Id: MailController.php 206 2010-10-20 10:55:55Z AntonShevchuk $
 */
class Admin_MailController extends Core_Controller_Action_Scaffold
{
    /**
     * init environment
     */
    public function init()
    {
        /* Initialize */
        parent::init();
        
        /* is Dashboard Controller */
        $this->_isDashboard();
    }

    /**
     * Index action
     * Show All
     *
     * @todo maybe create Datagrid config and put there all options
     * @see view
     */
    public function indexAction()
    {
        $this->view->aOptions = array('maxLength' => 140);
    }
    
    /**
     * Edit action
     */
    public function editAction()
    {
        parent::editAction();
    }
    
    /**
     * Edit Layout
     *
     */
    public function layoutAction()
    {
        $mail = new Model_Mail_Form_Layout();
        
        if ($this->_request->isPost() && 
            $mail->isValid($this->_getAllParams())) {
            Model_Mail::setLayout($mail->getValue('body')); 
            $this->_helper->getHelper('redirector')->direct('index');
        } else {
            if (!in_array(true, $mail->getValues())) { 
                $mail->setDefaults(array('body' => Model_Mail::getLayout()));
            }
            $this->view->editLayout = $mail;
        }
    }
    
    /**
     * delete Action
     */
    public function deleteAction()
    {
        return $this->_forward('notfound', 'error');
    }
    
    /**
     * create action
     */
    public function createAction()
    {
        return $this->_forward('notfound', 'error');
    }
    
    /**
     * send action
     */
    public function sendAction()
    {
        $mail = new Model_Mail_Form_Send();
        
        if ($this->_request->isPost() && 
            $mail->isValid($this->_getAllParams())) {
            
            try {
                Model_Mail::send($mail->getValues());
            } catch (Exception $e) {
                return $this->_forward(
                    'internal',
                    'error',
                    'admin',
                    array('error' => $e->getMessage())
                );
            }
            $this->_flashMessenger->addMessage('Mail Send');
            $this->_helper->getHelper('redirector')->direct('index');
        } else {
            if (!in_array(true, $mail->getValues())
                && ($alias = $this->_getParam('alias'))
            ) { 
                if (!$defaults = $this->_getTable()->getByAlias($alias)) {
                    $message = $this->__('Not found such mail alias ') . "'$alias'";
                    return $this->_forward(
                        'internal',
                        'error',
                        'admin',
                        array('error' => $message)
                    );
                }
                $mail->setDefaults($defaults->toArray());
            }
            $this->view->mailForm = $mail;
        }
    }

    /**
     * _getTable
     *
     * return dbTable for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Model_Mail_Table();
    }
    
    /**
     * disable create from
     */
    protected function _getCreateForm()
    {
        return;
    }
    
    /**
     * Get mail edit form
     *
     * @return object Model_Form_Admin_Mail_Edit
     */
    protected function _getEditForm()
    {
        return new Model_Mail_Form_Edit();
    }
}

