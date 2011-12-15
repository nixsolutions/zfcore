<?php
/**
 * Comments_AddController for Comments module
 *
 * @category   Application
 * @package    Blog
 * @subpackage Controller
 *
 * @version  $Id: AddController.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_AddController extends Core_Controller_Action
{
    public function indexAction()
    {
        $commentsTable = new Comments_Model_Comment_Table();
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $form = new Comments_Model_Comment_Form_Create();
            
            if ($this->getRequest()->isPost()
                && $form->isValid($this->getRequest()->getParams())
            ) {

                $row = $commentsTable->createRow($form->getValues());
                $row->save();
                
                $this->_helper->flashMessenger->addMessage(
                    'Comment added successfully'
                );
                
                $this->_redirect($form->getElement('returnUrl')->getValue());
            }
            
            $this->view->form = $form;
            
            $this->_forward('index', 'index', 'comments');
        }
    }
}