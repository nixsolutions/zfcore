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
        $aliasManager = new Comments_Model_CommentAlias_Manager();
        $commentsTable = new Comments_Model_Comment_Table();
        
        $alias = $aliasManager->getByAlias($this->getRequest()->getParam('alias'));
        $key = $this->getRequest()->getParam('key');
        
        if (!$alias) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        
        // user should have an identity
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $form = new Comments_Model_Comment_Form_Create();
            
            // check alias item options
            
            // check that required status for key
            if ($alias->isKeyRequired()) {
                $form->setKey($key);
            }
            
            // check need and visibility of title
            if (!$alias->isTitleDisplayed()) {
                $form->removeTitleElement();
            }
            
            // validate form by the POST request pararm
            if ($this->getRequest()->isPost()
                && $form->isValid($this->getRequest()->getParams())
            ) {
                // creating new comments row
                $row = $commentsTable->createRow($form->getValues());
                $row->aliasId = $alias->id;
                $row->status = Comments_Model_Comment::STATUS_ACTIVE;
                
                // change status to "review" when pre-moderation required
                if ($alias->isPreModerationRequired()) {
                    $row->status = Comments_Model_Comment::STATUS_REVIEW;
                }
                
                $row->save();
                
                // display the flash message according to the comment status
                switch ($row->status) {
                    case Comments_Model_Comment::STATUS_REVIEW:
                        $this->_helper->flashMessenger->addMessage(
                            'Comment added successfully and awaiting pre-moderation.'
                        );
                        break;
                    default:
                        $this->_helper->flashMessenger->addMessage(
                            'Comment added successfully'
                        );
                        break;
                }
                
                // redirect to the URL that was setted to the form element
                $this->_redirect($form->getValue('returnUrl'));
            }
            
            // set the view variables
            $this->view->user = $this->view->user();
            $this->view->form = $form;
        }
    }
}